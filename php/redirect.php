<?php

if (!(isset($_SESSION))) {
    session_start();
}
/* Include files */
include 'general.php';  // Generic functions

/* Specify the locations of the include files for each gateway */
global $gateways;
$gateways = array();
$gateways["Standard"] = 'Standard.php';
$gateways["DataCash"] = 'DataCashAPI.php';
$gateways["CyberSource"] = 'CyberSourceSO.php';
$gateways["SagePay (API)"] = 'SagePayAPI.php';
$gateways["Authipay (API)"] = 'authipay.php';
$gateways["Authipay (HSS)"] = 'AuthipayHSS.php';
/* Start the session */


$input = $_REQUEST;
storeRequest($input);
$_SESSION["ALERT"] = "";
$_SESSION['GATEWAYS'] = urlencode(serialize($gateways));

switch ($_SESSION['STAGE']) {

    /*     * * * * Express Checkout * * * * */
    case "EC_Start":
        $input = uploadLogo($input);
        $_SESSION['GATEWAY'] = $input['GATEWAY'];
        $_SESSION['COMMIT'] = $input['COMMIT'];
        $paramArray = sendMessage('setEC', $input);
        echo 'ACK: ' .$_SESSION['ACK'];
        $url = "https://localhost/Gateways/response.php";
        $_SESSION['STAGE'] = "set";
        break;
    case "set":
        $_SESSION['STAGE'] = "get";
        if ($_SESSION['ACK'] == "Success") {
            switch ($input['DEVICE']) {
                case "Mobile":
                    $cmd = "_express-checkout-mobile";
                    break;
                default:
                    $cmd = "_express-checkout";
                    break;
            }

            //$url = "https://www.paypal.com/cgi-bin/webscr?";
            if ($_SESSION["GATEWAY"] == "SagePay (API)") {
                $url = $_SESSION["URL"];
            } else {
                $paramArray = array("cmd" => "_express-checkout", "token" => $_SESSION['TOKEN']/*,  "useraction" => "commit" */);
                
                if ($_SESSION['COMMIT'] == "Yes") {
                    $paramArray["useraction"] = "commit";
                }
                $url = "https://www.sandbox.paypal.com/uk/cgi-bin/webscr?";
                //$paramArray = array("token" => $_SESSION['TOKEN']);
                //$url = "https://www.sandbox.paypal.com/checkoutnow?";
            }

            $method = "get";
        } else {

            $url = "https://localhost/Gateways/index.php";
        }
        break;
    case "get":
        $_SESSION['STAGE'] = "do";
        $paramArray = sendMessage('getECD', $input);
        $method = "post";
        $url = "https://localhost/Gateways/response.php";
        break;
    case "do":
        $_SESSION['STAGE'] = "EC_Complete";
        $input = unserialize(urldecode($_SESSION['RESPONSE']));
        $_SESSION['EMAIL'] = $input['EMAIL'];
        $paramArray = sendMessage('doEC', $input);
        $url = "https://localhost/Gateways/response.php";
        break;
    case "EC_Complete":
        $_SESSION['STAGE'] = "EC_Start";
        $url = "https://localhost/Gateways/index.php";
        break;

    /*     * * * * DoAuthorization * * * * */
    case "auth":
        $_SESSION['GATEWAY'] = $input['GATEWAY'];
        sendMessage('auth', $input);
        break;
    /*     * * * * MassPay * * * * */
    case "MP_Start":
        $_SESSION['GATEWAY'] = $input['GATEWAY'];
        sendMessage('masspay', $input);
        $url = "https://localhost/Gateways/response.php";
        break;
}
// Re-direct user
//var_dump($input);
redirect($url, $method, $paramArray);

function sendMessage($method, $input) {
    global $gateways;
    // Make sure something has been posted
    if (!empty($input)) {
        /* Choose gateway and get repsponse */
        // Get the correct gateway object from the array
        include $gateways[$_SESSION['GATEWAY']];
        getResponse($input, $gateway->getDetail($method)); // Get response from the gateway
        $response = unserialize(urldecode($_SESSION['RESPONSE']));
    }
}

function storeRequest($input) {
    $stage = $_SESSION['STAGE'];
    $accessToken = $_SESSION["AccessToken"];
    if ($stage == "EC_Start") {
        session_unset();
        $_SESSION['STAGE'] = "EC_Start";
    }
    foreach ($input as $key => $value) {
        $_SESSION[$key] = urlencode($value);
    }
    if (isset($accessToken)) {
        $_SESSION["AccessToken"] = $accessToken;
    }
}

function uploadLogo($input) {
    //var_dump($input);
    if (isset($input['LOGOIMG'])) {
        $var = 'LOGOIMG';
    } else {
        $var = 'HDRIMG';
    }
    //if they DID upload a file...
    if ($input[$var] == "See uploaded") {
        $filename = time() . "." . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $target = "img/" . $filename;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
            echo "OK!"; 
            unset($_FILES['logo']);
            $input[$var] = "https://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/$target";
            $_SESSION[$var] = "https://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/$target";
        }
    }
    return $input;
}

?>