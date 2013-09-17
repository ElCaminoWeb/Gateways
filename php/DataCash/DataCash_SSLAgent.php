<?php
/**
 * This module performs the actual SSL transportation of the XML document to the DataCash Payment Gateway for authorisation.
 *
 * @internal $Id: DataCash_SSLAgent.php,v 1.20 2007/02/28 14:19:59 sxf Exp $
 *
 * @package DataCash
 * @author Kenny Scott, Dave MacRae
 * @copyright DataCash Group plc 2003
 */

$CVSid = explode (" ", '$Id: DataCash_SSLAgent.php,v 1.20 2007/02/28 14:19:59 sxf Exp $');
$included_classes[basename($CVSid[1], '.php,v')] = $CVSid[2];


/**
 * This class performs the SSL transportation of the XML document to the DataCash Payment Gateway.
 *
 * @package DataCash
 * @access public
 */
class DataCash_SSLAgent {


  // instead of having the config file passed as a paremeter, we should look at having
  // it as a global as in the logger function.

  /**
   * DataCash_SSLAgent constructor.
   *
   * Pass the DataCash API configuration file as a parameter.
   *
   * @access public
   * @param DataCash_Document $config DataCash API configuration file
   */
  function DataCash_SSLAgent ($config) {
    $this->config = $config;
  }
  
  /**
   * Send the XML request to the DataCash Payment Gateway.  
   *
   * Returns true if the request was sent successfully and a good response was received, false otherwise.
   *
   * @access public
   * @param DataCash_Document $xmldoc XML request
   * @return boolean 
   */
  function send ( $xmldoc ) {

    // Set the User Agent fields before sending it to the DataCash Payment Gateway
    DataCash_UserAgent( $xmldoc );
    $ch = curl_init();
    curl_setopt ( $ch, CURLOPT_URL, $this->config->get( "Configuration.host" ) );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $xmldoc->getDocument() );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, array('Expect: ') );
    //
    // This next line says that we want the response saved, not directly printed to the browser.
    // This should probably be a configuration option.
    //
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //
    // There is olso an option in curl for it to check that the
    // host certificate matches. The configuration vaiable tests this
    //
 //   if ($this->config->get("Configuration.SSL.verify") != "") {
 //     curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, $this->config->get("Configuration.SSL.verify"));
 		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  //  }
    //
    // we should also use the configuration variables to define timeouts
    //
    if ($this->config->get("Configuration.timeout") != "") {
      curl_setopt ($ch, CURLOPT_TIMEOUT, $this->config->get("Configuration.timeout"));
    } else {
      curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    }

    //
    // Set some certification related options
    //
 //   $cert_loc = $this->config->get("Configuration.cacert_location");
 //   if ($cert_loc != "") {
      //We've a certificate location. Check whether it's exists.
 //     if (!file_exists($cert_loc)) {
  //$this->err_str = "Cannot find cacert_location: ".$cert_loc;
 // dc_log(1, $this->err_str);
  //return(false);
   //   }
      
      //Can we read it?
//      if (!is_readable($cert_loc)) {
 // $this->err_str = "Cannot read cacert_location: ".$cert_loc;
  //dc_log(1, $this->err_str);
 // return(false);
  //    }

      //Set the various options
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   //   curl_setopt($ch, CURLOPT_CAINFO, $cert_loc);

   // }

    //
    // Save the response in the response variable
    //
    $this->response = curl_exec ($ch);
    $this->errno = curl_errno ($ch);
    $this->err_str = curl_error ($ch);
    $this->curl_getinfo = curl_getinfo( $ch );

    /*
    $keys = array_keys( $this->curl_getinfo );
    foreach ( $keys as $key ) {
      printf( "The %s is [%s]\n", $key, $this->curl_getinfo[$key] );
    }
    */

    curl_close ($ch);

    if ($this->errno > 0) {
      dc_log(1, "DataCash_SSLAgent: Curl Errno = " . $this->errno . "\n");
      return( false );
    }

    else {
      $this->responseDocument = new DataCash_Document();
      $this->responseDocument->readDocumentFromString( $this->response );

      /* It's possible, although unlikely, for the transaction to succeed but for an
       * HTTP Response code to not be 200, such as a warning code.  Therefore, we don't
       * want to assume that the transaction failed if a 200 was not received.  What
       * we will do is see if the Response.status element isn't there, and if not, if
       * the HTTP Response code is also not 200, then we can safely assume that things
       * didn't go according to plan and we didn't get an XML Response document back.
       */

      if ( ( $this->responseDocument->get( "Response.status" ) == "" ) && ( $this->curl_getinfo["http_code"] != 200 ) ) {
  dc_log( 1, "DataCash_SSLAgent: HTTP Error code was " . $this->curl_getinfo["http_code"] );
  $this->err_str = "HTTP Error: Response Code " . $this->curl_getinfo["http_code"] . " received.";
  $this->errno = $this->curl_getinfo["http_code"];
  return( false );
      }

      /* If we got here, then the Response.status element had something in it, so
       * it worked. */
      return( true );
    }
  }
  
  /**
   * Return the response from the DataCash Payment Gateway.
   *
   * @access public
   * @return DataCash_Document 
   */
  function getResponseDocument ( ) {
    return $this->responseDocument;
  }

}

?>
