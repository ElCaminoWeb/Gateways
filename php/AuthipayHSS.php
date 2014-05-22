<?php
include 'inc_classes.php';
$gateway = new Gateway("SetExpressCheckout", "GetExpressCheckoutDetails", "DoExpressCheckoutPayment", "DoAuthorization", null,null);
function getResponse($input, $method) {
    $request["txntype"] = "sale";
    $request["timezone"] = "GMT";
    $request["txndatetime"] = getDateTime();
    $request["storename"] = "13205400147";
    $request["mode"] = "payonly";
    $request["chargetotal"] = $input['PAYMENTREQUEST_0_AMT'];
    $request["invoicenumber"] = "12345";
    $request["productid"] = "T-Shirt";
    $request["description"] = "M - Red";
    $request["price"] = $input['PAYMENTREQUEST_0_AMT'];
    
    $request["currency"] = "826";
    $request["language"] = "en_GB";
    $request["hash"] = createHash( $input['PAYMENTREQUEST_0_AMT'], "826" );
    $request["paymentMethod"] ="paypal";
    $request["responseSuccessURL"] = "http://www.example.com";
    $request["responseFailURL"] = "http://www.example.com";
    
    /* Recurring payments 
    $request["recurringInstallmentCount"] = "2";
    $request["recurringInstallmentPeriod"] = "day";
    $request["recurringInstallmentFrequency"] = "1";
    $request["recurringComments"] = "Test recurring payments";
   // */
    
    /* Shipping address  
    $request["sname"] =	$input["PAYMENTREQUEST_0_SHIPTONAME"];
    $request["saddr1"] = $input["PAYMENTREQUEST_0_SHIPTOSTREET"];
    $request["saddr2"] = $input["PAYMENTREQUEST_0_SHIPTOSTREET2"];
    $request["scity"] = $input["PAYMENTREQUEST_0_SHIPTOCITY"];
    $request["sstate"] = $input["PAYMENTREQUEST_0_SHIPTOSTATE"];
    $request["scountry"] = $input["PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE"];
    $request["szip"] = $input["PAYMENTREQUEST_0_SHIPTOZIP"];
    // */
    /* Billing address 
    $request["bname"] =	$input["PAYMENTREQUEST_0_SHIPTONAME"];
    $request["baddr1"] = $input["PAYMENTREQUEST_0_SHIPTOSTREET"];
    $request["baddr2"] = $input["PAYMENTREQUEST_0_SHIPTOSTREET2"];
    $request["bcity"] = $input["PAYMENTREQUEST_0_SHIPTOCITY"];
    $request["bstate"] = $input["PAYMENTREQUEST_0_SHIPTOSTATE"];
    $request["bcountry"] = $input["PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE"];
    $request["bzip"] = $input["PAYMENTREQUEST_0_SHIPTOZIP"];
    //*/
    
    $url = "https://test.ipg-online.com/connect/gateway/processing";
    
    
    redirect($url, "post", $request);
}

function getDateTime() {
    global $dateTime;
    return date("Y:m:d-H:i:s");
}

function createHash($chargetotal, $currency) {
    $storename = "13205400147";
    $sharedSecret = "k49HRjeCxa";

    $stringToHash = $storename . getDateTime() . $chargetotal . $currency . $sharedSecret;

    $ascii = bin2hex($stringToHash);

    return sha1($ascii);
}
function decode($serialised) {
    $array = unserialize(urldecode($serialised));
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
