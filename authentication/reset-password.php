<?php
include_once('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

require_once('./func.php');

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
            header("Refresh:3, url=http://localhost:8000/authentication");
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
                    <link rel="stylesheet" href="style1.css">
                    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
                    <title>Document</title>
                </head>
                <body>

                    <div class="center">
                        <h2>Reset Password</h2><br>
                        <form method="post" action="" name="update">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="userid" value="<?php echo $userid; ?>">

                            <div class="input_box">
                                <!-- <label>Enter New Password:</label> -->
                                <i class="uil uil-lock password"></i>
                                <input type="password" name="password" placeholder="Enter New Password"required><br><br>
                                <span></span>
                            </div>

                            <div class="input_box">
                                <!-- <label>Enter 2FA OTP:</label> -->
                                <i class="uil uil-arrow point"></i>
                                <input type="password" name="otp" placeholder="Enter OTP" required>
                                <span></span>
                            </div>
                            <input type="submit" value="Reset Password">
                        </form>
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
            header("Refresh:3, url=http://localhost:8000/authentication");
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
            header("Refresh:3,Location:index.html");
            echo '<div><p>Congratulations! Your password has been updated successfully.</p>';
        } else {
            echo "2FA failed. Re-enter the link or try requesting a new reset link.";
            $conn->close();
            exit();
        }
    }
} else {
    header("Location:index.html");
    exit();
}
?>