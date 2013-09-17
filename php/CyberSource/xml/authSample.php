<?php
/* authSample.php ************************************************************/
/*                                                                           */
/* Copyright 2004. CyberSource Corporation.  All rights reserved.            */
/*****************************************************************************/

/*-----------------------------------------------------------------------------
This sample demonstrates how to use the CyberSource Simple Order API for PHP to
run a Credit Card Authorization transaction with an XML document as input.

You must update the properties in cybs.ini before running this script.
-----------------------------------------------------------------------------*/

// load the config settings.  Alternatively, you could create an array and add
// the config settings one by one.  You could also combine the two methods,
// i.e. load the config settings from a file and overwrite some of the 
// settings afterwards.
$config = cybs_load_config( 'cybs.ini' );

runAuth( $config );
  	  		
//-----------------------------------------------------------------------------
function runAuth( $config )
//-----------------------------------------------------------------------------
{
	// this sample loads the input XML document from a file.
	$inputXML = getFileContent( "auth.xml" );

	// replace _APIVERSION_ with the value of targetAPIVersion in the $config
	// array.  Note that this is only done here so that you will not need to
	// modify the sample file auth.xml directly when switching to another API
	// version.  In your actual XML documents, the namespace URI should already
	// have the correct targetted API version.
	$inputXML
		= str_replace(
			"_APIVERSION_", $config[CYBS_C_TARGET_API_VERSION], $inputXML );

	// set up the request by creating an array and adding CYBS_SK_XML_DOCUMENT
	$request = array();
	$request[CYBS_SK_XML_DOCUMENT] = $inputXML;

	printf( "CREDIT CARD AUTHORIZATION REQUEST: \n%s\n",
		$inputXML );

	// send request now
	$reply = array();
	$status = cybs_run_transaction( $config, $request, $reply );
				
	if ($status == CYBS_S_OK)
	{
		printf( "CREDIT CARD AUTHORIZATION REPLY: \n%s\n",
			$reply[CYBS_SK_XML_DOCUMENT] );
	}	
	else
	{
		handleError( $status, $request, $reply );
		return( '' );
	}
}					

//-----------------------------------------------------------------------------
function handleError( $status, $request, $reply )
//-----------------------------------------------------------------------------
{
	echo "RunTransaction Status: $status\n";

	switch ($status)
	{
		case CYBS_S_PHP_PARAM_ERROR:
			printf( "Please check the parameters passed to cybs_run_transaction for correctness.\n" );
			break;
		
		case CYBS_S_PRE_SEND_ERROR:
			printf(	"The following error occurred before the request could be sent:\n%s\n",
				    $reply[CYBS_SK_ERROR_INFO] );
			break;
		
		case CYBS_S_SEND_ERROR:
			printf( "The following error occurred while sending the request:\n%s\n",
				    $reply[CYBS_SK_ERROR_INFO] );
			break;

		case CYBS_S_RECEIVE_ERROR:
			printf( "The following error occurred while waiting for or retrieving the reply:\n%s\n",
				    $reply[CYBS_SK_ERROR_INFO] );
			handleCriticalError( $status, $request, $reply );
			break;

		case CYBS_S_POST_RECEIVE_ERROR:
			printf(	"The following error occurred after receiving and during processing of the reply:\n%s\n",
				    $reply[CYBS_SK_ERROR_INFO] );
			handleCriticalError( $status, $request, $reply );
			break;		

		case CYBS_S_CRITICAL_SERVER_FAULT:
			printf( "The server returned a CriticalServerError fault:\n%s\n",
					getFaultContent( $reply ) );
			handleCriticalError( $status, $request, $reply );
			break;
		
		case CYBS_S_SERVER_FAULT:
			printf( "The server returned a ServerError fault:\n%s\n",
					getFaultContent( $reply ) );
			break;

		case CYBS_S_OTHER_FAULT:
			printf( "The server returned a fault:\n%s\n",
					getFaultContent( $reply ) );
			break;
 
		Case CYBS_S_HTTP_ERROR:
			printf(	"An HTTP error occurred:\n%s\nResponse Body:\n%s\n",
				    $reply[CYBS_SK_ERROR_INFO], $reply[CYBS_SK_RAW_REPLY] );
			break;
	}
}

//-----------------------------------------------------------------------------
// If an error occurs after the request has been sent to the server, but the
// client can//t determine whether the transaction was successful, then the
// error is considered critical.  If a critical error happens, the transaction
// may be complete in the CyberSource system but not complete in your order
// system.  Because the transaction may have been successfully processed by
// CyberSource, you should not resend the transaction, but instead send the
// error information and the order information (customer name, order number,
// etc.) to the appropriate personnel at your company.  They should use the
// information as search criteria within the CyberSource Transaction Search
// Screens to find the transaction and determine if it was successfully
// processed. If it was, you should update your order system with the
// transaction information. Note that this is only a recommendation; it may not
// apply to your business model.
//-----------------------------------------------------------------------------
function handleCriticalError( $status, $request, $reply )
//-----------------------------------------------------------------------------
{
	$replyType = '';
	$replyText = '';
	
	if ($status == CYBS_S_CRITICAL_SERVER_FAULT)
	{
		$replyType = 'FAULT DETAILS: ';
		$replyText = getFaultContent( $reply );
	}
	else
	{
		$replyText = $reply[CYBS_SK_RAW_REPLY];
		if ($replyText <> '')
			$replyType = 'RAW REPLY: ';
		else
			$replyType = "No Reply available.";
	}

	$messageToSend
		= sprintf( 
			"STATUS: %d\nERROR INFO: %s\nREQUEST: \n%s\n%s\n%s\n",
			$status, $reply[CYBS_SK_ERROR_INFO],
			getArrayContent( $request ), $replyType, $replyText );
		  
	// send $messageToSend to the appropriate personnel at your company
	// using any suitable method, e.g. e-mail, multicast log, etc.
	//
	// This sample code simply sends it to standard output.

	printf( "\nThis is a critical error.  Send the following information to the appropriate personnel at your company: \n%s\n",
			$messageToSend );
}		

				
//-----------------------------------------------------------------------------
function getFaultContent( $reply )
//-----------------------------------------------------------------------------
{
	$requestID = $reply[CYBS_SK_FAULT_REQUEST_ID];
	if ( $requestID == "")
		$requestID = "(unavailable)";
	
	return( sprintf(
		"Fault code: %s\nFault string: %s\nRequestID: %s\nFault document: %s",
		$reply[CYBS_SK_FAULT_CODE], $reply[CYBS_SK_FAULT_STRING],
		$requestID, $reply[CYBS_SK_FAULT_DOCUMENT] ) );
}

//-----------------------------------------------------------------------------
function getArrayContent( $arr )
//-----------------------------------------------------------------------------
{
        $content = '';
        while (list( $key, $val ) = each( $arr ))
        {
                $content = $content . $key . ' => ' . $val . "\n";
        }

        return( $content );
}

//-----------------------------------------------------------------------------
function getFileContent( $filename )
//-----------------------------------------------------------------------------
{
	$handle = fopen( $filename, "r" );
	$content = fread( $handle, filesize( $filename ) );
	fclose($handle);

	return( $content );
}

?>
