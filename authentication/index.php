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
                    <input type="text" name="username" id="usernameInput" placeholder="Name" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="email" name='email' id="emailInput" placeholder="Email" name="email" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name='password' placeholder="Password" required>
                    <label></label>
                </div>
                <div class="tnc">
                    <input type="checkbox" name="tnc" required><a href="/authentication/terms-and-conditions">Terms and Conditions</a>
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
                    <input type="email" name='email' id="emailInput" placeholder="Email" name="email" />
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name='password' placeholder="Password" />
                    <label></label>
                </div>
                <div class="infield">
                    <input type="otp" name='otp' id="otpInput" placeholder="OTP" />
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



        // // email validation
        // var emailInput = document.getElementById('emailInput');
        // var emailValidationMessage = document.getElementById('emailValidationMessage');
        // var typingTimer; // Timer identifier
        // var doneTypingInterval = 2000; // Delay in milliseconds (1 second)

        // emailInput.addEventListener('input', function () {
        //     clearTimeout(typingTimer); // Clear the previous timer

        //     // Start a new timer to delay the validation
        //     typingTimer = setTimeout(validateEmail, doneTypingInterval);
        // });

        // function validateEmail() {
        //     var email = emailInput.value;
        //     var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/;

        //     if (emailRegex.test(email)) {
        //         emailValidationMessage.textContent = '';
        //     } else {
        //         alert('Please enter a valid email address.');
        //         emailInput.value = '';
        //     }
        // }


        // // otp validation
        // var otpInput = document.getElementById('otpInput');
        // var otpValidationMessage = document.getElementById('otpValidationMessage');
        // var typingTimer; // Timer identifier
        // var doneTypingInterval = 1000; // Delay in milliseconds (1 second)

        // otpInput.addEventListener('input', function () {
        //     clearTimeout(typingTimer); // Clear the previous timer

        //     // Start a new timer to delay the validation
        //     typingTimer = setTimeout(validateOTP, doneTypingInterval);
        // });

        // function validateOTP() {
        //     var otp = otpInput.value;
        //     var otpRegex = /^\d{6}$/;

        //     if (otpRegex.test(otp)) {
        //         otpValidationMessage.textContent = '';
        //     } else {
        //         alert('Please enter a 6-digit OTP.');
        //         otpInput.value = '';
        //     }
        // }


        // //username validation
        // var usernameInput = document.getElementById('usernameInput');
        // var usernameValidationMessage = document.getElementById('usernameValidationMessage');
        // var typingTimer; // Timer identifier
        // var doneTypingInterval = 2000; // Delay in milliseconds (1 second)

        // usernameInput.addEventListener('input', function () {
        //     clearTimeout(typingTimer); // Clear the previous timer

        //     // Start a new timer to delay the validation
        //     typingTimer = setTimeout(validateUsername, doneTypingInterval);
        // });

        // function validateUsername() {
        //     var username = usernameInput.value;
        //     var usernameRegex = /^[a-zA-Z0-9]+$/;

        //     if (usernameRegex.test(username)) {
        //         usernameValidationMessage.textContent = '';
        //     } else {
        //         alert('Username should only contain alphanumeric characters.');
        //         usernameInput.value = '';
        //     }
        // }


        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const usernameInput = document.getElementById("usernameInput");

            form.addEventListener("submit", function(event) {
                const usernameValue = usernameInput.value;
                const alphanumericPattern = /^[a-zA-Z0-9]+$/;

                if (!alphanumericPattern.test(usernameValue)) {
                    event.preventDefault();
                    alert('Username should only contain alphanumeric characters.');
                    usernameInput.value = '';
                }
            });
        });
    </script>


</body>

</html>