<?php
include_once('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

require_once(__DIR__ . '/func.php');

if (isset($_GET["action"]) || isset($_POST["action"])) {
    $conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if (
        isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"])
        && ($_GET["action"] == "reset") && !isset($_POST["action"])
    ) {
        $key = $_GET["key"];
        $email = $_GET["email"];
        $userid = $_GET["userid"];
        $date = date("Y-m-d H:i:s");
        $result = mysqli_query(
            $conn,
            "SELECT * FROM `Password_Reset` WHERE `Reset_Key`='$key' and `Email`='$email';"
        );
        $row = mysqli_num_rows($result);
        if ($row === 0) {
            header("Refresh:3, url=/authentication");
            $error .= '<h2>Invalid Link!</h2><p>The link is invalid!</p>';
            echo $error;
            exit();
        } else {
            $row = mysqli_fetch_assoc($result);
            $ExpDate = $row['ExpDate'];
            if ($ExpDate >= $date) {
                ?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
                    <title>Document</title>
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
            .form .button:hover,.form button:active,.form .button:focus {
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
            .container:before, .container:after {
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
                        <h2>Reset Password</h2><br>
                        <form class="login-form" method="post" action="" name="update">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="userid" value="<?php echo $userid; ?>">

                            <input type="password" name="password" placeholder="Enter New Password" required><br><br>
                            <input type="password" name="otp" placeholder="Enter OTP" required>

                            <input class="button" type="submit" value="Reset Password">
                        </form>
                    </div>
                </div>
                </body>

                </html>

                <?php
            } else {
                $error .= "<h2>Link Expired</h2>
            <p>The link is expired.<br></p>";
            }
        }
        if ($error) {
            header("Refresh:3, url=/authentication");
            echo "<div class='error'>" . $error . "</div><br>";
            exit();
        }
    }
    if (
        isset($_POST["userid"]) && isset($_POST["action"]) &&
        ($_POST["action"] == "update")
    ) {
        $tfa = new TwoFactorAuth(qrcodeprovider: new EndroidQrCodeProvider());
        $error = "";
        $password = mysqli_real_escape_string($conn, $_POST["password"]);
        $salt = getRandomStringRand();
        $hasheddata = hash('sha512', $password . $salt);
        $userid = $_POST["userid"];
        $otp = $_POST["otp"];
        $sql = "SELECT * FROM `Credentials` WHERE `User_ID`='$userid'";
        $results = mysqli_query($conn, $sql);
        $row = mysqli_num_rows($results);
        if ($results->num_rows > 0) {
            $key = getenv('AES_KEY');
            $method = "AES-256-CBC";
            while ($row = $results->fetch_assoc()) {
                $tfa = new TwoFactorAuth(qrcodeprovider: new EndroidQrCodeProvider());
                $key = openssl_decrypt($row["Secret_Key"], $method, $key, iv: $row["IV"]);
                $tfa_result = $tfa->verifyCode($key, $_POST['otp']);
            }
        }
        if ($tfa_result === true) {
            mysqli_query(
                $conn,
                "UPDATE `Credentials` SET `Password`='$hasheddata',`Salt`='$salt' WHERE `User_ID`='$userid';"
            );
            mysqli_query($conn, "DELETE FROM `Password_Reset` WHERE `User_ID`='$userid'");
            $conn->close();
            header("Refresh:3,url= /vault");
            echo '<div><p>Congratulations! Your password has been updated successfully.</p>';
            exit();
        } else {
            echo "2FA failed. Re-enter the link or try requesting a new reset link.";
            $conn->close();
            exit();
        }
    }
} else {
    header("Location:/authentication");
    exit();
}
?>