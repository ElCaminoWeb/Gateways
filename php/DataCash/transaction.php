<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Send a PHP Transaction</title>
    <style type="text/css"> body { background-color: #FFFFFF; margin-left: 1%; margin-right: 1%;}th, td, p, dt, dl, dd, ul, ol, li { color: black; font-family: Arial, Helvetica, Geneva, sans-serif; font-size: 12px;}td.small { color: black; font-family: Arial, Helvetica, Geneva, sans-serif; font-size: 10px;}code, pre { font-family: monospace; font-size: 12px;}h1 { font-size: 20px; color: #0066FF; font-family: Arial, Helvetica, Geneva, sans-serif;}h2 { font-size: 18px; color: #0066FF; font-family: Arial, Helvetica, Geneva, sans-serif;}h3 { font-size: 16px; color: #0066FF; font-family: Arial, Helvetica, Geneva, sans-serif;}h4 { font-size: 14px; color: #0066FF; font-family: Arial, Helvetica, Geneva, sans-serif;}h5 { font-size: 12px; color: #0066FF; font-family: Arial, Helvetica, Geneva, sans-serif;}h6 { font-size: 10px; color: #0066FF; font-family: Arial, Helvetica, Geneva, sans-serif;} .field { background-color: #FFEEEE; font-weight: bold } .warning { color: red; font-weight: bold } .highlight { color: blue; font-weight: bold }</style>
  </head>
  <body>

    <?php

/**
 * This module contains an example transaction using the DataCash PHP API
 *
 * @internal $Id: transaction.php,v 1.4 2007/02/28 14:19:59 sxf Exp $
 *
 * @package Test
 * @copyright DataCash Group plc 2003
 */
    
include ("DataCash.php");

/**
 * Return a random merchant reference.
 *
 * @access public
 * @return string
 */
function random_ref () {

  /* This generates a random alphabetical merchant reference, but for me
   * doesn't seem always be random enough! Use it if you like.
   * $allchars = "abcdefghijklmnopqrstuvwxyz";
   * for ($i = 0;$i < 17;$i++) {
   *   $string .= $allchars{mt_rand (0,strlen($allchars) -1)};
   * }
   * return $string;
   */

  return time(); /* Unix time / seconds since 1970 should be unique enough */
}

/**
 * Return the supplied DataCash_Document as a string with XML entities transformed to HTML.
 *
 * @access public
 * @param DataCash_Document $xmldoc
 * @return string
 */
function getHtmlDocument ( $xmldoc ) {
    $stringDocument = $xmldoc->getDocument();
    $stringDocument = ereg_replace( '<', '&lt;', $stringDocument );
    $stringDocument = ereg_replace( '>', '&gt;', $stringDocument );
    return $stringDocument;
}

    ?>
    
    <h1>Send a PHP Transaction</h1> 
    
    <h2>1. Values from HTML Page</h2>

    <p>First, let's confirm the values which have been sent in from the HTML page.</p>

    <table cellpadding=3 cellspacing=0 border=1>
      <tr><th>Key</th><th>Value</th></tr>
      <tr><td>vtid</td><td><?php echo $_POST["vtid"]; ?></td></tr>
      <tr><td>password</td><td><?php echo $_POST["password"]; ?></td></tr>
      <tr><td>card number</td><td><?php echo $_POST["num"]; ?></td></tr>
      <tr><td>expiry date</td><td><?php echo $_POST["exp"]; ?></td></tr>
      <tr><td>config file</td><td><?php echo $_POST["configfile"]; ?></td></tr>
      <tr><td>cardinfo directory</td><td><?php echo $_POST["pathtocardinfo"]; ?></td></tr>
    </table>

    <h2>2. Configuration File</h2>

    <p>The configuration file holds the hostname where the transaction will be sent, as well as other
      values which we might want to use.  If you want, you can view the information in the config file
      like this:</p>

    <?php
      $config = new DataCash_Document();
      $config->readDocumentFromFile( $_POST["configfile"] );
    ?>

    <table cellpadding=3 cellspacing=0 border=1>
      <tr><th>Key</th><th>Value</th></tr>
      <tr><td>host</td><td><?php echo $config->get( "Configuration.host" ); ?></td></tr>
      <tr><td>timeout</td><td><?php echo $config->get( "Configuration.timeout" ); ?></td></tr>
      <tr><td>cacert_location</td><td><?php echo $config->get( "Configuration.cacert_location" ); ?></td></tr>
    </table>

    <p>For full details of the various configuration options check the <a href="file:///C|/Users/ebird/Downloads/xampp-win32-1.8.1-VC9/xampp/documentation/configuration.html">Configuration documentation</a> provided with these libraries.</p>

    <h2>3. The XML Request Document</h2>

    <p>We build the XML Request document based on the values sent by the HTML form and some
      other hard-coded values.  Thereafter, we'll output it.

      <?php
      
      $send_doc = new DataCash_Document("Request");

      $send_doc->set( "Request.Authentication.client", $_POST["vtid"] );
      $send_doc->set( "Request.Authentication.password", $_POST["password"] );
      $send_doc->set( "Request.Transaction.TxnDetails.amount", "10.00", array('currency' => "GBP") );
      $send_doc->set( "Request.Transaction.TxnDetails.merchantreference", random_ref() );
	  
	  $send_doc->set( "Request.Transaction.PayPalTxn.email" , "accept@example.com");
	  $send_doc->set( "Request.Transaction.PayPalTxn.method", "set_express_checkout");
	  $send_doc->set( "Request.Transaction.PayPalTxn.return_url", "http://localhost/php/GetExpressCheckoutDetails.php");
	  $send_doc->set( "Request.Transaction.PayPalTxn.cancel_url", "http://localhost/php/index.php");
	  $send_doc->set( "Request.Transaction.PayPalTxn.description", "T-Shirt");
	  $send_doc->set( "Request.Transaction.PayPalTxn.payment_action", "sale");


      printf( "\n<pre>\n%s</pre>\n", getHtmlDocument( $send_doc ) );
      
      ?>
    
    <h2>4. CardInfo</h2>

    <p>The CardInfo library can tell you some information about the card.  Here is how to find out the 
      scheme, issuer and country for the card number input in the HTML form.</p>
      
    <p><span style="font-weight: bold">Note: </span>the default card number used in this example is NOT
      in the CardInfo files, therefore to see some values retrieved from the CardInfo files, you should
      use a 'real' card number.</p>

      <?php

    $cardinfo = new DataCash_CardInfo (array("datadir" => $_POST["pathtocardinfo"], "pan" => $send_doc->get("Request.Transaction.CardTxn.Card.pan")));

      ?>

    <table cellpadding=3 cellspacing=0 border=1>
      <tr><th>Scheme</th><td><?php echo $cardinfo->scheme(); ?></td></tr>
      <tr><th>Issuer</th><td><?php echo $cardinfo->issuer(); ?></td></tr>
      <tr><th>Country</th><td><?php echo $cardinfo->country(); ?></td></tr>
    </table>

    <h2>5. Validation</h2>

    <p>The PHP API can also perform some validation on the document before you send it to the DataCash
      Payment Gateway.  The default information provided are valid, so try entering a 'real' card number with
      transposed numbers which should therefore be invalid, or put in 'XYZ' for the expiry date.

      <?php
$validation = $cardinfo->validation( $send_doc );
if ( $validation != null ) {
  printf( "<p class=warning>Validation error:\n<pre>%s</pre></p>\n", getHtmlDocument( $validation ) );
    print "<p class=warning>Processing will not continue.  Go back and correct the validation errors!</p>\n";
  exit;
}
else {
  print "<p class=highlight>No validation errors found.</p>\n";
}
      ?>

    <h2>6. Sending the Transaction to the Payment Gateway</h2>

    <p>Next, we want to send the transaction to the Payment Gateway.  There are many reasons why this might
      not work, such as an incorrect value being put in the configuration file, or the transaction taking
      longer that the timeout value you have specified in the configuration file.

    <p>To emulate an error, you could alter the value of the 'Configuration.host' element in your
      configuration file to something that does not exist, or alternatively you could set the 
      Configuration.timeout value to less than 5 seconds and use the magic card which takes over 10 seconds
      to respond (currently 5473000000000460, subject to change).

    <?php
$agent = new DataCash_SSLAgent( $config );
$success = $agent->send( $send_doc );
if ( !$success ) {
  print "<p class=warning>Error in transaction: " . $agent->err_str . "</p>\n";
}
else {
  print "<p>No error received</p>\n";
}

?>

    <h2>7. The XML Response Document</h2>

    <p>Now we'll output the document and retrieve some values from it.</p>

    <?php
  $response_doc = $agent->getResponseDocument();
  printf( "\n<pre>\n%s</pre>\n", getHtmlDocument( $response_doc ) );
  printf( "<p class=highlight>Transaction result: %s (%s)</p>\n", $response_doc->get("Response.status"), 
          $response_doc->get("Response.reason") );
    
?>
      
  </body>
</html>
