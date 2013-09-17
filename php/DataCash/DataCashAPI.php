<?php
    $gateway = new Gateway("set_express_checkout", "get_express_checkout_details", "do_express_checkout_payment", "do_authorization", array('REFERENCE'));
	error_reporting(E_ALL ^ E_NOTICE);
	include("DataCash.php");
	global $config;
	$config = new DataCash_Document();
	$config->readDocumentFromFile("C:/wamp/www/Gateways/php//DataCash/php-transaction.conf");
	
	function errorResponse($response) {
		$xml = new DataCash_Document();
		//$xml->readDocumentFromString($response);
		echo "<pre>". getHtmlDocument($response) . "</pre>";
		$error = $response->getRootElement()->getElement("PayPalTxn")->getElement("Errors")->getElement("Error");
		$errorMessage = "<p><strong>Error (";
		$errorMessage .= $error->getElement("error_code")->getText();
		$errorMessage .= ") </p></strong>";
		$errorMessage .=  "<p>" . $error->getElement("short_message")->getText(). "</p>";
		$errorMessage .= "<p>" . $error->getElement("long_message")->getText() . "</p>";
		$errorMessage .= "<p> See <a href=\"https://developer.paypal.com/webapps/developer/docs/classic/api/errorcodes/\"> PayPal Error Codes</a> for more details </p> </div>";
		$errorMessage = rawurlencode($errorMessage);
		$error = array("Error" => $errorMessage);
		return $error;
	}
	
	
	function getResponse($input, $method) {
        // Security Parameters		
		global $config;
		$send_doc = new DataCash_Document("Request");
		$send_doc->set( "Request.Authentication.client", "99004655" );
		$send_doc->set( "Request.Authentication.password", "FpA2Yumq" );
		$send_doc->set( "Request.Transaction.TxnDetails.merchantreference", time());
		if ($method != "get_express_checkout_details") {
			$send_doc->set( "Request.Transaction.PayPalTxn.email" , "accept@example.com");
		} else {
			$send_doc->set( "Request.Transaction.PayPalTxn.reference" , $input['REFERENCE']);	
		}
		$send_doc->set( "Request.Transaction.PayPalTxn.method", $method);
		if ($method == "set_express_checkout") { convert($input, $send_doc); }
		//echo "<pre>".getHtmlDocument($send_doc)."</pre>";
		$agent = new DataCash_SSLAgent( $config );
		$success = $agent->send( $send_doc );
		$response_doc = $agent->getResponseDocument();
		//echo "<pre>".getHtmlDocument($response_doc)."</pre>";
		// Decode API response
        $decoded = decodeResponse($response_doc, $method);
        return $decoded;
    }
	function convert($inp, $send_doc) {
		global $stdToDC;
		$cart = -1;
		$item = -1;
		$titles = array_keys($inp);
		$numberOfParams = sizeOf($titles);
		$basket = false;
		$new = array();
		for ($inc = 0; $inc < $numberOfParams; $inc ++) {
			$cur = $titles[$inc];
			$newTitle = $stdToDC[$cur];
			//echo "Title: " . $cur . " Value: " . $newTitle . "<br>";
			switch($newTitle) {
				case "":
					break;
				case "Total":
					$send_doc->set( "Request.Transaction.TxnDetails.amount", $inp[$cur] , array('currency' => $inp["PAYMENTREQUEST_0_CURRENCYCODE"]));
					break;
				case "Currency":
					break;
				case "Border":
					$cart = customiseCart("bordercolor",$inp[$cur],$send_doc, $cart);
					break;
				case "Logo":
					$cart = customiseCart("img", $inp[$cur], $send_doc, $cart);
					break;
				case "Name":
					$item = updateItem("name",$inp[$cur],$send_doc, $item);
					break;
				case "Desc":
					break;
				case "Qty":
					$item = updateItem("quantity",$inp[$cur],$send_doc, $item);
					break;
				case "Amt":
					$item = updateItem("amount",$inp[$cur],$send_doc, $item);
					break;
				default:
					$send_doc->set($newTitle, $inp[$cur]);
					break;
				
			}
		}
		$items = new Datacash_Element("Items");
		$items->addElement($item);
		$send_doc->getRootElement()->getElement("Transaction")->getElement("PayPalTxn")->addElement($items);
		$send_doc->getRootElement()->getElement("Transaction")->getElement("PayPalTxn")->addElement($cart);
		//echo "<pre>" . getHtmlDocument($send_doc) . "</pre>";
		//if ($basket) {$new = array_merge($new, array("BasketXML"=> $xml->saveXML())); }
	}

	function decodeResponse ($response, $method) {
		$newResponse = array();
		$newResponse['METHOD'] = $method; 
		$newResponse['ACK'] =  $response->get("Response.PayPalTxn.ack");
		$newResponse['TOKEN'] = ""; //$response->get("Response.PayPalTxn.token");
		$newResponse['REFERENCE'] = $response->get("Response.datacash_reference");
		$newResponse['MESSAGE'] = getHTMLDocument($response);
		return $newResponse;
		
	}
	
	function updateItem($elmt, $val, $xml, $item) {
		if ($item == -1) { 
			$item = new Datacash_Element("Item");
			$id = new DataCash_Attribute("id","0");
			$item->addAttribute($id);
		}
		$xmlElmt = new DataCash_Element($elmt);
		$xmlElmt->setText($val);
		$item->addElement($xmlElmt);
		return $item;
	}
	
	function customiseCart($name, $val, $xml, $cart) {
		if ($cart == -1) { 
			$cart = new DataCash_Element("header_style");
			$attr = new DataCash_Attribute($name,$val);
			$cart->addAttribute($attr); 
		}
		else { 
			$attr = new DataCash_Attribute($name,$val);
			$cart->addAttribute($attr); 
		}
		return $cart;
	}
	
	function getHtmlDocument ( $xmldoc ) {
    	$stringDocument = $xmldoc->getDocument();
    	$stringDocument = preg_replace( '/</', '&lt;', $stringDocument );
    	$stringDocument = preg_replace( '/>/', '&gt;', $stringDocument );
    	return $stringDocument;
	}
	global $stdToDC;
	// Set up conversion array
	$stdToDC["METHOD"] = "Request.Transaction.PayPalTxn.method";
	$stdToDC["PAYMENTREQUEST_0_AMT"] = "Total";
	$stdToDC['PAYMENTREQUEST_0_CURRENCYCODE'] = "Currency";
	$stdToDC['MAXAMT'] = "Request.Transaction.PayPalTxn.max_amount";
	$stdToDC['RETURNURL'] = "Request.Transaction.PayPalTxn.return_url";
	$stdToDC['CANCELURL'] = "Request.Transaction.PayPalTxn.cancel_url";
	$stdToDC['REQCONFIRMSHIPPING'] = "Request.Transaction.PayPalTxn.req_confirmed_shipping";
	$stdToDC['NOSHIPPING'] = "Request.Transaction.PayPalTxn.no_shipping";
	//$stdToDC['ALLOWNOTE'] = "Request.Transaction.PayPalTxn.return_url";
	$stdToDC['ADDROVERRIDE'] = "Request.Transaction.PayPalTxn.override_address";
	$stdToDC['LOCALECODE'] = "Request.Transaction.PayPalTxn.localecode";
	$stdToDC['CARTBORDERCOLOR'] = "Border";
	$stdToDC['LOGOIMG'] = "Logo";
	$stdToDC['SOLUTIONTYPE'] = "Request.Transaction.PayPalTxn.solution_type";
	// Item Details
	$stdToDC['L_PAYMENTREQUEST_0_NAME0'] = "Name";
	$stdToDC['L_PAYMENTREQUEST_0_DESC0'] = "Desc";
	$stdToDC['L_PAYMENTREQUEST_0_QTY0'] = "Qty";
	$stdToDC['L_PAYMENTREQUEST_0_AMT0'] = "Amt";	
	// Address Details
	$stdToDC['PAYMENTREQUEST_0_SHIPTONAME'] = "Request.Transaction.PayPalTxn.ShippingAddress.name";
	$stdToDC['PAYMENTREQUEST_0_SHIPTOSTREET'] = "Request.Transaction.PayPalTxn.ShippingAddress.street_address1";
	$stdToDC['PAYMENTREQUEST_0_SHIPTOSTREET2'] = "Request.Transaction.PayPalTxn.ShippingAddress.street_address2";
	$stdToDC['PAYMENTREQUEST_0_SHIPTOCITY'] = "Request.Transaction.PayPalTxn.ShippingAddress.city";
	$stdToDC['PAYMENTREQUEST_0_SHIPTOSTATE'] = "Request.Transaction.PayPalTxn.ShippingAddress.region";
	$stdToDC['PAYMENTREQUEST_0_SHIPTOZIP'] = "Request.Transaction.PayPalTxn.ShippingAddress.postcode";
	$stdToDC['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = "Request.Transaction.PayPalTxn.ShippingAddress.country_code";
	$stdToDC['PAYMENTREQUEST_0_SHIPTOPHONENUM'] = "Request.Transaction.PayPalTxn.ShippingAddress.telephone_number";
	// Totals
	$stdToDC['PAYMENTREQUEST_0_ITEMAMT'] = "Request.Transaction.PayPalTxn.item_total";
	$stdToDC['PAYMENTREQUEST_0_SHIPPINGAMT'] = "Request.Transaction.PayPalTxn.shipping_total";
	$stdToDC['PAYMENTREQUEST_0_HANDLINGAMT'] = "Request.Transaction.PayPalTxn.handling_total";
	$stdToDC['PAYMENTREQUEST_0_TAXAMT'] = "Request.Transaction.PayPalTxn.tax_total";
	// Other
	$stdToDC['PAYMENTREQUEST_0_DESC'] = "Request.Transaction.PayPalTxn.description";
	$stdToDC['CUSTOM'] = "Request.Transaction.PayPalTxn.custom";
	$stdToDC['PAYMENTREQUEST_0_INVNUM'] = "Request.Transaction.PayPalTxn.invnum";
		

?>