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
    <!-- <link rel="stylesheet" href="/authentication/style1.css"> -->
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        section {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .product {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.7);
            display: flex;
            max-width: 600px;
            padding: 20px;
            width: 100%;
        }

        .description {
            padding: 20px;
            text-align: center;
        }

        img {
            max-width: 128px;
            border-radius: 8px;
        }

        h3 {
            font-size: 24px;
            margin: 10px 0;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
        }

        ol {
            font-size: 16px;
            margin: 10px 0;
        }

        li {
            margin: 8px 0;
        }

        h4 {
            font-size: 20px;
            margin: 10px 0;
            color: #007BFF;
        }

        button#checkout-button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button#checkout-button:hover {
            background-color: #0056b3;
        }
    </style>
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
                <form action="/payment/checkout" method="POST">
                    <button type="submit" id="checkout-button">Checkout</button>
                </form>
            </div>
        </div>

    </section>
</body>

</html>