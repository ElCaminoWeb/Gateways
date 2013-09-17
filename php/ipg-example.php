<!DOCTYPE html>
<?php include("ipg-util.php"); ?>

<html>
<head><title>IPG Connect Sample for PHP</title></head>
	<body>
	<p><h1>Order Form</h1>

<form method="post" action="https://test.ipg-online.com/connect/gateway/processing">
	<input type="hidden" name="txntype" value="sale">
        <input type="hidden" name="timezone" value="GMT"/>
	<input type="hidden" name="txndatetime" value="<?php echo getDateTime() ?>"/>
	<input type="hidden" name="hash" value="<?php echo createHash( "13.00","826" ) ?>"/>
	<input type="hidden" name="storename" value="13205400147"/>
        <input type="hidden" name="mode" value="payplus"/>
        <input type="text" name="chargetotal" value="13.00"/>
        <input type="hidden" name="currency" value="826"/>
        <input type="hidden" name="language" value="en_GB"/>
        <input type="hidden" name="responseSuccessURL" value="http://www.example.com"/>
        <input type="hidden" name="responseFailURL" value="http://www.example.com" />
        
        <!-- <input type="hidden" name="mobileMode" value="true" /> -->
        <!-- <input type="hidden" name="paymentMethod" value="paypal" /> -->
	<input type="submit" value="Submit">
	</form>
	</body>
</html>
