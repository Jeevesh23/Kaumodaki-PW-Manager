<?php
include_once('./func.php');
include_once('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

$conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST["email"]) && (!empty($_POST["email"]))) {
    $email = $_POST["email"];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $hashemail = hash('md5', $email);
    $username = $_POST["username"];
    if (!$email) {
        $error .= "<p>Invalid email address!</p>";
    } else {
        $sql = "SELECT * FROM `Credentials` WHERE `Username`='$username' AND `Email`='$hashemail'";
        $results = mysqli_query($conn, $sql);
        $row = mysqli_num_rows($results);
        if ($row === 0) {
            header("Refresh:3, url=http://localhost:8000/authentication/reset-mail.php");
            $error .= "<p>No user is registered with this username!</p>";
        } else if ($results->num_rows > 0) {
            $key = getenv('AES_KEY');
            $method = "AES-256-CBC";
            while ($row = $results->fetch_assoc()) {
                $tfa = new TwoFactorAuth(qrcodeprovider: new EndroidQrCodeProvider());
                $key = openssl_decrypt($row["Secret_Key"], $method, $key, iv: $row["IV"]);
                $userid = $row["User_ID"];
                $tfa_result = $tfa->verifyCode($key, $_POST['otp']);
                if ($tfa_result === true)
                    break;
            }
        }
    }
    if ($error) {
        header("Refresh:3, url=http://localhost:8000/authentication/reset-mail.php");
        echo $error;
        exit();
    } else if ($tfa_result != true) {
        header("Refresh:3, url=http://localhost:8000/authentication/reset-mail.php");
        echo "2FA authentication problems!";
        exit();
    } else {
        $expformat = mktime(date("H"), date("i") + 10, date("s"), date("m"), date("d"), date("Y"));
        $expdate = date("Y-m-d H:i:s", $expformat);
        $key = sha1(getRandomStringRand(8) . $email);
        $addkey = substr(sha1(uniqid(rand(), 1)), 3, 10);
        $key = $key . $addkey;
        if (mysqli_query($conn, "SELECT * FROM `Password_Reset` WHERE `User_ID`='$userid'")->num_rows > 0) {
            mysqli_query($conn, "UPDATE `Password_Reset` SET `Email`='$email', `Reset_Key`='$key', `ExpDate`='$expdate' WHERE `User_ID`='$userid'");
        } else {
            mysqli_query(
                $conn,
                "INSERT INTO `Password_Reset` (`User_ID`,`Username`,`Email`, `Reset_Key`, `ExpDate`) VALUES ('$userid','$username', '$email', '$key', '$expdate');"
            );
        }
        $output = '<p>Dear user ' . $username . ', thanks for using our password manager!</p>';
        $output .= '<p>Please click on the following link to reset your password.</p>';
        $output .= '<p>-------------------------------------------------------------</p>';
        $output .= '<p><a href="http://localhost:8000/authentication/reset-password.php?key=' . $key . '&email=' . $email . '&action=reset&userid=' . $userid . '" target="_blank">
                    http://localhost:8000/authentication/reset-password.php?key=' . $key . '&email=' . $email . '&action=reset&userid=' . $userid . '</a></p>';
        $output .= '<p>-------------------------------------------------------------</p>';
        $output .= '<p>Please be sure to copy the entire link into your browser.
                    The link will expire after 10 minutes for security reasons.</p>';
        $output .= '<p>If you did not request this email, no action is needed, 
                    your password will not be reset. However, do change your account password.</p>';
        $output .= '<p>Also, if you recieved an email not intended for you, do not worry. You won\'t be
                    able to do anything much with it anyway :)</p>';
        $output .= '<p>Thanks for believing in us!</p>';
        $output .= '<p>Password Recovery Team</p>';

        $body = $output;
        $subject = "Password Recovery Email";
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL');
        $mail->Password = getenv('EMAIL_APP_PASSWORD');
        $mail->IsHTML(true);
        $mail->setFrom(getenv('EMAIL'), 'Password Manager Recovery Team');
        $mail->addAddress($email, 'John Doe');
        $mail->Subject = $subject;
        $mail->Body = $body;
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            header("Refresh:3,url=./index.php");
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
                    <h3>An email has been sent to you with instructions on how to reset your password.</h3>
                    </div>
                </body>
                </html>"
            ;
        }
    }
} else {
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style1.css">
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
        <title>Document</title>
    </head>

    <body>

        <div class="center">
            <h2>Forgot Password?</h2>
            <form method="post" name="reset"><br><br>
                <div class="input_box">
                    <!-- <label>Username:</label> -->
                    <i class="uil uil-user body"></i>
                    <input type="text" name="username" placeholder="Enter username" required>
                    <span></span>
                    <br><br>
                </div>
                <div class="input_box">
                    <!-- <label>Email-ID:</label> -->
                    <i class="uil uil-envelope-alt email"></i>
                    <input type="email" name="email" placeholder="Enter email" required>
                    <span></span>
                    <br><br>
                </div>
                <div class="input_box">
                    <!-- <label>2FA-OTP:</label> -->
                    <i class="uil uil-arrow point"></i>
                    <input type="password" name="otp" placeholder="Enter OTP" required>
                    <span></span>
                    <br><br>
                </div>
                <input type="submit" value="Submit">
            </form>
        </div>

    </body>

    </html>

<?php } ?>