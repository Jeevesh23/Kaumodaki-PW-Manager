<?php
include_once(__DIR__ . './../vendor/autoload.php');
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

$tfa = new TwoFactorAuth(
    qrcodeprovider: new EndroidQrCodeProvider()
);

session_start();

$verification = $_POST['otp'];
$result = $tfa->verifyCode($_SESSION['secret'], $verification);

$conn = mysqli_connect('localhost', 'root', '', 'PM_1');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($result === true) {
    $username = $_SESSION['username'];
    $salt = $_SESSION['salt'];
    $password = $_SESSION['password'];

    $key = getenv('AES_KEY');
    $method = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $secret = $_SESSION['secret'];
    $encrypted = openssl_encrypt($secret, $method, $key, iv: $iv);

    $sql1 = "INSERT INTO `Credentials` (`Username`, `Password`, `Salt`, `Secret_Key`, `IV`) VALUES ('$username','$password','$salt','$encrypted','$iv')";
    $result1 = mysqli_query($conn, $sql1);
    if (!$result1) {
        echo "Connection failed!";
        header("Refresh:5, url= http://localhost/DBMS-Lab-Project/authentication/register-form.php");
        exit();
    } else
        echo "User successfully registered!";
} else {
    echo "Error! 2FA problems.";
    header("Refresh:5, url= http://localhost/DBMS-Lab-Project/authentication/register-form.php");
    exit();
}

$conn->close();
?>