<?php
session_start();
if (!isset($_COOKIE[$_SESSION['hashemail']]) || $_COOKIE[$_SESSION['hashemail']] != 'register') {
    header("Location:/authentication");
    exit();
}
include_once(__DIR__ . '/func.php');
include_once('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

$tfa = new TwoFactorAuth(
    qrcodeprovider: new EndroidQrCodeProvider()
);

$verification = $_POST['otp'];
$result = $tfa->verifyCode($_SESSION['secret'], $verification);

$conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($result === true) {
    $username = $_SESSION['Username'];
    $hashemail = $_SESSION['hashemail'];
    $salt = $_SESSION['salt'];
    $salt2 = $_SESSION['salt2'];
    $password = $_SESSION['password'];

    $key = getenv('AES_KEY');
    $method = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $secret = $_SESSION['secret'];
    $encrypted = openssl_encrypt($secret, $method, $key, iv: $iv);
    $encemail = openssl_encrypt($_SESSION['email'], $method, $key);
    $hexiv = bin2hex($iv);

    $sql1 = "INSERT INTO `Credentials` (`Username`, `Email`,`Password`, `Salt`, `Salt2`,`Secret_Key`, `IV`) VALUES ('$username','$encemail','$password','$salt','$salt2','$encrypted','$hexiv')";
    $result1 = mysqli_query($conn, $sql1);
    if (!$result1) {
        $conn->close();
        header("Refresh:3, url= /authentication");
        echo '<script>alert("Connection failed!")</script>';
        exit();
    } else {
        $sql2 = "SELECT `User_ID` FROM `Credentials` WHERE `Email`='$encemail'";
        $result2 = mysqli_query($conn, $sql2);
        $conn->close();
        if ($result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $_SESSION['User_ID'] = $row["User_ID"];
            }
        }
        require(__DIR__ . '/../vault/genicon.php');
        createIconAndStoreInDB($username, $_SESSION['User_ID']);
        $_SESSION['Premium'] = 0;
        header("Refresh:3,url= /vault");
        echo '<script>alert("User successfully registered! Redirecting to vault!")</script>';
        exit();
    }
} else {
    header("Refresh:3, url= /authentication");
    echo '<script>alert("Error! 2FA problems.")</script>';
    exit();
}
