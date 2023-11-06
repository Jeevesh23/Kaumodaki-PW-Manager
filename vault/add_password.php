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
    <title>Add Password</title>
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
                <!-- <a href="">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>User</h3>
                </a> -->
                <a href="/vault/settings">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="/vault/add-password" class="active">
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
            <h1>Add New Password</h1>
            <!-- passwords -->
            <div class="A_passwords">
                <div class="heading">
                    <h2>Add new Password</h2>
                    <form class="add_password" action="/vault/enter-password" method="post">
                        <div class="input-box">
                            <input type="text" placeholder="Website" name="Website" required><br>
                        </div>
                        <div class="input-box">
                            <input type="text" placeholder="Link" name="Link" required><br>
                        </div>
                        <div class="input-box">
                            <input type="text" placeholder="Username" name="Username" required><br>
                        </div>
                        <div class="input-box">
                            <input type="text" placeholder="Password" name="Password" id="Password" required><br>
                        </div>
                        <div class="input-box">
                            <button id="insertbutton"><span class="material-icons-sharp">
                                    casino
                                </span></button>
                        </div>
                        <div class="input-box">
                            Type:<br>
                            Password
                            <input type="radio" name="Type" value="0" id="passwordRadio" required>
                            Passphrase
                            <input type="radio" name="Type" value="1" id="passphraseRadio" required>
                        </div>
                        <div class="input-box">
                            Random Password Character Size: <span id="passwordSizeValue">16</span>
                            <input type="range" min="16" max="128" value="16" class="slider" id="passwordSizeSlider">
                        </div>

                        <div class="input-box">
                            Random Passphrase Word Size: <span id="passphraseSizeValue">5</span>
                            <input type="range" min="5" max="20" value="5" class="slider" id="passphraseSizeSlider">
                        </div>
                        <div class="input-box">
                            Reset Reminder:
                            <input type="checkbox" name="Reset" value="1">
                            <br>(Every 180 days from new password entry.)
                        </div>
                        <div class="input-box message-box">
                            <textarea placeholder="Description" name="Description" rows="5" cols="40" required></textarea><br>
                        </div>
                        <input type="hidden" value=<?php echo $_SESSION['User_ID']; ?> name="User_ID">
                        <input type="reset" value="Reset Changes" class="button_R" />
                        <input type="submit" value="Create" class="button_C" />
                    </form>
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
                <div class="premium-buy">
                    <a href="/payment" class="material-icons-sharp">monetization_on</span></a>
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
                        <img src="<?php echo '/vault/Icons/' . $_SESSION['User_ID'] . '_user_icon.png' ?>">
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
    <!-- <script src="orders.js"></script> -->
    <script src="index.js"></script>
    <script>
        const card = document.querySelector('.user-profile');

        card.addEventListener('click', () => {
            const cardInner = document.querySelector('.flip-card-inner');
            cardInner.classList.toggle('flipped');
            card.style.transform = card.style.transform === 'rotateY(180deg)' ? 'rotateY(0deg)' : 'rotateY(180deg)';
        });

        document.getElementById("insertbutton").addEventListener("click", function() {
            event.preventDefault();

            const selectedRadio = document.querySelector('input[name="Type"]:checked');

            if (selectedRadio) {
                const sizeSlider = selectedRadio.value === "0" ? document.getElementById("passwordSizeSlider") : document.getElementById("passphraseSizeSlider");
                const size = sizeSlider.value;

                const endpoint = selectedRadio.value === "0" ? '/vault/password' : '/vault/passphrase';

                fetch(`${endpoint}?size=${size}`)
                    .then(response => response.text())
                    .then(passwordString => {
                        document.getElementById("Password").value = passwordString;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });

        const passwordRadio = document.getElementById("passwordRadio");
        const passphraseRadio = document.getElementById("passphraseRadio");
        const passwordSizeSlider = document.getElementById("passwordSizeSlider");
        const passphraseSizeSlider = document.getElementById("passphraseSizeSlider");
        const passwordSizeValue = document.getElementById("passwordSizeValue");
        const passphraseSizeValue = document.getElementById("passphraseSizeValue");

        function updateSliderVisibility() {
            if (passwordRadio.checked) {
                passwordSizeSlider.style.display = "block";
                passphraseSizeSlider.style.display = "none";
            } else if (passphraseRadio.checked) {
                passwordSizeSlider.style.display = "none";
                passphraseSizeSlider.style.display = "block";
            }
        }

        passwordRadio.addEventListener("change", updateSliderVisibility);
        passphraseRadio.addEventListener("change", updateSliderVisibility);

        passwordSizeSlider.addEventListener("input", function() {
            passwordSizeValue.textContent = passwordSizeSlider.value;
        });

        passphraseSizeSlider.addEventListener("input", function() {
            passphraseSizeValue.textContent = passphraseSizeSlider.value;
        });

        updateSliderVisibility();
    </script>
</body>

</html>