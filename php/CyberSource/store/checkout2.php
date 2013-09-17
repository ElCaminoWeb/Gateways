<?php
/* checkout2.php ********************************************************/
/*                                                                      */
/* Copyright 2004. CyberSource Corporation.  All rights reserved.       */
/************************************************************************/

session_start();
require 'util.php';
?>

<HTML>
<HEAD>

<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE> Order Status </TITLE>

</HEAD>
<BODY>

<?php include 'store_header.php';

	// set up the request by creating an array and adding fields to it
	$request = array();
	
	$request['ccAuthService_run'] = "true";

	// we will let the CyberSource PHP extension get the merchantID from
	// $_SESSION['CONFIG'] and insert it into $request.

	// this is your own tracking number.  This sample uses a hardcoded value.
	// CyberSource recommends that you use a unique one for each order.
	$request['merchantReferenceCode'] = "your_merchant_reference_code";
	
	$request['billTo_firstName'] = $_REQUEST['billTo_firstName'];
	$request['billTo_lastName'] = $_REQUEST['billTo_lastName'];
	$request['billTo_street1'] = $_REQUEST['billTo_street1'];
	$request['billTo_city'] = $_REQUEST['billTo_city'];
	$request['billTo_state'] = $_REQUEST['billTo_state'];
	$request['billTo_postalCode'] = $_REQUEST['billTo_postalCode'];
	$request['billTo_country'] = $_REQUEST['billTo_country'];
	$request['billTo_email'] = $_REQUEST['billTo_email'];
	$request['billTo_phoneNumber'] = $_REQUEST['billTo_phoneNumber'];
	$request['shipTo_firstName'] = $_REQUEST['shipTo_firstName'];
	$request['shipTo_lastName'] = $_REQUEST['shipTo_lastName'];
	$request['shipTo_street1'] = $_REQUEST['shipTo_street1'];
	$request['shipTo_city'] = $_REQUEST['shipTo_city'];
	$request['shipTo_state'] = $_REQUEST['shipTo_state'];
	$request['shipTo_postalCode'] = $_REQUEST['shipTo_postalCode'];
	$request['shipTo_country'] = $_REQUEST['shipTo_country'];
	$request['card_accountNumber'] = $_REQUEST['card_accountNumber'];
	$request['card_expirationMonth'] = $_REQUEST['shipTo_country'];
	$request['card_expirationYear'] = $_REQUEST['shipTo_country'];
	$request['purchaseTotals_currency'] = "USD";

	// extract credit card expiration month and year
	$monthyear = preg_split( "/\//", $_REQUEST['card_exp'] );
	$request['card_expirationMonth'] = $monthyear[0];
	$request['card_expirationYear'] = $monthyear[1];
	
	// obtain visitor's IP Address.  This is a simplistic method.  Please refer
	// to the suggestions made by other PHP developers on the getenv
	// documentation page at http://us4.php.net/getenv
	$ipAddress = '';
	if (isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ))
		$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	elseif (isset( $_SERVER['REMOTE_ADDR'] ))
		$ipAddress = $_SERVER['REMOTE_ADDR'];

	if ($ipAddress <> '')
		$request['billTo_ipAddress'] = $ipAddress;
	
	// set the line item fields using information in the shopping basket
	$basket = unserialize( $_SESSION['BASKET'] );
	$i = 0;
	foreach ($basket->items as $item)
	{
		$request['item_' . $i . '_productName'] = $item->productName;
		$request['item_' . $i . '_productSKU'] = $item->productSKU;
		$request['item_' . $i . '_unitPrice'] = $item->amount;
		$request['item_' . $i . '_quantity'] = $item->quantity;
		++$i;
	}
	
	// add other fields here per your business needs

	// send request now
	$reply = array();
	$status = cybs_run_transaction( $_SESSION['CONFIG'], $request, $reply );
	
	// write user-friendly message
	writeUserMessage( $status, $reply );
	echo "<p align='center'><a href='checkout.php'>Back</a></p>";
	echo '<hr>';
	
	// write internal-personnel-friendly message
	writeInternalMessage( $status, $request, $reply );
