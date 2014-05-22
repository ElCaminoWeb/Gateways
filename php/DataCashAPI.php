<?php

include 'inc_classes.php';
$gateway = new Gateway("set_express_checkout", "get_express_checkout_details", "do_express_checkout_payment", "do_authorization", "masspay", array('REFERENCE'));
error_reporting(E_ALL ^ E_NOTICE);

function getResponse($input, $method) {
    // Security Parameters		
    //global $config;
    //$send_doc = new DataCash_Document("Request");
    $client = "88000796";
    $password = "PcXAspS8aGg";

    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    $Request = $xml->createElement("Request");

    $Transaction = convert($input, $method, $xml);

    $Request->appendChild($Transaction);
    $Request->appendChild(createAuthentication($client, $password, $xml));
    $xml->appendChild($Request);
    $xmlString = $xml->saveXML();

    $ch = curl_init("https://accreditation.datacash.com/Transaction/cnp_a");
    curl_setopt_array($ch, array(
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $xmlString,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_TIMEOUT => 5
            )
    );
    $response = curl_exec($ch);
    $decoded = decodeResponse($response, $method, $xml);
    return $decoded;
}

function createAuthentication($client, $password, $xml) {
    $Authentication = $xml->createElement("Authentication");
    $Authentication->appendChild($xml->createElement("client", $client));
    $Authentication->appendChild($xml->createElement("password", $password));

    return $Authentication;
}

function convert($input, $method, $xml) {
    switch ($method) {
        case "set_express_checkout":
            $Transaction = convertSet($input, $method, $xml);
            break;
        case "get_express_checkout_details":
            $Transaction = convertGet($method, $xml);
            break;
        case "do_express_checkout_payment":
            $Transaction = convertDo($input, $method, $xml);
            break;
        case "masspay":
            $Transaction = convertMP($input, $method, $xml);
            break;
    }
    
    return $Transaction;
}

function convertSet($inp, $method, $xml) {
    global $setEC;

    $PayPalTxn = $xml->createElement("PayPalTxn");
    $PayPalTxn->appendChild($xml->createElement("method", $method));
    $cart = -1;
    $items = -1;
    $address = -1;
    $titles = array_keys($inp);
    $numberOfParams = sizeOf($titles);
    for ($inc = 0; $inc < $numberOfParams; $inc ++) {
        $cur = $titles[$inc];
        $newTitle = $setEC[$cur];
        if ($newTitle == "Total") {
            $total = $xml->createElement("amount", $inp[$cur]);
        } else if ($newTitle == "Currency") {
            $currency = $xml->createAttribute("currency");
            $currency->value = $inp[$cur];
        } else if ($newTitle == "Border") {
            $cart = customiseCart("bgcolor", $inp[$cur], $xml, $cart);
        } else if ($newTitle == "Logo") {
            $cart = customiseCart("img", $inp[$cur], $xml, $cart);
        } else if (preg_match('/^L_PAYMENTREQUEST/', $cur) == 1) {
            $temp = preg_split('/_/', $cur);
            $items = updateItems($temp[3], $inp[$cur], $xml, $items);
        } else if (preg_match('/^ShippingAddress/', $newTitle) == 1) {
            $temp = preg_split('/-/', $newTitle);
            print_r($temp);
            $address = updateAddress($temp[1], $inp[$cur], $xml, $address);
        } else if ($newTitle != "") {
            $PayPalTxn->appendChild($xml->createElement($newTitle, $inp[$cur]));
        }
    }
    // Add items
    if ($items != -1) {
        $PayPalTxn->appendChild($items);
    }

    // Add cart
    if ($cart != -1) {
        $PayPalTxn->appendChild($cart);
    }

    // Add address
    if ($address != -1) {
        $PayPalTxn->appendChild($address);
    }

    $TxnDetails = $xml->createElement("TxnDetails");
    // Add total
    $total->appendChild($currency);
    $TxnDetails->appendChild($total);

    // Add merchant reference
    $TxnDetails->appendChild($xml->createElement("merchantreference", time()));

    // Complete Transaction request
    $Transaction = $xml->createElement("Transaction");
    $Transaction->appendChild($PayPalTxn);
    $Transaction->appendChild($TxnDetails);

    return $Transaction;
}

function convertGet($method, $xml) {
    $PayPalTxn = $xml->createElement("PayPalTxn");
    $PayPalTxn->appendChild($xml->createElement("method", $method));
    $PayPalTxn->appendChild($xml->createElement("reference", $_SESSION['REFERENCE0']));

    $TxnDetails = $xml->createElement("TxnDetails");
    $TxnDetails->appendChild($xml->createElement("merchantreference", time()));
    
    $Transaction = $xml->createElement("Transaction");
    $Transaction->appendChild($PayPalTxn);
    $Transaction->appendChild($TxnDetails);

    return $Transaction;
}

