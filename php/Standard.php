<?php

include 'inc_classes.php';
$gateway = new Gateway("SetExpressCheckout", "GetExpressCheckoutDetails", "DoExpressCheckoutPayment", "DoAuthorization", "MassPay",null);

function getResponse($input, $method) {
    // Security Parameters
    /* US Account 
      $header["user"] = "ebird-facilitator_api1.paypal.com";
      $header["pwd"] = "1370865055";
      $header["signature"] = "AtwuHRgEjbbGxX5TqJomjrbXFY-TALdVUvP9oTSkCOQ9vqqf.ddgoUaf";
      $end = "https://api-3t.sandbox.paypal.com/nvp";
    // */
    /* UK MAM Sandbox Account  
    $header["user"] = "child2_api1.mam.com";
    $header["pwd"] = "1387368528";
    $header["signature"] = "A30pmuqi2qUvp-q6fIhtFdXE9iNHA6p6yXFHl-dtjp3AxGBfedMrH1OU";
    $end = "https://api-3t.sandbox.paypal.com/nvp";
    // */
    /* UK Sandbox Account   */
    $header["USER"] = "ebird-gb_api1.gmail.com";
    $header["PWD"] = "1372069232";
    $header["SIGNATURE"] = "AkqLVlV0JbJqDZ0BEgyqFyxsB6xAASqsDHNG0NL5bPKmDCC.Qsjdu4j4";
    $end = "https://api-3t.sandbox.paypal.com/nvp";
    // */
    /* UK Live Account 
    $header["user"] = "ebirdgb_api1.gmail.com";
    $header["pwd"] = "29CLNUB8HNRTYTRV";
    $header["signature"] = "AfwrRYCymWudAJQI2HfVtpmnVowwAD-aylmy8L.GGxwxUmswMZhkQLM0";
    $end = "https://api-3t.paypal.com/nvp";
    //  */
    // General Parameters
    $header["VERSION"] = "106.0";
    $header["METHOD"] = $method;

    $request = array_merge($header, $input);
    /*if (isset($_SESSION["AccessToken"])) {
        $request["IDENTITYACCESSTOKEN"] = $_SESSION["AccessToken"];
    }*/
    
    //$request["BUYEREMAILOPTINENABLE"] = "1";
    /*
    $request["PAYMENTREQUEST_0_PAYMENTREQUESTID"] = "123456948";
    $request["PAYMENTREQUEST_1_PAYMENTREQUESTID"] = "123456947";
    $request["PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID"] = "ebird-gb@gmail.com";
    $request["PAYMENTREQUEST_1_SELLERPAYPALACCOUNTID"] = "ebird-gb2@gmail.com";
    /*
    $request["PAYMENTREQUEST_0_PAYMENTACTION"] = "Order";
    $request["PAYMENTREQUEST_1_PAYMENTACTION"] = "Order";*/
    // Convert array to correctly formatted string
   // $request["PAYMENTREQUEST_0_PAYMENTACTION"] = "Authorization";
    $data = setUpParams($request);
    
    // Specify end point
    

    //$request = array_merge($request, array("ENDPOINT" => $end));

    // Talk to API Server
   $ch = curl_init();    
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    
    //turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    //curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
    
    curl_setopt($ch, CURLOPT_URL, $end);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSLKEY, "privkey.pem");
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, "PEM");
echo 0;
    $response = curl_exec($ch);
    echo 1;
    $curlerr = curl_error($ch);
    curl_close($ch);
    print_r($curlerr);
   // echo $request . "\n";
   // echo $response;
    //$response = hash_call("SetExpressCheckout",$request);
    // Decode API response
    $resp = explode("=", $response);
    $titles = array();
    $values = array();
    $titles[0] = $resp[0];
    for ($inc = 1; $inc < (count($resp) - 1); $inc ++) {
        $temp = explode("&",$resp[$inc]);
        $values[$inc - 1] = $temp[0];
        $titles[$inc] = $temp[1];
    }
    $values[count($titles)-1] = $resp[count($resp)-1];
    
    $reply = array();
    for ($inc = 0; $inc < count($titles); $inc ++) {
        $reply[$titles[$inc]] = urldecode($values[$inc]);
    }
    
    if (!isset($reply['METHOD'])) {
        $reply['METHOD'] = $method;
    }

    $_SESSION['ACK'] = $reply['ACK'];
    $_SESSION['TOKEN'] = $reply['TOKEN'];
    $_SESSION['ACTION'] = $request['PAYMENTREQUEST_0_PAYMENTACTION'];
    $_SESSION['METHOD'] = $method;
    $_SESSION['APITYPE'] = "NVP";
    $_SESSION['REQUEST'] = urlencode(serialize($request));
    $_SESSION['RESPONSE'] = urlencode(serialize($reply));


    // preg_replace("/&/","\n",urldecode(setUpParams($finalArray)));
}

function getToken($input, $method) {
    $resp = getStdResponse($input, $method);
    $token = $resp['TOKEN'];
    return $token;
}

function decode($serialised) {
    $array = unserialize(urldecode($serialised));
    $titles = array_keys($array);
    $data = "";
    for ($inc = 0; $inc < count($array); $inc ++) {
        $current = $titles[$inc];
        if ($array[$current] != "") {
            $data .= "[" . $current . "] = " . $array[$current] . "\n";
        }
    }
    return trim($data);
}

?>