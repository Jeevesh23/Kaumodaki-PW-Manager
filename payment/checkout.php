<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
} else if ($_SESSION['Premium']) {
    header("Location: /vault");
    die();
}
require_once '/app/vendor/autoload.php';
$newIncludePath = '/app/vendor';
set_include_path($newIncludePath);

$stripeSecretKey = getenv('STRIPE_SECRET_KEY');
$stripePriceId = getenv('STRIPE_PRICE_ID');

\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost:8000';

$checkout_session = \Stripe\Checkout\Session::create([
    'line_items' => [[
        'price' => $stripePriceId,
        'quantity' => 1,
    ]],
    'customer_email' => $_SESSION['email'],
    'mode' => 'payment',
    'success_url' => $YOUR_DOMAIN . "/payment/success?session_id={CHECKOUT_SESSION_ID}",
    'cancel_url' => $YOUR_DOMAIN . '/payment',
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
