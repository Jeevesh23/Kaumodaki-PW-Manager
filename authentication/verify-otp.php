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
    $conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM `Credentials` WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Order_ID`!='0'";
    $req = mysqli_query($conn, $sql);
    if ($req->num_rows > 0)
        $_SESSION['Premium'] = 1;
    else
        $_SESSION['Premium'] = 0;
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
    echo '<script> alert("Error! 2FA problems.") </script>';
    exit();
}
