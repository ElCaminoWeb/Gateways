<?php
include 'general.php';
/* UK Sandbox Account  */
$request["user"] = "child_api1.mam.com";
$request["pwd"] = "1386075830";
$request["signature"] = "Aq64XqZfY60hBhzUe.k7TaTvidjIAnTe6L9S8cVw2UFTiESsL6mdoFjs";
$end = "https://api-3t.sandbox.paypal.com/nvp";
// General Parameters
$request["version"] = "106.0";
$request["method"] = "GetTransactionDetails";
$request["TRANSACTIONID"] = $_GET["transId"];
$data = setUpParams($request);

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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Response</title>
        <link type="text/css" href="../css/bootstrap.css" rel="stylesheet" media="screen"/>
        <link href="css/bootstrap-glyphicons.css" rel="stylesheet">
    </head>
    <body style="background-color: #F3F3F3">
        <?php 
            if($finalArray['ACK'] == "Success") {
             var_dump($finalArray);
            } else {
                echo '<div class="alert alert-danger">';
                echo '<p><b>Error (' . $finalArray['L_ERRORCODE0']. '): </b>'. $finalArray['L_SHORTMESSAGE0']. '</p>';
                echo '<p>'. $finalArray['L_LONGMESSAGE0']. '</p>';
                echo '</div>';
                //print_r($finalArray);
            }
        ?>
    </body>
</html>

