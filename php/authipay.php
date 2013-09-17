<?php

include 'inc_classes.php';
$gateway = new Gateway("sale", "payPalEcGetDetailsService", "Sale", "payPalAuthorizationService", array('requestToken', 'requestID'));

function errorResponse($response) {
    $errorMessage = "<p><strong>Error (";
    $errorMessage .= $response['errorCode'];
    $errorMessage .= ") </p></strong>";
    $errorMessage .= "<p>" . $response['L_SHORTMESSAGE0'] . "</p>";
    $errorMessage .= "<p>" . $response['L_LONGMESSAGE0'] . "</p>";
    $errorMessage .= "<p> See <a href=\"https://developer.paypal.com/webapps/developer/docs/classic/api/errorcodes/\"> PayPal Error Codes</a> for more details </p> </div>";
    $errorMessage .= rawurlencode($errorMessage);
    $error = array("Error" => $errorMessage);
    return $error;
}

/* function getResponse($method, $input) {
  $client = new nusoap_client("https://test.ipg-online.com/ipgapi/services");


  } */

function getResponse($method, $input) {
    $order = "C-8b10e7a4-6827-4e52-8c5c-724b8d95192f";
    switch ($method) {
        case "capture":
            /* Capture */
            $xmlStr = "<SOAP-ENV:Envelope
                           xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
                           <SOAP-ENV:Header />
                           <SOAP-ENV:Body>
                               <ns5:IPGApiOrderRequest
                                   xmlns:ns5 =\"http://ipg-online.com/ipgapi/schemas/ipgapi\"
                                   xmlns:ns3 =\"http://ipg-online.com/ipgapi/schemas/a1\"
                                   xmlns:ns4 =\"http://ipg-online.com/ipgapi/schemas/v1\">
                                   <ns4:Transaction>
                                       <ns4:PayPalTxType>
                                           <ns4:Type>postAuth</ns4:Type>
                                       </ns4:PayPalTxType>
                                       <ns4:Payment>
                                           <ns4:ChargeTotal>3</ns4:ChargeTotal>
                                           <ns4:Currency>GBP</ns4:Currency>
                                       </ns4:Payment>
                                       <ns4:TransactionDetails>
                                           <ns4:OrderId>" . $order . "</ns4:OrderId>
                                       </ns4:TransactionDetails>
                                   </ns4:Transaction>
                               </ns5:IPGApiOrderRequest>
                           </SOAP-ENV:Body>
                       </SOAP-ENV:Envelope>";
            break;
        case "Billing Agreement":
            /* Recurring Payment */
            $xmlStr = "<SOAP-ENV:Envelope
            xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
            <SOAP-ENV:Header />
            <SOAP-ENV:Body>
                <ns4:IPGApiActionRequest 
                    xmlns:ns4 =\"http://ipg-online.com/ipgapi/schemas/ipgapi\"
                    xmlns:ns2 =\"http://ipg-online.com/ipgapi/schemas/a1\" 
                    xmlns:ns3 =\"http://ipg-online.com/ipgapi/schemas/v1\">
                    <ns2:Action>
                        <ns2:RecurringPayment>
                            <ns2:Function>install</ns2:Function>
                            <ns2:RecurringPaymentInformation>
                                <ns2:RecurringStartDate>20130910</ns2:RecurringStartDate>
                                <ns2:InstallmentCount>12</ns2:InstallmentCount>
                                <ns2:InstallmentFrequency>1</ns2:InstallmentFrequency>
                                <ns2:InstallmentPeriod>month</ns2:InstallmentPeriod>
                            </ns2:RecurringPaymentInformation>
                                    <ns3:Payment>
                                        <ns3:ChargeTotal>1</ns3:ChargeTotal>
                                        <ns3:Currency>826</ns3:Currency>
                                    </ns3:Payment>
                                </ns2:RecurringPayment>
                            </ns2:Action>
                        </ns4:IPGApiActionRequest>
                    </SOAP-ENV:Body>
              </SOAP-ENV:Envelope>";
            //*/
            break;
        case "Void":
            $xmlStr = "<SOAP-ENV:Envelope
            xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
            <SOAP-ENV:Header />
            <SOAP-ENV:Body>
            <ns5:IPGApiOrderRequest
            xmlns:ns5=\"http://ipg-online.com/ipgapi/schemas/ipgapi\"
            xmlns:ns3=\"http://ipg-online.com/ipgapi/schemas/a1\"
            xmlns:ns4=\"http://ipg-online.com/ipgapi/schemas/v1\">
            <ns4:Transaction>
            <ns4:PayPalTxType>
            <ns4:Type>void</ns4:Type>
            </ns4:PayPalTxType>
            <ns4:TransactionDetails>
            <ns4:OrderId>C-d0ab3922-94f6-4ad3-ad0e-1b31bafa53c1</ns4:OrderId>
            <ns4:TDate>1378808837</ns4:TDate>
            </ns4:TransactionDetails>
            </ns4:Transaction>
            </ns5:IPGApiOrderRequest>
            </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>";
            break;
        case "Refund":
            $xmlStr = "<SOAP-ENV:Envelope
            xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\">
            <SOAP-ENV:Header />
            <SOAP-ENV:Body>
            <ns5:IPGApiOrderRequest
            xmlns:ns5=\"http://ipg-online.com/ipgapi/schemas/ipgapi\"
            xmlns:ns3=\"http://ipg-online.com/ipgapi/schemas/a1\"
            xmlns:ns4=\"http://ipg-online.com/ipgapi/schemas/v1\">
            <ns4:Transaction>
            <ns4:PayPalTxType>
            <ns4:Type>return</ns4:Type>
            </ns4:PayPalTxType>
            <ns4:Payment>
            <ns4:ChargeTotal>3</ns4:ChargeTotal>
            <ns4:Currency>GBP</ns4:Currency>
            </ns4:Payment>
            <ns4:TransactionDetails>
            <ns4:OrderId>" . $order . "</ns4:OrderId>
            </ns4:TransactionDetails>
            </ns4:Transaction>
            </ns5:IPGApiOrderRequest>
            </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>";
            break;
    }

    // initializing cURL with the IPG API URL:
    $ch = curl_init("https://test.ipg-online.com/ipgapi/services");
    // setting the request type to POST:
    curl_setopt($ch, CURLOPT_POST, 1);
    // setting the content type:
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
    // setting the authorization method to BASIC:
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // supplying your credentials:
    curl_setopt($ch, CURLOPT_USERPWD, "WS13205400147._.1:DJJq2w1uDd");
    // filling the request body with your SOAP message:
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
    // telling cURL to verify the server certificate:
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    // setting the path where cURL can find the certificate to verify the
    // received server certificate against:
    curl_setopt($ch, CURLOPT_CAINFO, "C:\Users\ebird\Downloads\IPG_Certificate_WS13205400147._.1\client\geotrust.pem");
    // setting the path where cURL can find the client certificate:
    curl_setopt($ch, CURLOPT_SSLCERT, "C:\Users\ebird\Downloads\IPG_Certificate_WS13205400147._.1\client\WS13205400147._.1.pem");
    // setting the path where cURL can find the client certificate’s
    // private key:
    curl_setopt($ch, CURLOPT_SSLKEY, "C:\Users\ebird\Downloads\IPG_Certificate_WS13205400147._.1\client\WS13205400147._.1.key");
    // setting the key password:
    curl_setopt($ch, CURLOPT_SSLKEYPASSWD, "fythSSJ0pH");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_CAINFO, 'C:/wamp/www/Hermes/cacert.pem');

    $response = curl_exec($ch);

    $curlerr = curl_error($ch);
    curl_close($ch);

    /* echo 1;
      $client = new SoapClient('https://test.ipg-online.com/ipgapi/services/order.wsdl');
      echo 2;
      $response = $client->Request(array(
      'Transaction' => array(
      'PayPalTxType' => array(
      'Type' => 'credit'
      ),
      'ClickandBuyData' => '',
      'Payment' => array(
      'ChargeTotal' => '1',
      'Currency' => 'EUR'
      ),
      'Billing' => array(
      'Email' => 'ebird-pers-gb@gmail.com'
      )
      )
      ));
     */
    echo "<pre>";
    print_r($curlerr);
    print_r($response); // view the full response to see what is returned
    echo "</pre>";
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
    $_SESSION['ACK'] = $ack;
    $_SESSION['TOKEN'] = $response['payPalEcSetReply_paypalToken'];
    $_SESSION['METHOD'] = $method;
    $_SESSION['APITYPE'] = "NVP";
    $_SESSION['REQUEST'] = urlencode(serialize($request));
    $_SESSION['RESPONSE'] = urlencode(serialize($response));
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

