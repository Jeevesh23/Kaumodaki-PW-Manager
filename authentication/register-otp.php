<?php
session_start();
if (!isset($_COOKIE[$_SESSION['email']]) || $_COOKIE[$_SESSION['email']] != 'register') {
    header("Location:index.html");
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
    $email = $_SESSION['email'];
    $salt = $_SESSION['salt'];
    $password = $_SESSION['password'];

    $key = getenv('AES_KEY');
    $method = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $secret = $_SESSION['secret'];
    $encrypted = openssl_encrypt($secret, $method, $key, iv: $iv);

    $sql1 = "INSERT INTO `Credentials` (`Username`, `Email`,`Password`, `Salt`, `Secret_Key`, `IV`) VALUES ('$username','$email','$password','$salt','$encrypted','$iv')";
    $result1 = mysqli_query($conn, $sql1);
    if (!$result1) {
        header("Refresh:3, url= http://localhost:8000/authentication");
        echo "Connection failed!";
        exit();
    } else
        echo "User successfully registered!";
} else {
    header("Refresh:3, url= http://localhost:8000/authentication");
    echo "Error! 2FA problems.";
    exit();
}

$conn->close();
?>