function convertDo($inp, $PayPalTxn, $xml) {
    print_r($_SESSION);
    $xmlDoc = new DOMDocument();
    $xmlDoc->loadXML($inp);
    
    $PayPalTxn = $xml->createElement("PayPalTxn");
    $PayPalTxn->appendChild($xml->createElement("method", $method));
    $PayPalTxn->appendChild($xml->createElement("reference", $_SESSION['REFERENCE0']));
    
    $PayPalTxn->appendChild($xmlDoc->getElementsByTagName('ShippingAddress')->item(0));

    $items = -1;
    $tags = $xmlDoc->getElementsByTagName("*");
    $numberOfParams = sizeOf($tags);
    for ($inc = 0; $inc < $numberOfParams; $inc ++) {
        $cur = $tags->item($inc);
        $name = strtoupper($cur->nodeName);
        $val = $cur->nodeValue;
        //$newTitle = $getEC[$name];
        $newTitle = -1;
        if (preg_match('/^L_/', $name) == 1) {
            $temp = preg_split('/_/', $name);
            $items = updateItems($temp[1], $val, $xml, $items);
        } else if ($newTitle != "") {
            $PayPalTxn->appendChild($xml->createElement($newTitle, $val));
        }
    }
    // Add items
    if ($items != -1) {
        $PayPalTxn->appendChild($items);
    }
    
    $TxnDetails = $xml->createElement("TxnDetails");
    
    // Add total
    $currency = $xmlDoc->getElementsByTagName("currencycode")->item(0)->nodeValue;
    $totalVal = $xmlDoc->getElementsByTagName("amt")->item(0)->nodeValue;
    $total = $xml->createElement("amount", $totalVal);
    $total->appendChild($currency);
    $TxnDetails->appendChild($total);

    // Add merchant reference
    $TxnDetails->appendChild($xml->createElement("merchantreference", time()));

    // Complete Transaction request
    $Transaction = $xml->createElement("Transaction");
    $Transaction->appendChild($PayPalTxn);
    $Transaction->appendChild($TxnDetails);

    return $Transaction;
}

function convertMP($inp, $send_doc) {
    global $MPToDC;

    $titles = array_keys($inp);
    $numberOfParams = sizeOf($titles);
    $ppreq = array();
    for ($inc = 0; $inc < $numberOfParams; $inc ++) {
        $cur = $titles[$inc];
        $newTitle = $MPToDC[$cur];
        if ($newTitle != "") {
            $ppreq[$cur] = $inp[$cur];
            switch ($newTitle) {
                case "Total":
                    $send_doc->set("Request.Transaction.TxnDetails.amount", $inp[$cur], array('currency' => $inp["CURRENCYCODE"]));
                    break;
                case "Currency":
                    break;
                case "Recipient":
                    echo "Type: " . $inp["RECEIVERTYPE"];
                    $send_doc->set("Request.Transaction.MassPayTxn.recipient", $inp[$cur], array('type' => $inp["RECEIVERTYPE"]));
                    break;
                case "Type":
                    break;
                default:
                    $send_doc->set($newTitle, $inp[$cur]);
                    break;
            }
        }
    }

    return $ppreq;
    //echo "<pre>" . getHtmlDocument($send_doc) . "</pre>";
    //if ($basket) {$new = array_merge($new, array("BasketXML"=> $xml->saveXML())); }
}

function decodeResponse($response, $method, $request) {
    $xmlDoc = new DOMDocument();
    $xmlDoc->loadXML($response);
    $_SESSION['ACK'] = $xmlDoc->getElementsByTagName('ack')->item(0)->nodeValue;
    $_SESSION['TOKEN'] = $xmlDoc->getElementsByTagName('token')->item(0)->nodeValue;
    // $_SESSION['ACTION'] = $request['PAYMENTREQUEST_0_PAYMENTACTION'];
    $_SESSION['METHOD'] = $method;
    $_SESSION['APITYPE'] = "SOAP";
    $_SESSION['REQUEST'] = urlencode(getHTMLDocument($request));
    $_SESSION['RESPONSE'] = urlencode(getHTMLDocument($xmlDoc));
    $_SESSION['REFERENCE0'] = $xmlDoc->getElementsByTagName('datacash_reference')->item(0)->nodeValue;
}

function updateItems($elmt, $val, $xml, $items) {
    global $setEC;
    if ($items == -1) {
        $items = $xml->createElement("Items");
    }
    $elmtName = substr($elmt, 0, -1);
    $newTitle = $setEC[$elmtName];
    if ($newTitle != NULL) {
        $itemNum = substr($elmt, -1);
        $itemObjs = $items->childNodes;
        $item = $itemObjs->item($itemNum);
        if (!isset($item)) {
            $item = $xml->createElement("Item");
            $id = $xml->createAttribute("id");
            $id->value = $itemNum;
            $item->appendChild($id);
        }
        $item->appendChild($xml->createElement($newTitle, $val));
        $items->appendChild($item);
    }

    return $items;
}

