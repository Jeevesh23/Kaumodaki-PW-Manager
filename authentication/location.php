<?php
session_start();
require('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);
date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $api_key = getenv('OPENCAGE_API_KEY');
    $geocoder = new \OpenCage\Geocoder\Geocoder($api_key);
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $result = $geocoder->geocode($latitude . ',' . $longitude);

    $username = $_SESSION['Username'];
    $output .= '<p>Dear user ' . $username . ', thanks for using our password manager!</p>';
    $output .= '<p>A new sign in attempt was successful.</p>';
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p>Latitude: ' . $latitude . ' Longitude: ' . $longitude . '</p>';
    $output .= '<p>Location: ' . $result['results'][0]['formatted'] . '</p>';
    $output .= '<p>If this is you, no need to worry! This is just a security measure.</p>';
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p>However if you did not request this, somebody may have gained access to your account.</p>';
    $output .= '<p>Click the link below to remove access of the user.</p>';
    $output .= '<p><a href="http://localhost:8000/authentication/kill-session?email=' . $email . '&action=reset&userid=' . $userid . '" target="_blank">
                    http://localhost:8000/authentication/kill-session?mail=' . $email . '&action=reset&userid=' . $userid . '</a></p>';
    $body = $output;
    $subject = "Password Security Email";
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->SMTPAuth = true;
    $mail->Username = getenv('EMAIL');
    $mail->Password = getenv('EMAIL_APP_PASSWORD');
    $mail->IsHTML(true);
    $mail->setFrom(getenv('EMAIL'), 'Password Manager Security Team');
    $mail->addAddress($email, $username);
    $mail->Subject = $subject;
    $mail->Body = $body;
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        header("Refresh:3,url=/authentication");
        echo
        "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Document</title>
                    <link rel='stylesheet' href='/authentication/style1.css'> 
                </head>
                <body>
                    <div>
                    <h3>An email has been sent to you with instructions on how to reset your password.</h3>
                    </div>
                </body>
                </html>";
        exit();
    }
}
