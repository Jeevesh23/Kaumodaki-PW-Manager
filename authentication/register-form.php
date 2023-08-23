<?php
session_start();
if (!isset($_COOKIE[$_SESSION['hashemail']]) || $_COOKIE[$_SESSION['hashemail']] != 'register') {
    header("Location:index.html");
    exit();
}
include_once('/app/vendor/autoload.php');
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

$tfa = new TwoFactorAuth(
    qrcodeprovider: new EndroidQrCodeProvider()
);
$secret = $tfa->createSecret();
$_SESSION['secret'] = $secret;

?>
<!DOCTYPE html>
<html>

<head>
    <title>Two-Factor Authentication</title>
</head>

<body>
    <div class="Code">
        <p>Scan the following image with your authenticator app:</p>
        <img src="<?php
        echo $tfa->getQRCodeImageAsDataUri($_SESSION['username'] . '_' . $_SESSION['email'], $secret, 400); ?>">
    </div>
    <div class="Form">
        <form method="post" action="./register-otp.php">
            <label for="otp">Enter OTP: </label>
            <input type="text" placeholder="Your OTP" name="otp" id="otp" required>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>

</html>