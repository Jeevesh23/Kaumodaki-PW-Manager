<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
require_once(__DIR__ . '/securecheck.php');
$con = mysqli_connect("db", "root", "MYSQL_ROOT_PASSWORD", "PM_1");

if (!$con) {
    die("Connection Error");
}

$sql = "SELECT `Password`,`IV`,`Wrd/Phr` FROM `User_Info` WHERE `Website`= '" . $_POST['data'] . "' AND `User_ID`= " . $_SESSION['User_ID'];
$result = mysqli_query($con, $sql);
if (!$result) {
    echo "Error in retrieving from database!";
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
    <title>Advanced Strength Analysis</title>
    <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="/vault/style.css">
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
            <h1>Advanced Strength Analysis</h1>
            <?php
                session_start();
                if (!isset($_SESSION['User_ID'])) {
                    header("Location: /authentication");
                    die();
                }
                if (!isset($_SESSION['Premium']) || $_SESSION['Premium'] != 1) {
                    echo '<script>alert("Only available to premium users!");</script>';
                    header("Refresh:0.5,url=/strength-analysis");
                    exit();
                }
                $newIncludePath = '/app/vendor/';
                set_include_path($newIncludePath);
                include_once('autoload.php');

                use ZxcvbnPhp\Zxcvbn;

                //$pass is the password or passphrase.
                //$type indicates whether password(0) or passphrase(1)
                $zxcvbn = new Zxcvbn();
                $pass = $_POST['password'];
                $type = $_POST['type'];
                $weak = $zxcvbn->passwordStrength($pass);
                function display_array($arr)
                {
                    foreach ($arr as $key => $val) {
                        if (gettype($val) != 'array')
                            if ($key != 'guesses_log10')
                                echo $key . ' : ' . $val . '<br>';
                            else
                                echo 'guesses_log2 : ' . $val / log(2, 10) . '<br>';
                    }
                }
                function display_array_rec($arr)
                {
                    foreach ($arr as $key => $val) {
                        if (gettype($val) != 'array')
                            echo $key . ' : ' . $val . '<br>';
                        else {
                            echo $key . ':<br>';
                            display_array_rec($val);
                        }
                    }
                }
                function display_sequence_arr($arr)
                {
                    foreach ($arr as $key => $val) {
                        if (gettype($val) == 'object' || gettype($val) == 'array') {
                            if (preg_match("/^[0-9]+$/", $key))
                                echo '<br>';
                            echo $key . " : ";
                            display_sequence_arr($val);
                        } else {
                            if ($key == 'password')
                                echo '<br>';
                            echo $key . ' : ' . $val . '<br>';
                        }
                    }
                }
                echo '<p>guesses is the estimated guesses needed to crack password.</p><br>';
                echo '<p>guesses_log2 is complex entropy in bits.<br>';
                echo '<p>score is an integer from 0-4, 0 indicating the very most guessable passwords.</p><br>';
                echo '<p>calc_time is how long it took zxcvbn to calculate an answer, in milliseconds.</p><br><br>';
                display_array($weak);
                echo '<br>';
                echo '<p>The following parameters show crack time estimations, based on a few scenarios:-</p><br>';
                echo '<p>online_throttling_100_per_hour is online attack on a service that ratelimits password auth attempts.</p><br>';
                echo '<p>online_no_throttling_10_per_second is online attack on a service that doesn\'t ratelimit, or where an attacker has outsmarted ratelimiting.</p><br>';
                echo '<p>offline_slow_hashing_1e4_per_second is an offline attack. Assumes multiple attackers, proper user-unique salting, and a slow hash function w/ moderate work factor, such as bcrypt, scrypt, PBKDF2.</p><br>';
                echo 'offline_fast_hashing_1e10_per_second is an offline attack with user-unique salting but a fast hash function like SHA-1, SHA-256 or MD5. A wide range of reasonable numbers anywhere from one billion - one trillion guesses per second, depending on number of cores and machines, ballparking at 10B/sec.</p><br><br>';
                display_array_rec($weak['crack_times_display']);
                echo '<br>';
                echo '<p>feedback shows warning and suggestions about password when score <=2.</p> <br><br>';
                display_array_rec($weak['feedback']);
                echo '<br>';
                if (!$type) {
                    echo '<p>sequence denotes whether the password could be easily bruteforced or is a part of a common dictionary.</p><br><br>';
                    display_sequence_arr($weak['sequence']);
                    echo '<br>';
                }
            ?>
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

    <script>
        const darkMode = document.querySelector('.dark-mode');

        function enableDarkMode() {
            localStorage.setItem('darkMode', 'enabled');
            document.body.classList.toggle('dark-mode-variables');
            darkMode.querySelector('span:nth-child(1)').classList.toggle('active');
            darkMode.querySelector('span:nth-child(2)').classList.toggle('active');
        }

        function disableDarkMode() {
            localStorage.setItem('darkMode', 'disabled');
            document.body.classList.toggle('dark-mode-variables');
            darkMode.querySelector('span:nth-child(1)').classList.toggle('active');
            darkMode.querySelector('span:nth-child(2)').classList.toggle('active');
        }

        function toggleDarkMode() {
            if (localStorage.getItem('darkMode') === 'enabled') {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        }

        if (localStorage.getItem('darkMode') === 'enabled') {
            enableDarkMode();
        }
        darkMode.addEventListener('click', toggleDarkMode);

        if (localStorage.getItem('darkMode') === 'enabled') {
            enableDarkMode();
        }

        const sideMenu = document.querySelector('aside');
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');

        menuBtn.addEventListener('click', () => {
            sideMenu.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            sideMenu.style.display = 'none';
        });
    </script>
    
</body>

</html>