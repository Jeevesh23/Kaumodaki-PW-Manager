<?php
session_start();
if (!isset($_COOKIE[$_SESSION['hashemail']]) || $_COOKIE[$_SESSION['hashemail']] != 'register') {
    header("Location:/authentication");
    exit();
}
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
    $username = $_SESSION['username'];
    $hashemail = $_SESSION['hashemail'];
    $salt = $_SESSION['salt'];
    $password = $_SESSION['password'];

    $key = getenv('AES_KEY');
    $method = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $secret = $_SESSION['secret'];
    $encrypted = openssl_encrypt($secret, $method, $key, iv: $iv);

    $sql1 = "INSERT INTO `Credentials` (`Username`, `Email`,`Password`, `Salt`, `Secret_Key`, `IV`) VALUES ('$username','$hashemail','$password','$salt','$encrypted','$iv')";
    $result1 = mysqli_query($conn, $sql1);
    if (!$result1) {
        $conn->close();
        header("Refresh:3, url= /authentication");
        echo "Connection failed!";
        exit();
    } else {
        $sql2 = "SELECT `User_ID` FROM `Credentials` WHERE `Email`='$hashemail'";
        $result2 = mysqli_query($conn, $sql2);
        $conn->close();
        if ($result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $_SESSION['User_ID'] = $row["User_ID"];
            }
        }
        require(__DIR__ . '/../vault/genicon.php');
        createIconAndStoreInDB($username, $_SESSION['User_ID']);
        $_SESSION['PREMIUM'] = 0;
        header("Refresh:3,url= /vault");
        echo "User successfully registered! Redirecting to vault!";
        exit();
    }
} else {
    header("Refresh:3, url= /authentication");
    echo "Error! 2FA problems.";
    exit();
}
