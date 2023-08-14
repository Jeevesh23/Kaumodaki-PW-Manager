<?php
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;

include_once(__DIR__ . './../vendor/autoload.php');
use RobThree\Auth\TwoFactorAuth;

$tfa = new TwoFactorAuth(
    qrcodeprovider: new EndroidQrCodeProvider()
);
$secret = $tfa->createSecret();
session_start();
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
        echo $tfa->getQRCodeImageAsDataUri($_SESSION['username'], $secret, 400); ?>">
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