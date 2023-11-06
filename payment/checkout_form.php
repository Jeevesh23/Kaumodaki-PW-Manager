<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
} else if ($_SESSION['Premium']) {
    echo '<script>alert("You are already a premium subscriber! Thanks BTW!");</script>';
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/vault';
    header("Refresh:0.5,url= $referer");
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Premium Subscription Landing Page</title>
    <link rel="stylesheet" href="/authentication/style1.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <section>
        <div class="product">
            <div class="description">
                <img src="/logos/password_manager_premium.jpg" width="128px">
                <h3>PW Manager Premium Subscription</h3>
                <p> With this subscription, you can support us, as well as get access to premium features such as:-</p>
                <ol>
                    <li>Access to premium storage to store documents(upto 16 MB).</li>
                    <li>Advanced password strength analysis using ZXCVBN library.</li>
                </ol>
                <h4>Rs 500.00</h4>
            </div>
        </div>
        <form action="/payment/checkout" method="POST">
            <button type="submit" id="checkout-button">Checkout</button>
        </form>
    </section>
</body>

</html>