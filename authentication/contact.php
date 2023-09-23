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
    $mail->addCC(getenv('DEV_EMAIL_1'), 'SorcierMaheP');
    $mail->addCC(getenv('DEV_EMAIL_2'), 'P Jeevesh');
    $mail->Subject = $subject;
    $mail->Body = $body;
    if (!$mail->send()) {
      echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
      header("Refresh:3,url=/");
      echo
        "
              <!DOCTYPE html>
              <html lang='en'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Document</title>
                  <link rel='stylesheet' href='style1.css'> 
              </head>
              <body>
                  <div>
                  <h2>Feedback email has been sent!</h2>
                  </div>
              </body>
              </html>"
      ;
      exit();
    }
  }
} else { ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">

  <head>
    <meta charset="UTF-8" />
    <title>Contact Us Form | Password Manager</title>
    <link rel="stylesheet" href="/authentication/contact.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>

  <body>
    <div class="container">
      <div class="content">
        <div class="left-side">
          <div class="address details">
            <i class="fas fa-map-marker-alt"></i>
            <div class="topic">Address</div>
            <div class="text-one">Ponda</div>
            <div class="text-two">Goa 403-401</div>
          </div>
          <div class="phone details">
            <i class="fas fa-phone-alt"></i>
            <div class="topic">Phone</div>
            <div class="text-one">+0098 9893 5647</div>
            <div class="text-two">+0096 3434 5678</div>
          </div>
          <div class="email details">
            <i class="fas fa-envelope"></i>
            <div class="topic">Email</div>
            <div class="text-one">abcd@gmail.com</div>
            <div class="text-two">efgh@gmail.com</div>
          </div>
        </div>
        <div class="right-side">
          <div class="topic-text">Send us a message</div>
          <p>If you have any types of queries related to our Password manager, you can send us a message from here. It's
            our pleasure to help you.</p>
          <form action="" method="post">
            <div class="input-box">
              <input type="text" placeholder="Enter your name" name="name" required>
            </div>
            <div class="input-box">
              <input type="text" placeholder="Enter your email" name="email" required>
            </div>
            <div class="input-box message-box">
              <textarea placeholder="Enter your message" name="message" required></textarea>
            </div>
            <div class="button">
              <input type="submit" value="Send Now" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>

  </html>
<?php } ?>