<?php
    include 'inc_classes.php';
    $gateway = new Gateway("payPalEcSetService", "payPalEcGetDetailsService", "Sale", "payPalAuthorizationService",array('requestToken', 'requestID'));
	function errorResponse($response) {
		$errorMessage = "<p><strong>Error (";
		$errorMessage .= $response['errorCode'];
		$errorMessage .= ") </p></strong>";
		$errorMessage .=  "<p>" . $response['L_SHORTMESSAGE0'] . "</p>";
		$errorMessage .= "<p>" . $response['L_LONGMESSAGE0']. "</p>";
		$errorMessage .= "<p> See <a href=\"https://developer.paypal.com/webapps/developer/docs/classic/api/errorcodes/\"> PayPal Error Codes</a> for more details </p> </div>";
		$errorMessage = rawurlencode($errorMessage);
		$error = array("Error" => $errorMessage);
		return $error;
	}
	
	function getResponse($input, $method) {
        // Security Parameters
		// Confifuration
		$config = cybs_load_config( 'C:/wamp/www/Gateways/php/cybs.ini' );
		
		// Header
		$request = array();
		$request[$method . '_run']="true";
		$request['merchantID']="paypaltest_cs";
		$request['merchantReferenceCode']="W945E2KCQVPM2";
		//$request['payPalEcSetService_paypalMerchantId']="test-cybersource@paypal.com";
		
		// General Parameters
		switch ($method) {
			case "payPalEcSetService": 
				$data = convertForSet($input);
				$_SESSION['AMT'] = $data['purchaseTotals_grandTotalAmount'];
				$_SESSION['CURRENCY'] = $data['purchaseTotals_currency'];
				$request = array_merge($request, $data);
				break;
			case "payPalEcGetDetailsService":
				$request['payPalEcGetDetailsService_paypalToken'] = $_SESSION['TOKEN'];
				$request['payPalEcGetDetailsService_paypalEcSetRequestToken'] = $_SESSION['REFERENCE0'];
				$request['payPalEcGetDetailsService_paypalEcSetRequestID'] = $_SESSION['REFERENCE1'];
				break;
			case "Sale":
			    $request['payPalEcDoPaymentService_run'] = "true";
				$request['payPalEcDoPaymentService_paypalEcSetRequestToken'] = $_SESSION['REFERENCE0'];
				$request['payPalEcDoPaymentService_paypalEcSetRequestID'] = $_SESSION['REFERENCE1'];
				$request['purchaseTotals_grandTotalAmount'] = $_SESSION['AMT'];
				$request['purchaseTotals_currency'] = $_SESSION['CURRENCY'];
				$request['payPalEcDoPaymentService_paypalToken'] = $input['payPalEcGetDetailsReply_paypalToken'];
				$request['payPalEcDoPaymentService_paypalPayerId'] = $input['payPalEcGetDetailsReply_payerId'];
				$request['payPalEcDoPaymentService_paypalCustomerEmail'] = $input['payPalEcGetDetailsReply_payer'];
				$request['payPalDoCaptureService_run'] = "true";
				$request['payPalDoCaptureService_completeType'] = "Complete";
				break;
			case "payPalAuthorizationService": 
				$data = convertAuth($input);
				$request = array_merge($request, $data);
				break;
			default:
				$request = array_merge($request, $input);
				break; 
		}
		
		//$request['merchantAccount'] = "test-cybersource@paypal.com";
		
				
		// Talk to API Server
        $response = array();
		$status = cybs_run_transaction( $config, $request, $response );
		if (($_SESSION['ACTION'] == "Sale") && ($method == "payPalEcDoPaymentService")) {
		   
		}
		// Decode API response
		switch ($response['decision']) {
			case"ACCEPT":
				$ack = "Success";
				break;
			default:
				$ack = "Fail";
				break;	
		}
		$_SESSION['ACK'] =  $ack;
		$_SESSION['TOKEN'] = $response['payPalEcSetReply_paypalToken'];
		$_SESSION['METHOD'] = $method;
		$_SESSION['APITYPE'] = "NVP";
		$_SESSION['REQUEST'] = urlencode(serialize($request));
		$_SESSION['RESPONSE'] = urlencode(serialize($response));
    }
	function convertForSet($inp) {
		$request = array();
		global $set;
		$cart = -1;
		$item = -1;
		$name=0;$desc=0;$num=0;$qty=0;$amt=0;
		$titles = array_keys($inp);
		$numberOfParams = sizeOf($titles);
		$basket = false;
		$new = array();
		for ($inc = 0; $inc < $numberOfParams; $inc ++) {
			$cur = $titles[$inc];
			$newTitle = $set[$cur];
			if (preg_match('/^L_PAYMENTREQUEST_0_NAME/', $cur)) {
				$request['item_' . $name . '_productName'] = $inp[$cur];
				$name ++;
			} elseif (preg_match('/^L_PAYMENTREQUEST_0_DESC/', $cur)) {
				$request['item_' . $desc . '_productDescription'] = $inp[$cur];
				$desc ++;
			} elseif (preg_match('/^L_PAYMENTREQUEST_0_NUMBER/', $cur)) {
				$request['item_' . $num . '_productSKU'] = $inp[$cur];
				$num ++;
			} elseif (preg_match('/^L_PAYMENTREQUEST_0_QTY/', $cur)) {
				$request['item_' . $qty . '_quantity'] = $inp[$cur];
				$qty ++;
			}elseif (preg_match('/^L_PAYMENTREQUEST_0_AMT/', $cur)) {
				$request['item_' . $amt . '_unitPrice'] = $inp[$cur];
				$amt ++;
			}
			
			echo "Title: " . $cur . " Value: " . $newTitle . "<br>";
			switch($newTitle) {
				case "":
					break;
				case "Shipping Name":
					$names = explode(" ",$inp[$cur]);
					$request['shipTo_firstName'] = $names[0];
					$request['shipTo_lastName'] = $names[sizeOf($names)-1];
					break;
				default:
					$request[$newTitle] = $inp[$cur];
					break;
			}
		}
		return $request;
	}
	function convertAuth($inp) {
		$request = array();
		global $auth;
		$titles = array_keys($inp);
		$numberOfParams = sizeOf($titles);
		for ($inc = 0; $inc < $numberOfParams; $inc ++) {
			$cur = $titles[$inc];
			$newTitle = $set[$cur];
			switch($newTitle) {
				case "":
					break;
				default:
					$request[$newTitle] = $inp[$cur];
					break;
			}
		}
	}
	
	function decode($serialised) {
	    $array = unserialize(urldecode($serialised));
	    ksort($array);
	    $titles = array_keys($array);
	    $data="\n";
	    for($inc = 0; $inc < count($array); $inc ++){
	        $current = $titles[$inc];
	         
	        if ((!empty($array[$current])) && ($array[$current] != "")) {
	            $data .= "[" . $current . "] = " . $array[$current] . "\n";
	        }
	    }
	    return trim($data);
	}
		
	
	global $set;
	global $do;
	global $auth;
	/* Set up conversion array for SetExpressCheckout */
	$set['PAYMENTREQUEST_0_AMT'] = "purchaseTotals_grandTotalAmount";
	$set['PAYMENTREQUEST_0_CURRENCYCODE'] = "purchaseTotals_currency";
	$set['MAXAMT'] = "payPalEcSetService_paypalMaxamt";
	$set['RETURNURL'] = "payPalEcSetService_paypalReturn";
	$set['CANCELURL'] = "payPalEcSetService_paypalCancelReturn";
	$set['REQCONFIRMSHIPPING'] = "payPalEcSetService_paypalReqconfirmshipping";
	$set['NOSHIPPING'] = "payPalEcSetService_paypalNoshipping";
	//$set['ALLOWNOTE'] = "Request.Transaction.PayPalTxn.return_url";
	$set['ADDROVERRIDE'] = "payPalEcSetService_paypalAddressOverride";
	$set['LOCALECODE'] = "payPalEcSetService_paypalLc";
	$set['CARTBORDERCOLOR'] = "payPalEcSetService_paypalPayflowcolor";
	$set['LOGOIMG'] = "payPalEcSetService_paypalLogoimg";
	$set['HDRIMG'] = "payPalEcSetService_paypalHdrimg";
	$set['SOLUTIONTYPE'] = "Request.Transaction.PayPalTxn.solution_type"; // NO SOLUTION TYPE
	// Item Details
	// Address Details
	$set['PAYMENTREQUEST_0_SHIPTONAME'] = "Shipping Name";
	$set['PAYMENTREQUEST_0_SHIPTOSTREET'] = "shipTo_street1";
	$set['PAYMENTREQUEST_0_SHIPTOSTREET2'] = "shipTo_street2";
	$set['PAYMENTREQUEST_0_SHIPTOCITY'] = "shipTo_city";
	$set['PAYMENTREQUEST_0_SHIPTOSTATE'] = "shipTo_state";
	$set['PAYMENTREQUEST_0_SHIPTOZIP'] = "shipTo_postalCode";
	$set['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = "shipTo_country";
	$set['PAYMENTREQUEST_0_SHIPTOPHONENUM'] = "shipTo_phoneNumber";
	// Totals
//	$set['PAYMENTREQUEST_0_ITEMAMT'] = "Request.Transaction.PayPalTxn.item_total";
//	$set['PAYMENTREQUEST_0_SHIPPINGAMT'] = "Request.Transaction.PayPalTxn.shipping_total";
//	$set['PAYMENTREQUEST_0_HANDLINGAMT'] = "Request.Transaction.PayPalTxn.handling_total";
//	$set['PAYMENTREQUEST_0_TAXAMT'] = "Request.Transaction.PayPalTxn.tax_total";
	// Other
	$set['PAYMENTREQUEST_0_DESC'] = "payPalEcSetService_paypalDesc";
//	$set['CUSTOM'] = "Request.Transaction.PayPalTxn.custom";
	$set['PAYMENTREQUEST_0_INVNUM'] = "payPalEcSetService_invoiceNumber";
	
	/* Set up conversion array for SetExpressCheckout */
	
	
	
	/* Set up conversion array for DoAuth */
	$auth['TRANSACTIONID'] = "payPalAuthorizationService_paypalOrderId";
	$auth['AMT'] = "purchaseTotals_grandTotalAmount";
	$auth['CURRENCYCODE'] = "purchaseTotals_currency";
	
	
?>