?>

<?php include 'store_footer.php'; ?>

</BODY>

</HTML>

<?php

// ----------------------------------------------------------------------------
// These are sample messages to be displayed to the shopper.  Modify them to
// suit your needs.
// ----------------------------------------------------------------------------
function writeUserMessage( $status, $reply )
// ----------------------------------------------------------------------------
{	
	echo '<h3> User-Friendly Message: </h3><p>';
	
	switch( $status )
	{
		// successful transmission (not necessarily a successful transaction)
		case CYBS_S_OK:
			$decision = strtoupper( $reply['decision'] );
			
			if ($decision == "ACCEPT")
				echo 'Thank you!  Your order has been received.';
				
			elseif ($decision == "REVIEW")
				echo 'There was a problem completing your order.  We apologize for the inconvenience.  Please contact customer support to review your order.';
				
			else
			{    // REJECT or ERROR
				 // Here, you can customize the message depending on the
				 // reasonCode.  This sample only handles 204 (Insufficient
				 // funds) specifically and handles the rest generically.
				
				$reasonCode = $reply['reasonCode'];
				
				switch ($reasonCode)
				{
					case '204':
						echo 'There are insufficient funds in your account.  Please use a different credit card.';
						break;
					default:
						if ($decision == 'REJECT')
							echo 'Your order could not be completed.  Please review the information you entered and try again.';
						else // ERROR
							echo 'Your order could not be completed.  We apologize for the inconvenience.  Please try again at a later time.';
				}
			}
			break;

		// non-critical errors			
		case CYBS_S_PHP_PARAM_ERROR:
		case CYBS_S_PRE_SEND_ERROR:
		case CYBS_S_SEND_ERROR:
		case CYBS_S_SERVER_FAULT:
		case CYBS_S_OTHER_FAULT:
		case CYBS_S_HTTP_ERROR:
			echo 'Your order could not be completed.  We apologize for the inconvenience.  Please try again at a later time.';
			break;
		
		// critical errors
		case CYBS_S_RECEIVE_ERROR:
		case CYBS_S_POST_RECEIVE_ERROR:
		case CYBS_S_CRITICAL_SERVER_FAULT:
			echo 'There was a problem completing your order.  We apologize for the inconvenience.  Please contact customer support to review your order.';
			break;	
	}
	
	echo '</p>';
}


