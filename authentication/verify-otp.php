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
?>
    <html>

    <head>
        <link rel='stylesheet' href='/authentication/style1.css'>
    </head>

    <body>
        <div>
            <h3 id="location"></h3>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                if (navigator.geolocation) {
                    $('#location').html('Kindly wait!');
                    navigator.geolocation.getCurrentPosition(showLocation);
                } else {
                    $('#location').html('Geolocation is not supported by this browser.');
                }
            });

            function showLocation(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
                $.ajax({
                    type: 'POST',
                    url: '/authentication/location',
                    data: 'latitude=' + latitude + '&longitude=' + longitude,
                    success: function(msg) {
                        if (msg) {
                            $("#location").html(msg);
                            setTimeout(function() {
                                window.location.href = "/vault";
                            }, 1000);
                        } else {
                            $("#location").html('Not Available');
                        }
                    }
                });
            }
        </script>
    </body>

    </html>
<?php exit();
} else {
    header("Refresh:3, url= /authentication");
    echo "Error! 2FA problems.";
    exit();
}
