<?php
session_start();
if (!isset($_COOKIE[$_SESSION['hashemail']]) || $_COOKIE[$_SESSION['hashemail']] != 'signin') {
    header("Location:/authentication");
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
    header("Refresh:3,url= /vault");
    echo "User successfully signed in! Redirecting to vault!";
    exit();
} else {
    header("Refresh:3, url= /authentication");
    echo "Error! 2FA problems.";
    exit();
}
