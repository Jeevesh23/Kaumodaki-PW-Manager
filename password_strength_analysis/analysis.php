<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
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
require_once(__DIR__ . '/securecheck.php');
$con = mysqli_connect("db", "root", "MYSQL_ROOT_PASSWORD", "PM_1");

if (!$con) {
    die("Connection Error");
}

$sql = "SELECT `Password`,`IV`,`Wrd/Phr` FROM `User_Info` WHERE `Website`= '" . $_GET['data'] . "' AND `User_ID`= " . $_SESSION['User_ID'];
$result = mysqli_query($con, $sql);
if (!$result) {
    echo '<script>alert("Error in retrieving from database!")</script>';
    die();
} else if ($result->num_rows == 1) {
    while ($row = $result->fetch_assoc()) {
        $encpwd = $row['Password'];
        $type = $row['Wrd/Phr'];
        $iv = $row['IV'];
        $key = getenv('AES_KEY');
        $password = openssl_decrypt($encpwd, "AES-256-CBC", $key, iv: $iv);
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Password Strength Analysis</title>
    <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="/vault/style.css">
    <style>
        /* .input-box {
            margin: 10px 0;
        } */

        input[type="email"] {
            /* background-color: var(--color-white); */
            margin: 10px 0px;
            background-color: #fff;
            min-width: 200px;
            padding: 10px 20px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        #leak-search {
            margin: 10px;
        }

        input[type="submit"] {
            background-color: #0074d9;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <h2>Kaumodaki<br><span class="danger">PW Manager</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <a href="/vault" class="active">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="/vault/settings">
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
            <h1>Strength Analysis</h1>
            <?php
            if ($type == 0) {
                echo "<p>Password: " . $password . "</p>";
                if (passwordlen($password))
                    echo "<p>Password is safe in length.</p><br>";
                else
                    echo "<p>Password length too less to be secure!</p><br>";
                echo "<p>Basic Password Entropy: " . pwd_entropy($password) . " bits.</p>";
                if (pwd_entropy($password) >= 60)
                    echo "<p>Password safe in strength! Check out advanced strength analysis for more info!</p><br>";
                else
                    echo "<p>Password is weak! Check out advanced strength analysis for more info!</p><br>";
            } else if ($type == 1) {
                echo "<p>Passphrase: " . $password . "</p>";
                if (phraselen($password))
                    echo "<p>Passphrase is safe in length.</p><br>";
                else
                    echo "<p>Passphrase is too small!</p><br>";
                echo "<p>Basic Passphrase Entropy: " . phr_entropy(phraselen($password)) . " bits.</p>";
                if (phr_entropy(phraselen($password)) >= 60)
                    echo "<p>Passphrase safe in strength! Check out advanced strength analysis for more info!</p><br>";
                else
                    echo "<p>Passphrase is weak! Check out advanced strength analysis for more info!</p><br>";
            }
            echo "<p>Do you want to perform a leak lookup analysis to see if your credentials were leaked?</p>";
            echo "<p>Continue with current email: '" . $_SESSION['email'] . "' or use a enter a different one.<p>";
            ?>
            <br>
            <form action="/strength-analysis/leak" method="POST">
                <input type="email" name="email" placeholder="Email">
                <input type="submit" name="submit" id="leak-search" value="Leak Lookup Search">
            </form>
            <br>
            <p>Perform an advanced strength analysis of your password using ZXCVBN (Premium Feature)!</p>
            <form action="/advanced-strength" method="POST">
                <input type="hidden" name="type" value=<?php echo $type ?>>
                <input type="hidden" name="password" value=<?php echo $password ?>><br>
                <input type="submit" name="submit" value="Advanced Strength Analysis!">
            </form>
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
                        <p>Hey, <b><?php echo $_SESSION['Username']; ?></b></p>
                    </div>
                    <div class="profile-photo">
                        <img src="<?php echo '/vault/Icons/' . $_SESSION['User_ID'] . '_user_icon.png' ?>">
                    </div>
                </div>

            </div>

            <div class="user-profile" id="user-profile-card">
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

    <script src="/password_strength_analysis/index.js"></script>
    <script>
        const card = document.querySelector('.user-profile');

        card.addEventListener('click', () => {
            const cardInner = document.querySelector('.flip-card-inner');
            cardInner.classList.toggle('flipped');
            card.style.transform = card.style.transform === 'rotateY(180deg)' ? 'rotateY(0deg)' : 'rotateY(180deg)';
        });
    </script>

</body>

</html>