// ----------------------------------------------------------------------------
// These are messages for internal personnel and are rendered on the page only
// to show what the error data will look like.
// ----------------------------------------------------------------------------
function writeInternalMessage( $status, $request, $reply )
// ----------------------------------------------------------------------------
{
	echo "<table bgcolor='#EEEEEE' width='100%'><tr><td>";
	echo '<h3> Internal Message: </h3><p>';
	
	switch ($status)
	{
		case CYBS_S_OK:
		
			$decision = strtoupper( $reply['decision'] );
			
			if ($decision == "REVIEW")
				
				handleReview( $request, $reply );
			
			else
			{
				echo '<p><b>CREDIT CARD AUTHORIZATION REQUEST:</b></p>';
				echo getArrayContent( $request );
				echo '<br>';
					 
				echo '<p><b>CREDIT CARD AUTHORIZATION REPLY:</b></p>';
				echo getArrayContent( $reply );
				echo '<br>';
			}
			break;
	
		case CYBS_S_PHP_PARAM_ERROR:
			printf( "Please check the parameters passed to cybs_run_transaction for correctness.<br>" );
			break;
		
		case CYBS_S_PRE_SEND_ERROR:
			printf(	"The following error occurred before the request could be sent:<br>%s<br>",
				    $reply[CYBS_SK_ERROR_INFO] );
			break;
		
		case CYBS_S_SEND_ERROR:
			printf( "The following error occurred while sending the request:<br>%s<br>",
				    $reply[CYBS_SK_ERROR_INFO] );
			break;

		case CYBS_S_RECEIVE_ERROR:
			printf( "The following error occurred while waiting for or retrieving the reply:<br>%s<br>",
				    $reply[CYBS_SK_ERROR_INFO] );
			handleCriticalError( $status, $request, $reply );
			break;

		case CYBS_S_POST_RECEIVE_ERROR:
			printf(	"The following error occurred after receiving and during processing of the reply:<br>%s<br>",
				    $reply[CYBS_SK_ERROR_INFO] );
			handleCriticalError( $status, $request, $reply );
			break;		

		case CYBS_S_CRITICAL_SERVER_FAULT:
			printf( "The server returned a CriticalServerError fault:<br>%s<br>",
					getFaultContent( $reply ) );
			handleCriticalError( $status, $request, $reply );
			break;
		
		case CYBS_S_SERVER_FAULT:
			printf( "The server returned a ServerError fault:<br>%s<br>",
					getFaultContent( $reply ) );
			break;

		case CYBS_S_OTHER_FAULT:
			printf( "The server returned a fault:<br>%s<br>",
					getFaultContent( $reply ) );
			break;
 
		Case CYBS_S_HTTP_ERROR:
			printf(	"An HTTP error occurred:<br>%s<br>Response Body:<br>%s<br>",
				    $reply[CYBS_SK_ERROR_INFO], $reply[CYBS_SK_RAW_REPLY] );
			break;
	}
	
	echo '</p></td></tr></table>';
}


//-----------------------------------------------------------------------------
// If an error occurs after the request has been sent to the server, but the
// client can't determine whether the transaction was successful, then the
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
			"STATUS: %d<br>ERROR INFO: %s<br>REQUEST: <br>%s<br>%s<br>%s<br>",
			$status, $reply[CYBS_SK_ERROR_INFO],
			print_f( $request, true ), $replyType, $replyText );
		  
	// send $messageToSend to the appropriate personnel at your company
	// using any suitable method, e.g. e-mail, multicast log, etc.
	//
	// This sample code simply sends it to the browser.

	printf( "<br>This is a critical error.  Send the following information to the appropriate personnel at your company: <br>%s<br>",
			$messageToSend );
}		

				
// ----------------------------------------------------------------------------
// If you use CyberSource Decision Manager, you may also receive the REVIEW
// value for the decision field. A REVIEW means that Decision Manager has
// flagged the order for review based on how you configured the Decision
// Manager rules.  This sample treats the REVIEW as a REJECT.  This procedure
// is a placeholder for sending the request and reply information to the
// appropriate personnel at your company.  They should then review the order.
// Please consult the section "Handling Reviews" in the accompanying
// developer's guide for detailed information.
// ----------------------------------------------------------------------------
function handleReview( $request, $reply )
// ----------------------------------------------------------------------------
{
	$messageToSend
		= sprintf( 
			"A decision of REVIEW was received on the following order:<br>REQUEST:<br>%s<br>REPLY:<br>%s<br>",
			getArrayContent( $request ), getArrayContent( $reply ) );
			
  
	// send $messageToSend to the appropriate personnel at your company
	// using any suitable method, e.g. e-mail, multicast log, etc.
	//
	// This sample code simply sends it to the browser.

	printf( "<br>Send the following information to the appropriate personnel at your company:<br>%s<br>",
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
		"Fault code: %s<br>Fault string: %s<br>RequestID: %s<br>Fault document: %s",
		$reply[CYBS_SK_FAULT_CODE], $reply[CYBS_SK_FAULT_STRING],
		$requestID, $reply[CYBS_SK_FAULT_DOCUMENT] ) );
}

// Copyright 2004. CyberSource Corporation.  All rights reserved.
?>
