<?php
include_once(__DIR__ . './../vendor/autoload.php');
use RobThree\Auth\TwoFactorAuth;

session_start();

$tfa = new TwoFactorAuth();
$result = $tfa->verifyCode($_SESSION['secret'], $_SESSION['otp']);
if ($result === true) {
    echo "User successfully signed in!";
} else {
    echo "Error! 2FA problems.";
    header("Refresh:5, url= http://localhost/DBMS-Lab-Project/authentication");
    exit();
}
?>