<?php include_once(__DIR__ . '/../db/db.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <!-- font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- css stylesheet -->
    <link rel="stylesheet" href="/authentication/test.css">
</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="/authentication/register" method="post">
                <h1>Create Account</h1>
                <!-- <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div> -->
                <!-- <span>or use your email for registration</span> -->
                <div class="infield">
                    <input type="text" name="username" placeholder="Name" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="email" name='email' placeholder="Email" name="email" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name='password' placeholder="Password" required>
                    <label></label>
                </div>
                <button>Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="/authentication/signin" method="post">
                <h1>Sign in</h1>
                <!-- <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div> -->
                <!-- <span>or use your account</span> -->
                <div class="infield">
                    <input type="email" name='email' placeholder="Email" name="email" />
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name='password' placeholder="Password" />
                    <label></label>
                </div>
                <div class="infield">
                    <input type="otp" name='otp' placeholder="OTP" />
                    <label></label>
                </div>
                <a href="reset-mail" class="forgot">Forgot your password?</a>
                <button>Login</button>
            </form>
        </div>
        <div class="overlay-container" id="overlayCon">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome, user! We have been expecting you!</h1>
                    <img src="/authentication/images/the_emperor_icon.png" width="72" height="72">
                    <p>Continue your journey with us and enter your vault!</p>
                    <button>Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello There! Come here friend, don't be afraid!</h1>
                    <img src="/authentication/images/ben_kenobi_icon.png" width="64" height="64">
                    <p>Start your journey with us and secure your accounts!</p>
                    <button>Sign Up</button>
                </div>
            </div>
            <button id="overlayBtn"></button>
        </div>
    </div>

    <!-- js code -->
    <script>
        const container = document.getElementById('container');
        const overlayCon = document.getElementById('overlayCon');
        const overlayBtn = document.getElementById('overlayBtn');

        overlayBtn.addEventListener('click', () => {
            container.classList.toggle('right-panel-active');

            overlayBtn.classList.remove('btnScaled');
            window.requestAnimationFrame(() => {
                overlayBtn.classList.add('btnScaled');
            })
        });
    </script>

</body>

</html>