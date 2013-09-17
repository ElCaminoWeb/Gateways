<?php
	error_reporting(E_ALL ^ E_NOTICE);
	include("C:/wamp/www/Gateways/DataCash/DataCash.php");

	function getHtmlDocument ( $xmldoc ) {
    	$stringDocument = $xmldoc->getDocument();
    	$stringDocument = preg_replace( '/</', '&lt;', $stringDocument );
    	$stringDocument = preg_replace( '/>/', '&gt;', $stringDocument );
    	return $stringDocument;
	}
	
	$config = new DataCash_Document();
    $config->readDocumentFromFile("php-transaction.conf");

	$send_doc = new DataCash_Document();
	$send_doc->readDocumentFromFile("SetEC.xml");

	$agent = new DataCash_SSLAgent( $config );
	$success = $agent->send( $send_doc );
	$response_doc = $agent->getResponseDocument();
	printf( "\n<pre>\n%s</pre>\n", getHtmlDocument( $response_doc ) );
  	printf( "<p class=highlight>Transaction result: %s (%s)</p>\n", $response_doc->get("Response.status"), $response_doc->get("Response.reason") );

?>