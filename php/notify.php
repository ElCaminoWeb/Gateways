<?php
$email = $_REQUEST['email'];
$req = "";
foreach ($_REQUEST as $key => $value) {
    $req .= $key . ": " . $value. PHP_EOL; 
}
require("./Mailer/class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();                                      // set mailer to use SMTP
$mail->Port = "587";  // specify main and backup server
$mail->Host = "smtp.gmail.com";  // specify main and backup server
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "ebird.pp@gmail.com";  // SMTP username
$mail->Password = "i.o.w.154"; // SMTP password
$mail->SMTPSecure = "tls"; 

$mail->SMTPDebug = 2;

$mail->From = "ebird.pp@gmail.com";
$mail->FromName = "SagePay Notifier";
$mail->AddAddress($email);                  // name is optional

$mail->WordWrap = 50;                                 // set word wrap to 50 characters
$mail->IsHTML(false);                                  // set email format to HTML

$mail->Subject = "SagePay Notfication";
$mail->Body    = $req;
//echo $req;
//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Message has been sent";
?>