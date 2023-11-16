<?php
include_once(__DIR__ . '/func.php');
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
    $key = getenv('AES_KEY');
    $hashemail = hash('md5', $email);
    $encemail = openssl_encrypt($email, "AES-256-CBC", $key);
    $username = $_POST["username"];
    if (!$email) {
        $error .= "<p>Invalid email address!</p>";
    } else {
        $sql = "SELECT * FROM `Credentials` WHERE `Username`='$username' AND `Email`='$encemail'";
        $results = mysqli_query($conn, $sql);
        $row = mysqli_num_rows($results);
        if ($row === 0) {
            $error .= "<p>No user is registered with this username!</p>";
        } else if ($results->num_rows > 0) {
            $method = "AES-256-CBC";
            while ($row = $results->fetch_assoc()) {
                $tfa = new TwoFactorAuth(qrcodeprovider: new EndroidQrCodeProvider());
                $key = openssl_decrypt($row["Secret_Key"], $method, $key, iv: hex2bin($row["IV"]));
                $userid = $row["User_ID"];
                $tfa_result = $tfa->verifyCode($key, $_POST['otp']);
                if ($tfa_result === true)
                    break;
            }
        }
    }
    if ($error) {
        header("Refresh:3, url=/authentication/reset-mail");
        echo $error;
        exit();
    } else if ($tfa_result != true) {
        header("Refresh:0.5, url=/authentication/reset-mail");
        echo '<script>alert("2FA authentication problems!")</script>';
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
        $output .= '<p><a href="http://localhost:8000/authentication/reset-password?key=' . $key . '&email=' . $email . '&action=reset&userid=' . $userid . '" target="_blank">
                    http://localhost:8000/authentication/reset-password?key=' . $key . '&email=' . $email . '&action=reset&userid=' . $userid . '</a></p>';
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
} else {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- <link rel="stylesheet" href="/authentication/style1.css"> -->
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
        <title>Forgot Password?</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

            .forgotpassword-page {
                width: 360px;
                padding: 8% 0 0;
                margin: auto;
            }

            .form {
                position: relative;
                z-index: 1;
                background: #FFFFFF;
                max-width: 360px;
                margin: 0 auto 100px;
                padding: 45px;
                text-align: center;
                box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
                border-radius: 25px;
            }

            .form input {
                outline: 0;
                background: #f2f2f2;
                width: 100%;
                border: 0;
                margin: 0 0 15px;
                padding: 15px;
                box-sizing: border-box;
                font-size: 14px;
                border-radius: 10px;
            }

            .form .button {
                text-transform: uppercase;
                outline: 0;
                background: #259c8d;
                width: 100%;
                border: 0;
                padding: 15px;
                color: #FFFFFF;
                font-size: 14px;
                -webkit-transition: all 0.3 ease;
                transition: all 0.3 ease;
                cursor: pointer;
            }

            .form .button:hover,
            .form button:active,
            .form .button:focus {
                background: #2CD0BC;
            }

            .form .message {
                margin: 15px 0 0;
                color: #b3b3b3;
                font-size: 12px;
            }

            .form .message a {
                color: #2CD0BC;
                text-decoration: none;
            }

            .form .register-form {
                display: none;
            }

            .container {
                position: relative;
                z-index: 1;
                max-width: 300px;
                margin: 0 auto;
            }

            .container:before,
            .container:after {
                content: "";
                display: block;
                clear: both;
            }

            .container .info {
                margin: 50px auto;
                text-align: center;
            }

            .container .info h1 {
                margin: 0 0 15px;
                padding: 0;
                font-size: 36px;
                font-weight: 300;
                color: #1a1a1a;
            }

            .container .info span {
                color: #4d4d4d;
                font-size: 12px;
            }

            .container .info span a {
                color: #000000;
                text-decoration: none;
            }

            .container .info span .fa {
                color: #EF3B3A;
            }

            body {
                background: #259c8d;
            }
        </style>
    </head>

    <body>
        <div class="forgotpassword-page">
            <div class="form">
                <h1>Forgot Password?</h2>
                    <form class="login-form" method="post" name="reset">
                        <input type="text" name="username" placeholder="Enter username" required>
                        <input type="email" name="email" placeholder="Enter email" required>
                        <input type="text" name="otp" placeholder="Enter OTP" required>
                        <input class="button" type="submit" value="Submit">
                    </form>
            </div>
        </div>
    </body>

    </html>

<?php } ?>