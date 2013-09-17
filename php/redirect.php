<?php
	/* Include files */
	include 'general.php';		// Generic functions
	
	/* Specify the locations of the include files for each gateway */
	global $gateways;
	$gateways = array();
	$gateways["Standard"] = 'Standard.php';
        $gateways["DataCash"] = '/DataCash/DataCashAPI.php';
        $gateways["CyberSource"] = 'CyberSourceSO.php';
        $gateways["SagePay (API)"] = 'SagePayAPI.php';
        $gateways["Authipay (API)"] = 'authipay.php';
        $gateways["Authipay (HSS)"] = 'AuthipayHSS.php';
	/* Start the session */
	session_start();
	$_SESSION['GATEWAYS'] = serialize($gateways);
//	if (isset($_SESSION['GATEWAY'])) { include $gateways[$_SESSION['GATEWAY']]; }
	
	switch ($_SESSION['STAGE']) {
		
		/* * * * * Express Checkout * * * * */
		case "EC_Start":
			$_SESSION['GATEWAY'] = $_POST['GATEWAY'];
                        $_SESSION['COMMIT'] = $_POST['COMMIT'];
			//$_POST['PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID'] = "ebird@paypal.com";
			//$_POST['CHANNELTYPE'] = "Merchant";
			$paramArray = sendMessage(setEC, $_POST);
			$url = "https://localhost/Gateways/response.php";
			$_SESSION['STAGE'] = "set";
			break;
		case "set":
			$_SESSION['STAGE'] = "get";
			if ($_SESSION['ACK'] == "Success") {
				switch($_POST['DEVICE']) {
					case "Mobile":
						$cmd = "_express-checkout-mobile";
						break;
					default:
						$cmd = "_express-checkout";
						break;
				}
				//$paramArray = array("cmd" => "_express-checkout-mobile", "token" => $_SESSION['TOKEN'], /*"useraction" => "commit"*/);
                                $paramArray = array("token" => $_SESSION['TOKEN']);
                                if ($_SESSION['COMMIT']=="Yes") { $paramArray["useraction"] = "commit"; }
				//$url = "https://www.sandbox.paypal.com/cgi-bin/webscr?";
				//$url = "https://www.sandbox.paypal.com/uk/cgi-bin/merchantpaymentweb?";
                                $url = "https://www.sandbox.paypal.com/checkoutnow?";
				$method = "get";
			} else {
				$url = "https://localhost/Gateways/index.php";
			}
			break;
		case "get":
			$_SESSION['STAGE'] = "do";
			$paramArray = sendMessage(getECD, $_GET);
			$method = "post";
			$url = "https://localhost/Gateways/response.php";
			break;
		case "do":
			//$_SESSION['STAGE'] = "EC_Complete";
			$input = unserialize(urldecode($_SESSION['RESPONSE']));
			$_SESSION['EMAIL'] = $input['EMAIL'];
			$paramArray = sendMessage(doEC, $input);
			$url = "https://localhost/Gateways/response.php";
			break;
		case "EC_Complete":
			$_SESSION['STAGE'] = "EC_Start";
			$url = "https://localhost/Gateways/index.php";
			break;
		
		/* 		DoAuthorization
		* * * * * * * * * * * * * * * */
		case "auth":
			$_SESSION['GATEWAY'] = $_POST['GATEWAY'];
			sendMessage(auth, $_POST);
	}
	// Re-direct user to PayPal
 	//redirect($url,$method, $paramArray);
	
	function sendMessage ($method, $input) {
		global $gateways;
		// Make sure something has been posted
		if (!empty($input)) {		
			/* Choose gateway and get repsponse */
			// Get the correct gateway object from the array
		    include $gateways[$_SESSION['GATEWAY']];
			$reference = $gateway->getDetail(references);		// Get the array containing the references the gateway uses
			getResponse($input, $gateway->getDetail($method));	// Get response from the gateway
			$response = unserialize(urldecode($_SESSION['RESPONSE']));
			if ($method == setEC) {
				for ($inc = 0; $inc < sizeOf($reference) ; $inc ++) {
					$_SESSION['REFERENCE' . $inc] = $response[$reference[$inc]];
				}
			}
			
		}
	}
?>