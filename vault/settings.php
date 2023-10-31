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
        main {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .main_container {
            display: flex;
            /* padding-top: 10%; */
            /* align-items: center; */
            /* justify-content: center; */
            margin: auto;
            height: 100vh;
            background: #ece7e700;
            font-family: 'Poppins', sans-serif;
        }

        .accordion_tab {
            max-width: 640px;
            padding: 10px;
        }

        .accordion_tab_wrapper {
            display: flex;
            gap: 20px;
        }

        .accordion_tab_group .tab {
            cursor: pointer;
            padding: 10px 20px;
            margin: 8px 2px;
            background: #fcd5ce70;
            width: 100%;
            display: inline-block;
            color: #23211f;
            border-radius: 10px;
            box-shadow: 0 0.5rem 0.8rem #eadad81e;
            border: 2px solid #bc6c2520;
        }

        .accordion_tab_group .tab:hover {
            background: #000000;
            color: #fff;
        }

        #one:checked~.accordion_tab_group #one-tab,
        #two:checked~.accordion_tab_group #two-tab,
        #three:checked~.accordion_tab_group #three-tab,
        #four:checked~.accordion_tab_group #four-tab {
            background: #d8d5d5;
            color: #0b0a0a;
        }

        .accordion_tab_wrapper input[type="radio"] {
            display: none;
        }

        .accordion_tab_contents {
            background: #f0eeec;
            color: black;
            padding: 20px;
            border-radius: 10px;
        }

        #one:checked~.accordion_tab_contents #one-tab-content,
        #two:checked~.accordion_tab_contents #two-tab-content,
        #three:checked~.accordion_tab_contents #three-tab-content,
        #four:checked~.accordion_tab_contents #four-tab-content {
            display: block;
        }

        .accordion_tab_contents .accordion_tab_content {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <!-- <img src="images/profile.jpg"> -->
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
                <!-- <a href="#">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>User</h3>
                </a> -->
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
            <h1><br>Settings</h1>
            <div class="main_container">
                <div class="accordion_tab">
                    <div class="accordion_tab_wrapper">
                        <input type="radio" id="one" name="accordion_group" class="radio" checked>
                        <input type="radio" id="two" name="accordion_group" class="radio">
                        <input type="radio" id="three" name="accordion_group" class="radio">
                        <input type="radio" id="four" name="accordion_group" class="radio">
                        <div class="accordion_tab_group">
                            <label for="one" class="tab" id="one-tab">Profile</label>
                            <label for="two" class="tab" id="two-tab">Change<br>Passsword</label>
                            <label for="three" class="tab" id="three-tab">Contact Us</label>
                            <label for="four" class="tab" id="four-tab">About</label>
                        </div>
                        <div class="accordion_tab_contents">
                            <div class="accordion_tab_content" id="one-tab-content">
                                <h2 class="accordion_tab_title">User-Profile</h2>
                                <p class="accordion_tab_description">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore mollitia eveniet magnam. Accusamus explicabo, velit libero expedita dignissimos maiores minus excepturi nihil commodi, optio alias modi harum placeat reprehenderit aspernatur!</p>
                            </div>
                            <div class="accordion_tab_content" id="two-tab-content">
                                <h2 class="accordion_tab_title">Change-Password</h2>
                                <p class="accordion_tab_description">Lorem ipsum dolor sit amet consectetur adipisicing elit. Perspiciatis itaque libero magnam aspernatur voluptas rem aut sunt accusamus fugit incidunt quo dicta ipsa deserunt, similique, soluta, deleniti omnis provident odio!</p>
                            </div>
                            <div class="accordion_tab_content" id="three-tab-content">
                                <h2 class="accordion_tab_title">Contact Us</h2>
                                <p class="accordion_tab_description">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos, assumenda commodi. Enim sapiente incidunt, eum beatae iste perferendis laboriosam tenetur, animi necessitatibus officia architecto excepturi autem molestiae eius, fuga nostrum.
                            </div>
                            <div class="accordion_tab_content" id="four-tab-content">
                                <h2 class="accordion_tab_title">About</h2>
                                <p class="accordion_tab_description">What is Framesworks?
                                    In computer programming, a software framework is an abstraction in which software, providing generic functionality, canbe selectively changed by additional user-written code, thus providing application-specific software</p>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <img src="<?php echo '/vault/Icons/' . $_SESSION['User_ID'] . '_user_icon.png' ?>">
                    </div>
                </div>

            </div>
            <!-- End of Nav -->
        </div>
    </div>


    <script src="index.js"></script>
</body>

</html>