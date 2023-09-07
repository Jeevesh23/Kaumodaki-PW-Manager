<?php
session_start();
if (!isset($_COOKIE[$_SESSION['hashemail']]) || $_COOKIE[$_SESSION['hashemail']] != 'signin') {
    header("Location:index.html");
    exit();
}
include_once('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

$tfa = new TwoFactorAuth(qrcodeprovider: new EndroidQrCodeProvider());
$result = $tfa->verifyCode($_SESSION['secret'], $_SESSION['otp']);
if ($result === true) {
    header("Refresh:3,url= ./../vault/index.php");
    echo "User successfully signed in! Redirecting to vault!";
} else {
    header("Refresh:3, url= http://localhost:8000/authentication");
    echo "Error! 2FA problems.";
    exit();
}
?>