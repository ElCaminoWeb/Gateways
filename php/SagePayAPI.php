<?php

include 'inc_classes.php';
$gateway = new Gateway("SetEC", "GetECD", "DoECP", "DoAuthorization", "MassPay", null);

function getResponse($input, $method) {
    // Security Parameters
    /* UK Account */
    $header["VPSProtocol"] = "2.23";
    $header["Vendor"] = "pptest";
    $rand = time();
    if ($method == "SetEC") {
        $header["TxType"] = "PAYMENT";
        $url = "https://test.sagepay.com/Simulator/VSPDirectGateway.asp?";
    } else if ($method == "GetECD") {
        $_SESSION['ACK'] = "Success";
        $_SESSION['METHOD'] = "Get";
        $_SESSION['APITYPE'] = "NVP";
        $_SESSION['REQUEST'] = urlencode(serialize(array("NoRequest" => "There is no request as this is all simulated.")));
        $_SESSION['RESPONSE'] = urlencode(serialize($input));
        return $input;
    } else if ($method == "DoECP") {
        
    }
    $header["VendorTxCode"] = $rand;
    $header["CardType"] = "PayPal";
    $header["Description"] = "Test";
    // Load XML doc
    $xml = new DOMDocument('1.0');
    //$xml->load("SP1_Set.xml");	
    $xml->formatOutput = true;
    $basket = $xml->createElement('basket');
    $basket = $xml->appendChild($basket);
    $paramArray = convert($input, $xml);
    $paramArray["BillingSurname"] = "Name";
    $paramArray["BillingFirstnames"] = "Customer";
    $paramArray["BillingAddress1"] = "Street 1";
    $paramArray["BillingCity"] = "City";
    $paramArray["BillingPostCode"] = "AB12 3CD";
    $paramArray["BillingCountry"] = "GB";
    $request = array_merge($header, $paramArray);
    $data = setUpParams($request);
    //redirect($end, "post", $request);
    // Talk to API Server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    $response = curl_exec($ch);
    $curlerr = curl_error($ch);
    //var_dump($curlerr);
    
    // echo $response;
    curl_close($ch);
    $resp = explode(PHP_EOL, $response);
    var_dump($resp);
    $titles = array();
    $values = array();
    for ($inc = 0; $inc < count($resp); $inc ++) {
        $temp = explode("=", $resp[$inc]);
        $titles[$inc] = $temp[0];
        for ($tmpInc = 1; $tmpInc < count($temp); $tmpInc ++) {
            if ($tmpInc == 1) {
                $values[$inc] = $temp[$tmpInc];
            } else {
                $values[$inc] .= "=" . $temp[$tmpInc];
            }
        }
    }
    //$values[count($titles)-1] = $resp[count($resp)-1];

    $reply = array();
    for ($inc = 0; $inc < (count($titles)); $inc ++) {
        $reply[$titles[$inc]] = urldecode($values[$inc]);
    }

    switch ($reply['Status']) {
        case"PPREDIRECT":
            $ack = "Success";
            break;
        default:
            $ack = "Failure";
            break;
    }
    $_SESSION['ACK'] = $ack;
    $_SESSION['TOKEN'] = "EC-25643518W6515625N7159495R";
    $_SESSION['METHOD'] = "Register";
    $_SESSION['APITYPE'] = "NVP";
    $_SESSION['URL'] = $reply['PayPalRedirectURL'];
    $_SESSION['REQUEST'] = urlencode(serialize($request));
    $_SESSION['RESPONSE'] = urlencode(serialize($reply));
    return $reply;

    // Decode API response
//        $decoded = decodeResponse($response); 
    // return $response; 
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

function convert($inp, $xml) {
    global $EC;
    $titles = array_keys($inp);
    $numberOfParams = sizeOf($titles);
    $basket = false;
    $new = array();
    for ($inc = 0; $inc < $numberOfParams; $inc ++) {
        $cur = $titles[$inc];
        if (isset($EC[$cur])) {
            $newTitle = $EC[$cur];
        } else {
            $newTitle = -1;
        }

        if ($newTitle == "basket") {
            $basket = true;
            updateBasket($xml, $cur, $inp[$cur]);
        } else if ($newTitle == "ShippingName") {
            $temp = explode(" ", $inp[$cur]);
            $new["DeliverySurname"] = $temp[1];
            $new["DeliveryFirstnames"] = $temp[0];
        } else if ($newTitle != -1) {
            echo "Title: " . $newTitle . " Value: " . $inp[$cur] . "<br>";
            $new[$newTitle] = $inp[$cur];
        }
    }
    if ($basket) {
        $new["BasketXML"] = $xml->saveXML();
    }
    return $new;
}

function updateBasket($xml, $title, $val) {
    $basket = $xml->getElementsByTagName('basket')->item(0);
    if ($title == "PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID") {
        $agentId = $xml->createElement('agentId', $val);
        $agentId = $basket->appendChild($agentId);
    } else {
        $item = $xml->getElementsByTagName('item')->item(0);
        if (!isset($item)) {
            $item = $xml->createElement('item');
            $item = $basket->appendChild($item);
        }
        switch ($title) {
            case "L_PAYMENTREQUEST_0_NAME0":
                $elmt = $xml->createElement('productSku', $val);
                break;
            case "L_PAYMENTREQUEST_0_NUMBER0":
                $elmt = $xml->createElement('productCode', $val);
                break;
            case "L_PAYMENTREQUEST_0_DESC0":
                $elmt = $xml->createElement('description', $val);
                break;
            case "L_PAYMENTREQUEST_0_QTY0":
                $elmt = $xml->createElement('quantity', $val);
                break;
            case "L_PAYMENTREQUEST_0_AMT0":
                $elmt = $xml->createElement('unitGrossAmount', $val);
                break;
        }
        $elmt = $item->appendChild($elmt);
    }
}

function xmlToString($xml) {
    $str = "<basket>";
    $agents = $xml->getElementsByTagName('agentId');
    $numberOfAgents = $agents->length;
    $agentId = $agents->item(0)->textContent;
    $str .= "<agentId>" . $agentId . "</agentId>";
    $str .= "<item><productSku>" . $xml->getElementsByTagName('productSku')->item(0)->textContent . "</productSku>";
    $str .= "<description>" . $xml->getElementsByTagName('description')->item(0)->textContent . "</description>";
    $str .= "<quantity>" . $xml->getElementsByTagName('quantity')->item(0)->textContent . "</quantity>";
    $str .= "<unitGrossAmount>" . $xml->getElementsByTagName('unitGrossAmount')->item(0)->textContent . "</unitGrossAmount>";
    $str .= "</item></basket>";

    return $str;
}

global $EC;
// Set up conversion array
$EC["METHOD"] = -1;
$EC["PAYMENTREQUEST_0_AMT"] = "Amount";
$EC['PAYMENTREQUEST_0_CURRENCYCODE'] = "Currency";
$EC['MAXAMT'] = -1;
$EC['RETURNURL'] = "PayPalCallbackURL";
$EC['CANCELURL'] = -1;
$EC['REQCONFIRMSHIPPING'] = -1;
$EC['NOSHIPPING'] = -1;
//$stdToDC['ALLOWNOTE'] = "Request.Transaction.PayPalTxn.return_url";
$EC['ADDROVERRIDE'] = -1;
$EC['LOCALECODE'] = "Language";
$EC['CARTBORDERCOLOR'] = -1;
$EC['LOGOIMG'] = -1;
$EC['SOLUTIONTYPE'] = -1;
// Item Details
$EC['L_PAYMENTREQUEST_0_NAME0'] = "basket";
$EC['L_PAYMENTREQUEST_0_DESC0'] = "basket";
$EC['L_PAYMENTREQUEST_0_QTY0'] = "basket";
$EC['L_PAYMENTREQUEST_0_AMT0'] = "basket";
// Address Details
$EC['PAYMENTREQUEST_0_SHIPTONAME'] = "ShippingName";
$EC['PAYMENTREQUEST_0_SHIPTOSTREET'] = "DeliveryAddress1";
$EC['PAYMENTREQUEST_0_SHIPTOSTREET2'] = "DeliveryAddress2";
$EC['PAYMENTREQUEST_0_SHIPTOCITY'] = "DeliveryCity";
$EC['PAYMENTREQUEST_0_SHIPTOSTATE'] = -1;
$EC['PAYMENTREQUEST_0_SHIPTOZIP'] = "DeliveryPostCode";
$EC['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = "DeliveryCountry";
$EC['PAYMENTREQUEST_0_SHIPTOPHONENUM'] = "DeliveryPhone";
// Totals
$EC['PAYMENTREQUEST_0_ITEMAMT'] = -1;
$EC['PAYMENTREQUEST_0_SHIPPINGAMT'] = -1;
$EC['PAYMENTREQUEST_0_HANDLINGAMT'] = -1;
$EC['PAYMENTREQUEST_0_TAXAMT'] = -1;
// Other
$EC['PAYMENTREQUEST_0_DESC'] = -1;
$EC['CUSTOM'] = -1;
$EC['PAYMENTREQUEST_0_INVNUM'] = -1;
?>