function updateAddress($elmt, $val, $xml, $address) {
    if ($address == -1) {
        $address = $xml->createElement("ShippingAddress");
    }
    $address->appendChild($xml->createElement($elmt, $val));
    return $address;
}

function customiseCart($name, $val, $xml, $cart) {
    if ($cart == -1) {
        $cart = $xml->createElement("header_style");
        $attr = $xml->createAttribute($name);
        $attr->value = $val;
        $cart->appendChild($attr);
    } else {
        $attr = $xml->createAttribute($name);
        $attr->value = $val;
        $cart->appendChild($attr);
    }
    return $cart;
}

function decode($serialised) {
    $array = urldecode($serialised);
    return $array;
}

function getHtmlDocument($xmldoc) {
    $stringDocument = $xmldoc->saveXML();
    $stringDocument = preg_replace('/</', '&lt;', $stringDocument);
    $stringDocument = preg_replace('/>/', '&gt;', $stringDocument);
    return $stringDocument;
}

global $setEC;
// Set up conversion array
$setEC["METHOD"] = "method";
$setEC["PAYMENTREQUEST_0_AMT"] = "Total";
$setEC['PAYMENTREQUEST_0_CURRENCYCODE'] = "Currency";
$setEC['MAXAMT'] = "max_amount";
$setEC['RETURNURL'] = "return_url";
$setEC['CANCELURL'] = "cancel_url";
$setEC['REQCONFIRMSHIPPING'] = "req_confirmed_shipping";
$setEC['NOSHIPPING'] = "no_shipping";
//$stdToDC['ALLOWNOTE'] = "Request.Transaction.PayPalTxn.return_url";
$setEC['ADDROVERRIDE'] = "override_address";
$setEC['LOCALECODE'] = "localecode";
$setEC['CARTBORDERCOLOR'] = "Border";
$setEC['LOGOIMG'] = "Logo";
$setEC['SOLUTIONTYPE'] = "solution_type";
// Item Details
$setEC['NAME'] = "name";
$setEC['QTY'] = "quantity";
$setEC['AMT'] = "amount";
// Billing Agreement Details
$setEC['L_BILLINGTYPE0'] = "billing_type";
$setEC['L_BILLINGAGREEMENTDESCIPTION0'] = "billing_agreement_description";
// Address Details
$setEC['PAYMENTREQUEST_0_SHIPTONAME'] = "ShippingAddress-name";
$setEC['PAYMENTREQUEST_0_SHIPTOSTREET'] = "ShippingAddress-street_address1";
$setEC['PAYMENTREQUEST_0_SHIPTOSTREET2'] = "ShippingAddress-street_address2";
$setEC['PAYMENTREQUEST_0_SHIPTOCITY'] = "ShippingAddress-city";
$setEC['PAYMENTREQUEST_0_SHIPTOSTATE'] = "ShippingAddress-region";
$setEC['PAYMENTREQUEST_0_SHIPTOZIP'] = "ShippingAddress-postcode";
$setEC['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = "ShippingAddress-country_code";
$setEC['PAYMENTREQUEST_0_SHIPTOPHONENUM'] = "ShippingAddress-telephone_number";
// Totals
$setEC['PAYMENTREQUEST_0_ITEMAMT'] = "item_total";
$setEC['PAYMENTREQUEST_0_SHIPPINGAMT'] = "shipping_total";
$setEC['PAYMENTREQUEST_0_HANDLINGAMT'] = "handling_total";
$setEC['PAYMENTREQUEST_0_TAXAMT'] = "tax_total";
// Other
$setEC['PAYMENTREQUEST_0_DESC'] = "description";
$setEC['CUSTOM'] = "custom";
$setEC['PAYMENTREQUEST_0_INVNUM'] = "invnum";


global $MPToDC;
// Set up conversion array
$MPToDC["METHOD"] = "Request.Transaction.PayPalTxn.method";
$MPToDC["L_AMT0"] = "Total";
$MPToDC['CURRENCYCODE'] = "Currency";
$MPToDC['EMAILSUBJECT'] = "Request.Transaction.MassPayTxn.email_subject";
$MPToDC['RECEIVERTYPE'] = "Type";
$MPToDC['L_EMAIL0'] = "Recipient";
$MPToDC['L_RECEIVERPHONE0'] = "Recipient";
$MPToDC['L_RECEIVERID0'] = "Recipient";
$MPToDC['L_NOTE0'] = "Request.Transaction.MassPayTxn.note";
?>