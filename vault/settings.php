<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
require_once(__DIR__ . '/config/db.php');
$namequery = "SELECT `Username` FROM `Credentials` WHERE `User_ID`=" . $_SESSION['User_ID'];
$nameres = mysqli_query($con, $namequery);
$namerow = $nameres->fetch_row();
if (isset($_POST['logout']) && $_POST['logout'] == 1) {
    echo '<script>
            var confirmLogout = window.confirm("Are you sure you want to log out?");
            if (confirmLogout) {
                window.location.href = "/vault/logout";
            } else {
                window.history.back();
            }
          </script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Settings</title>
    <link rel="icon" type="image/png" href="./dist/images/favicon.png" />

    <!-- Icon Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+Pro:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet" />
    <style>
        main p {
            margin-bottom: 40px;
            text-align: center;
        }

        main a {
            text-decoration: underline;
            color: #1889e6;
        }

        main a:hover {
            text-decoration: none;
        }

        main .tab-content {
            color: #000000;
        }

        /**/
        /* main styles */
        /**/
        .pcss3t {
            margin: 0;
            padding: 0;
            border: 0;
            outline: none;
            font-size: 0;
            text-align: left;
        }

        .pcss3t>input {
            position: absolute;
            left: -9999px;
        }

        .pcss3t>label {
            position: relative;
            display: inline-block;
            margin: 0;
            padding: 0;
            border: 0;
            outline: none;
            cursor: pointer;
            transition: all 0.1s;
            -o-transition: all 0.1s;
            -ms-transition: all 0.1s;
            -moz-transition: all 0.1s;
            -webkit-transition: all 0.1s;
        }

        .pcss3t>input:checked+label {
            cursor: default;
        }

        .pcss3t>ul {
            list-style: none;
            position: relative;
            display: block;
            overflow: hidden;
            margin: 0;
            padding: 0;
            border: 0;
            outline: none;
            font-size: 13px;
        }

        .pcss3t>ul>li {
            position: absolute;
            width: 100%;
            overflow: auto;
            padding: 30px 40px 40px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            opacity: 0;
            transition: all 0.5s;
            -o-transition: all 0.5s;
            -ms-transition: all 0.5s;
            -moz-transition: all 0.5s;
            -webkit-transition: all 0.5s;
        }

        .pcss3t>.tab-content-first:checked~ul .tab-content-first,
        .pcss3t>.tab-content-2:checked~ul .tab-content-2,
        .pcss3t>.tab-content-3:checked~ul .tab-content-3,
        .pcss3t>.tab-content-4:checked~ul .tab-content-4,
        .pcss3t>.tab-content-5:checked~ul .tab-content-5,
        .pcss3t>.tab-content-6:checked~ul .tab-content-6,
        .pcss3t>.tab-content-7:checked~ul .tab-content-7,
        .pcss3t>.tab-content-8:checked~ul .tab-content-8,
        .pcss3t>.tab-content-9:checked~ul .tab-content-9,
        .pcss3t>.tab-content-last:checked~ul .tab-content-last {
            z-index: 1;
            top: 0;
            left: 0;
            opacity: 1;
        }



        .pcss3t>label {
            padding: 0 20px;
            background: #e5e5e5;
            font-size: 13px;
            line-height: 49px;
        }

        .pcss3t>label:hover {
            background: #f2f2f2;
        }

        .pcss3t>input:checked+label {
            background: #fff;
        }

        .pcss3t>ul {
            background: #fff;
            text-align: left;
        }

        .pcss3t-steps>label:hover {
            background: #e5e5e5;
        }


        .pcss3t-theme-1>label {
            margin: 0 5px 5px 0;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 2px rgba(0, 0, 0, 0.2);
            color: #808080;
            opacity: 0.8;
        }

        .pcss3t-theme-1>label:hover {
            background: #fff;
            opacity: 1;
        }

        .pcss3t-theme-1>input:checked+label {
            margin-bottom: 0;
            padding-bottom: 5px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
            color: #2b82d9;
            opacity: 1;
        }

        .pcss3t-theme-1>ul {
            border-radius: 5px;
            box-shadow: 0 3px rgba(0, 0, 0, 0.2);
        }

        .pcss3t-theme-1>.tab-content-first:checked~ul {
            border-top-left-radius: 0;
        }


        .pcss3t>ul,
        .pcss3t>ul>li {
            height: 450px;
        }

        li img {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.9);
        }

        main .form-heading {
            text-decoration: underline;
            text-decoration-color: #9b59b6;
            text-underline-offset: 2px;
        }

        main .material-icons-sharp {
            width: 30px;
            padding-top: 10px;
        }

        /* reset password */
        .reset_password {
            width: 300px;
            margin: 0 auto;
            text-align: center;
        }

        .reset_password form {
            background-color: #f3f3f3;
            padding: 20px;
            border-radius: 5px;
        }

        .reset_password label {
            display: block;
            margin-bottom: 10px;
        }

        .reset_password input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .reset_password button,
        .reset {
            background-color: #0074d9;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .reset_password button:hover {
            background-color: #0056b3;
        }

        /* Style the form container */
        .contact-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 5px;
        }

        /* Style the form heading */
        .contact-heading {
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Style the form labels */
        .contact-label {
            display: block;
            margin: 10px 0;
            font-weight: bold;
        }

        /* Style the form input fields */
        .contact-input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        /* Style the form textarea */
        .contact-textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        /* Style the form submit button */
        .contact-submit {
            background-color: #0074d9;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .contact-submit:hover {
            background-color: #0056b3;
        }


        /* user profile */
        .user_container .title {
            font-size: 25px;
            font-weight: 500;
            position: relative;
        }

        .content form .user-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0 12px 0;
        }

        form .user-details .input-box {
            margin-bottom: 15px;
            width: calc(100% / 2 - 20px);
        }

        form .input-box span.details {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .user-details .input-box input {
            height: 45px;
            width: 100%;
            outline: none;
            font-size: 16px;
            border-radius: 5px;
            padding-left: 15px;
            border: 1px solid #ccc;
            border-bottom-width: 2px;
            transition: all 0.3s ease;
        }

        .user-details .input-box input:focus,
        .user-details .input-box input:valid {
            border-color: #9b59b6;
        }

        form .button {
            height: 40px;
            margin: 35px 0;
            gap: 10px;
        }

        form .button input {
            height: 100%;
            width: 20%;
            border-radius: 5px;
            border: none;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #0074d9;
        }

        form .button input:hover {
            background-color: #002ead;
            transition: 0.7s;
            transform: scale(1.1);
        }

        @media(max-width: 584px) {
            .container {
                max-width: 100%;
            }

            form .user-details .input-box {
                margin-bottom: 15px;
                width: 100%;
            }

            form .category {
                width: 100%;
            }

            .content form .user-details {
                max-height: 300px;
                overflow-y: scroll;
            }

            .user-details::-webkit-scrollbar {
                width: 5px;
            }
        }

        @media(max-width: 459px) {
            .container .content .category {
                flex-direction: column;
            }
        }

        .form-label {
            display: block;
            margin: 10px 0;
            font-weight: bold;
        }

        /* Style the form select input (Country) */
        .form-select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <h2>Password<br><span class="danger">Manager</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <a href="/vault">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="#" class="active">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="/vault/add-password">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>Add Password</h3>
                </a>
                <a href="/vault/uploads">
                    <span class="material-icons-sharp">
                        upload
                    </span>
                    <h3>Upload</h3>
                </a>
                <form method="post">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit">
                        <span class="material-icons-sharp">
                            logout
                        </span>
                        <h3>Logout</h3>
                    </button>
                </form>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Settings</h1><br>
            <!-- tabs -->
            <div class="pcss3t pcss3t-effect-scale pcss3t-theme-1">
                <input type="radio" name="pcss3t" checked id="tab1" class="tab-content-first">
                <label for="tab1"><span class="material-icons-sharp">
                        person
                    </span>User-Profile</label>

                <input type="radio" name="pcss3t" id="tab2" class="tab-content-2">
                <label for="tab2"><span class="material-icons-sharp">
                        visibility
                    </span>Change Password</label>

                <input type="radio" name="pcss3t" id="tab3" class="tab-content-3">
                <label for="tab3"><span class="material-icons-sharp">
                        contact_support
                    </span>Contact</label>

                <input type="radio" name="pcss3t" id="tab4" class="tab-content-last">
                <label for="tab4"><span class="material-icons-sharp">
                        info
                    </span>About</label>

                <ul>
                    <li class="tab-content tab-content-first typography">
                        <div class="user_container">
                            <h2 class="form-heading">User Information</h2><br>
                            <div class="profile-photo">
                                <img src=<?php echo '/vault/Icons/' . $_SESSION['User_ID'] . '_user_icon.png' ?>>
                            </div>
                            <div class="content">
                                <form action="/vault/update-details">
                                    <div class="user-details">
                                        <div class="input-box">
                                            <span class="details">Username</span>
                                            <input type="text" name="username" placeholder=<?php echo $_SESSION['Username']; ?>>
                                        </div><br>
                                        <div class="input-box">
                                            <span class="details">Email</span>
                                            <input type="email" name="email" placeholder=<?php echo $_SESSION['email']; ?>>
                                        </div>
                                    </div>
                                    <div class="button">
                                        <input type="reset" value="Reset Changes">
                                        <input type="submit" value="Change">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </li>

                    <li class="tab-content tab-content-2 typography">
                        <h2 class="form-heading">Reset Master-Password</h2>
                        <div class="reset_password">
                            <form action="/vault/change-master" method="post" onsubmit="return validateForm()">
                                <div class="input-box">
                                    <span class="details">Password</span>
                                    <input type="text" name="reset-password" placeholder="Enter your password" id="reset-master-pwd" required>
                                </div>
                                <div class="input-box">
                                    <span class="details">Confirm Password</span>
                                    <input type="text" name="confirm-reset-password" placeholder="Confirm your password" id="confirm-reset-master-pwd" required>
                                </div>
                                <br>
                                <input class="reset" type="reset" value="Reset Changes">
                                <button type="submit">Submit</button>
                            </form>
                        </div>
                    </li>

                    <li class="tab-content tab-content-3 typography">
                        <div class="contact-container">
                            <h2 class="form-heading">Contact Us</h2>
                            <form action="#" method="post">
                                <div class="input-box">
                                    <span class="details">Name</span>
                                    <input type="text" placeholder="Enter your password" required>
                                </div>
                                <div class="input-box">
                                    <span class="details">Email</span>
                                    <input type="text" placeholder="Enter your password" required>
                                </div>
                                <div class="input-box">
                                    <span class="details">Message</span>
                                    <textarea id="message" name="message" rows="4" class="contact-textarea"></textarea>
                                </div>
                                <button type="submit" class="contact-submit">Submit</button>
                            </form>
                        </div>
                    </li>

                    <li class="tab-content tab-content-last typography">
                        Hello! We are Mahendra and Jeevesh, two students at GEC trying our hand for the first time at a password manager.
                    </li>
                </ul>
            </div>
        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="import-data">
                    <a href="/import-data" class="material-icons-sharp">download</a>
                </div>
                <div class="premium-buy">
                    <a href="/payment" class="material-icons-sharp">monetization_on</a>
                </div>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Hey, <b><?php echo $namerow[0] ?></b></p>
                    </div>
                    <div class="profile-photo">
                        <img src="<?php echo '/vault/Icons/' . $_SESSION['User_ID'] . '_user_icon.png' ?>" height="45px">
                    </div>
                </div>

            </div>
            <!-- End of Nav -->
            <div class="user-profile">
                <div class="flip-card" id="myFlipCard">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="logo" id="logo">
                                <?php
                                if ($_SESSION['Premium'])
                                    echo "<img src=/logos/password_manager_premium.jpg>";
                                else
                                    echo "<img src=/logos/password_manager.jpg>";
                                ?>
                            </div>
                        </div>
                        <div class="flip-card-back" id="text">
                            <p>When you say, "I have nothing to hide", you're saying, "I don't care about this right". <br>- Edward Snowden</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="index.js"></script>
    <script>
        const card = document.querySelector('.user-profile');

        card.addEventListener('click', () => {
            const cardInner = document.querySelector('.flip-card-inner');
            cardInner.classList.toggle('flipped');
            card.style.transform = card.style.transform === 'rotateY(180deg)' ? 'rotateY(0deg)' : 'rotateY(180deg)';
        });

        function validateForm() {
            var password = document.getElementById("reset-master-pwd").value;
            var confirmPassword = document.getElementById("confirm-reset-master-pwd").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>