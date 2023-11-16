<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
} else if ($_SESSION['Premium']) {
    header("Location: /vault");
    die();
}
require '/app/vendor/autoload.php';
$stripeSecretKey = getenv('STRIPE_SECRET_KEY');
$stripe = new \Stripe\StripeClient($stripeSecretKey);
try {
    $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
    $pi = $session->payment_intent;

    $con = mysqli_connect("db", "root", "MYSQL_ROOT_PASSWORD", "PM_1");
    if (!$con) {
        die("Connection Error");
    }

    $sql = "UPDATE `Credentials` SET `Order_ID`='$pi' WHERE `User_ID`=" . $_SESSION['User_ID'];
    $req = mysqli_query($con, $sql);
    $_SESSION['Premium'] = 1;
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Thanks for your order!</title>
        <link rel="stylesheet" href="/authentication/style1.css">
    </head>

    <body>
        <div>
            <p>
                We appreciate your business! If you have any questions, please email
                <a href="mailto:projectt2900@gmail.com">projectt2900@gmail.com</a>
            </p>
        </div>
    </body>

    </html>
<?php
    http_response_code(200);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>