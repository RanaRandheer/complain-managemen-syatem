<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer\src\Exception.php';
require 'PHPMailer\src\PHPMailer.php';
require 'PHPMailer\src\SMTP.php';

function reply($replytoaddress)
{
  $mail = new PHPMailer;
  $mail->isSMTP();
  $mail->SMTPSecure = 'ssl';
  $mail->SMTPAuth = true;
  $mail->Host = 'smtp.gmail.com';
  $mail->Port = 465;
  $mail->Username = 'userid@host';
  $mail->Password = 'password';
  $mail->setFrom('userid@host');
  $mail->addAddress($replytoaddress);
  $mail->Subject = 'Hello from PHPMailer!';
  $mail->Body = 'This is a test.';
  
  //send the message, check for errors
  if (!$mail->send())
    echo "ERROR: " . $mail->ErrorInfo;
  else
    echo "SUCCESS";
}

reply($replytoaddress);

?>
