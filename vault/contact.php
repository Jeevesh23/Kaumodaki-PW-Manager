<?php
include_once('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if (isset($_POST["email"]) && (!empty($_POST["email"]))) {
    $email = $_POST["email"];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $name = $_POST["name"];
    $message = $_POST["message"];
    if (!$email) {
        header("Refresh:3,url=/authentication/contact");
        echo "Invalid email address!";
        exit();
    } else {
        $DEV_1 = 'bWFoZXByaW9sMTMxMw==';
        $DEV_2 = 'amVldmVzaC5uYWlkdTIzMDQ=';
        $output = '<p>Message recieved from ' . $name . ' !</p>';
        $output .= '<p>Email: ' . $email . '</p>';
        $output .= "<p>$message</p>";
        $body = $output;
        $subject = "Password Manager Contact";
        $mail = new PHPMailer();
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->IsSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL');
        $mail->Password = getenv('EMAIL_APP_PASSWORD');
        $mail->IsHTML(true);
        $mail->setFrom(getenv('EMAIL'), 'Password Contact Bot');
        $mail->addCC(base64_decode($DEV_1) . '@gmail.com', 'SorcierMaheP');
        $mail->addCC(base64_decode($DEV_2) . '@gmail.com', 'P Jeevesh');
        $mail->Subject = $subject;
        $mail->Body = $body;
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            header("Refresh:3,url=/vault");
            echo
            "
              <!DOCTYPE html>
              <html lang='en'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Document</title>
              </head>
              <body>
                  <div>
                  <h2>Feedback email has been sent!</h2>
                  </div>
              </body>
              </html>";
            exit();
        }
    }
}
