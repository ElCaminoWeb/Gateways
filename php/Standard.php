<?php

include 'inc_classes.php';
$gateway = new Gateway("SetExpressCheckout", "GetExpressCheckoutDetails", "DoExpressCheckoutPayment", "DoAuthorization", null);

function getResponse($input, $method) {
    // Security Parameters
    /* US Account
      $header = array("user" => "ebird-facilitator_api1.paypal.com");
      $header = array_merge($header, array("pwd" => "1370865055"));
      $header = array_merge($header, array("signature" => "AtwuHRgEjbbGxX5TqJomjrbXFY-TALdVUvP9oTSkCOQ9vqqf.ddgoUaf"));
     */
    /* UK Sandbox Account */
    $header["user"] = "ebird-gb_api1.gmail.com";
    $header["pwd"] = "1372069232";
    $header["signature"] = "AkqLVlV0JbJqDZ0BEgyqFyxsB6xAASqsDHNG0NL5bPKmDCC.Qsjdu4j4";
    $end = "https://api-3t.sandbox.paypal.com/nvp";
    // */
    /* UK Live Account 
    $data["user"] = "ebirdgb_api1.gmail.com";
    $data["pwd"] = "29CLNUB8HNRTYTRV";
    $data["signature"] = "AfwrRYCymWudAJQI2HfVtpmnVowwAD-aylmy8L.GGxwxUmswMZhkQLM0";
    $end = "https://api-3t.paypal.com/nvp";
    //  */
    // General Parameters
    $header["version"] = "91.0";
    $header["method"] = $method;

    $request = array_merge($header, $input);
    $request["PAYMENTREQUEST_0_PAYMENTREQUESTID"] = "123456948";
    $request["PAYMENTREQUEST_1_PAYMENTREQUESTID"] = "123456947";
    $request["PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID"] = "ebird-gb@gmail.com";
    $request["PAYMENTREQUEST_1_SELLERPAYPALACCOUNTID"] = "ebird-gb2@gmail.com";
    $request["PAYMENTREQUEST_0_PAYMENTACTION"] = "Order";
    $request["PAYMENTREQUEST_1_PAYMENTACTION"] = "Order";
    // Convert array to correctly formatted string
    $data = setUpParams($request);

    // Specify end point
    

    //$request = array_merge($request, array("ENDPOINT" => $end));

    // Talk to API Server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $end);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSLKEY, "privkey.pem");
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, "PEM");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    $curlerr = curl_error($ch);
    curl_close($ch);

    // Decode API response
    $reponse = stripslashes($response);
    $splitLen = stripos($response, "charset=utf-8") + 17;
    $newResp = str_split($response, $splitLen);
    $array2 = $newResp[1];
    $len = count($newResp);
    for ($inc = 1; $inc < ($len - 1); $inc ++) {
        $array2 .= $newResp[$inc + 1];
    }
    $array = explode("&", $array2);
    $arrayLen = count($array);
    for ($inc = 0; $inc < $arrayLen; $inc ++) {
        $newArray[$inc] = explode("=", $array[$inc]);
        $finalArray[$newArray[$inc][0]] = urldecode($newArray[$inc][1]);
    }
    if (!isset($finalArray['METHOD'])) {
        $finalArray['METHOD'] = $method;
    }

    $_SESSION['ACK'] = $finalArray['ACK'];
    $_SESSION['TOKEN'] = $finalArray['TOKEN'];
    $_SESSION['ACTION'] = $request['PAYMENTREQUEST_0_PAYMENTACTION'];
    $_SESSION['METHOD'] = $method;
    $_SESSION['APITYPE'] = "NVP";
    $_SESSION['REQUEST'] = urlencode(serialize($request));
    $_SESSION['RESPONSE'] = urlencode(serialize($finalArray));


    // preg_replace("/&/","\n",urldecode(setUpParams($finalArray)));
}

function getToken($input, $method) {
    $resp = getStdResponse($input, $method);
    $token = $resp['TOKEN'];
    return $token;
}

function decode($serialised) {
    $array = unserialize(urldecode($serialised));
    ksort($array);
    $titles = array_keys($array);
    $data = "\n";
    for ($inc = 0; $inc < count($array); $inc ++) {
        $current = $titles[$inc];

        if ((!empty($array[$current])) && ($array[$current] != "")) {
            $data .= "[" . $current . "] = " . $array[$current] . "\n";
        }
    }
    return trim($data);
}

?>