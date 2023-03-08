<?php
error_reporting(1);
/* ***************************** EMAIL SENDING ***************************** */

function emailSending($senderName = "", $senderEmail = "", $recipientEmail = "", $subject = "", $msgContent = "", $attachment = null)
{
    $separator = md5(time());
    $eol = "\r\n";
    $headers = "From: " . $senderName . " <" . $senderEmail . ">" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
    $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
    $headers .= "This is a MIME encoded message." . $eol;
    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/html; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 8bit" . $eol;
    $body .= $msgContent . $eol;
    $body .= "--" . $separator . $eol;
    if ($attachment != null) {
        $encodedString = $attachment['EncodedString'];
        $filename = $attachment['FileName'];
        $content = $encodedString;
        $body .= "Content-Type:application/octet-stream; name=\"" . $filename . "\"" . $eol;
        $body .= "Content-Transfer-Encoding: base64" . $eol;
        $body .= "Content-Disposition: attachment" . $eol;
        $body .= $content . $eol;
        $body .= "--" . $separator . "--";
    }
    if (mail($recipientEmail, $subject, $body, $headers)) {
        $sendMail = true;
    } else {
        $sendMail = false;
    }
    return $sendMail;
}

/* ***************************** EMAIL SENDING ***************************** */

if (isset($_POST['sendOTP'])) {
    $otpSend = "";
    $sent = "";
    $className = "";
    $otpGenerate = rand(1000, 9999);
    $senderName = "<your name/company name>";
    $senderEmail = "<your email from which you want to send the mail>";
    $recipientEmail = trim($_POST['email_id']);
    $subject = "Please confirm the OTP";
    $msgContent = "Your company's <b>OTP code</b> - " . $otpGenerate;
    $attachment = null;

    if (emailSending($senderName, $senderEmail, $recipientEmail, $subject, $msgContent, $attachment)) {
        $otpSend = "Successfylly sent OTP by email";
        $className = "success";
        $sent = 1;
    } else {
        $otpSend = "Could not send OTP by email";
        $className = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP sending through Email and verifying</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./style.css" />
</head>
<body>
<div class="container">
<form class="form-signin" action="" method="post">
  <h2 class="form-signin-heading">Provide email for OTP</h2>
  <label for="inputEmail" class="sr-only">Email address</label>
  <input type="email" id="inputEmail" class="form-control" name="email_id" placeholder="Email address" required autofocus>
  <b class="text-<?php echo $className; ?>"><?php echo $otpSend; ?></b><br>
  <button class="btn btn-lg btn-primary btn-block" type="submit" name="sendOTP" id="sendOTP">Generate OTP</button>
</form>
<?php
if ($sent == 1) {
    ?>
<div class="form-signin">
<h2 class="form-signin-heading">Verify OTP</h2>
<label for="inputOTP" class="sr-only">OTP</label>
<input type="hidden" name="hotp" id="hotp" class="form-control" placeholder="Enter OTP" value="<?php echo $otpGenerate; ?>" />
<input type="text"  name="otp" id="otp" class="form-control" placeholder="Enter OTP"  value="" />
  <b id="message"></b><br>
  <button class="btn btn-lg btn-primary btn-block" type="button" name="verify" id="verify">Verify</button>
</div>
<?php
}
?>
</div> <!-- /container -->
</body>
<script src="./jquery.min.js"></script>
<script>
$(document).ready(function(){
   $("#verify").click(function(){
   let hOtp = $("#hotp").val();
   let otp = $("#otp").val();
   if((hOtp === null) || (hOtp === "") || (otp === null) || (otp === "")){
    $("#message").text("Provide OTP");
   }else if(hOtp !== otp){
    $("#message").text("OTP entered is wrong");
   }else{
    $("#hotp").val("");
    $("#otp").val("");
    $("#message").text("Successfully verified");
   }
 });
});
</script>
</html>
