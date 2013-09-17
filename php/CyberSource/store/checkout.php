<?php
/* checkout.php *********************************************************/
/*                                                                      */
/* Copyright 2004. CyberSource Corporation.  All rights reserved.       */
/************************************************************************/

session_start();

require 'util.php';

// create hardcoded shopping basket and save it in session
$basket = new Basket;
$basket->addItem( "test product 1", "TP-001", 215.00, 1 );
$basket->addItem( "test product 2", "TP-002", 1.00, 5 );
$basket->addItem( "test product 3", "TP-003", 10.00, 2 );
$_SESSION['BASKET'] = serialize( $basket );

// load the config settings.  Alternatively, you could create an array and add
// the config settings one by one.  You could also combine the two methods,
// i.e. load the config settings from a file and overwrite some of the 
// settings afterwards.
$config = cybs_load_config( 'cybs.ini' );
$_SESSION['CONFIG'] = $config;
echo $config['merchantID'];

?>

<HTML>
<HEAD>

<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE> Checkout </TITLE>
<STYLE>
	.numeric {text-align:right}
</STYLE>

</HEAD>
<BODY>

<?php include 'store_header.php'; ?>

<H1> Checkout </H1>

Here are the items in your shopping basket: <BR><BR>

<TABLE FRAME="border" width="600">
	<TR>
		<TH>Product Name</TH>
		<TH>Product SKU</TH>
		<TH>Price</TH>
		<TH>Quantity</TH>
		<TH>Total</TH>
	</TR>
<?php
	$grandTotal = 0;

	foreach ($basket->items as $item)
	{
		$itemTotal = $item->amount * $item->quantity;
		$grandTotal = $grandTotal + $itemTotal;
?>
		<TR>
			<TD><?php echo $item->productName; ?></TD>
			<TD><?php echo $item->productSKU; ?></TD>
			<TD CLASS=numeric>$<?php echo round($item->amount,2);?></TD>
			<TD CLASS=numeric><?php echo $item->quantity; ?></TD>
			<TD CLASS=numeric>$<?php echo round($itemTotal,2);?></TD>
		</TR>
<?php
	}
?>
		<TR>
			<TD COLSPAN=4 STYLE="font-style:italic;text-align:right">Your total:</TD>
			<TD CLASS=numeric STYLE="font-weight:bold">$<?php echo round($grandTotal,2);?></TD>
		</TR>
</TABLE>

<FORM ACTION="checkout2.php" METHOD=post>

<table width="600">
<tr><td colspan=3>
Please confirm the information below and click the Submit button to perform the
authorization.
<br><br>
</td></tr>
<tr>
<td>
	<h3>Billing Information</h3>
	First Name:<BR>
	<INPUT TYPE=text NAME="billTo_firstName" VALUE="John"></INPUT>

	<BR>Last Name:<BR>
	<INPUT TYPE=text NAME="billTo_lastName" VALUE="Doe"></INPUT>

	<BR>Street Address:<BR>
	<INPUT TYPE=text NAME="billTo_street1" VALUE="1295 Charleston Road"></INPUT>

	<BR>City:<BR>
	<INPUT TYPE=text NAME="billTo_city" VALUE="Mountain View"></INPUT>

	<BR>State:<BR>
	<INPUT TYPE=text NAME="billTo_state" VALUE="CA"></INPUT>

	<BR>Zip:<BR>
	<INPUT TYPE=text NAME="billTo_postalCode" VALUE="94043"></INPUT>

	<BR>Country:<BR>
	<SELECT NAME="billTo_country">
	<OPTION VALUE="us">United States
	<OPTION VALUE="ca">Canada
	</SELECT>
	<BR>
	
	<BR>Email Address:<BR>
	<INPUT TYPE=text NAME="billTo_email" VALUE="nobody@cybersource.com"></INPUT>

	<BR>Phone:<BR>
	<INPUT TYPE=text NAME="billTo_phoneNumber" VALUE="650-965-6000"></INPUT>

</td>
<td  valign="top">
	<h3>Shipping Information</h3>
	
	First Name:<BR>
	<INPUT TYPE=text NAME="shipTo_firstName" VALUE="Jane"></INPUT>

	<BR>Last Name:<BR>
	<INPUT TYPE=text NAME="shipTo_lastName" VALUE="Doe"></INPUT>

	<BR>Street Address:<BR>
	<INPUT TYPE=text NAME="shipTo_street1" VALUE="100 Elm Street"></INPUT>

	<BR>City:<BR>
	<INPUT TYPE=text NAME="shipTo_city" VALUE="San Mateo"></INPUT>

	<BR>State:<BR>
	<INPUT TYPE=text NAME="shipTo_state" VALUE="CA"></INPUT>

	<BR>Zip:<BR>
	<INPUT TYPE=text NAME="shipTo_postalCode" VALUE="94401"></INPUT>

	<BR>Country:<BR>
	<SELECT NAME="shipTo_country">
	<OPTION VALUE="us">United States
	<OPTION VALUE="ca">Canada
	</SELECT>
</td>
<td valign="top">
	<h3>Credit Card Information</h3>
		Credit Card	Number:<BR>
		<INPUT TYPE=text NAME="card_accountNumber" VALUE="4111111111111111"></INPUT>

		<BR>Expiration (mm/yyyy):<BR>
		<INPUT TYPE=text NAME="card_exp" VALUE="12/2020"></INPUT>
</td>
</tr>
<tr>
<td></td>
<td align="center">
	<INPUT TYPE=submit VALUE="       Submit       "></INPUT>
</td>
</tr>
</table>

</FORM>

<hr>
<table bgcolor="#EEEEEE" width="100%">
<tr><td>
<h3>$config:</h3>
<?php
	echo getArrayContent( $config );
?>
</td></tr>
</table>

<?php include 'store_footer.php'; ?>

</BODY>
</HTML>

<?php // Copyright 2004. CyberSource Corporation.  All rights reserved. ?>
