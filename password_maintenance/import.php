<!DOCTYPE html>
<html>

<head>
    <title>Advanced Strength Analysis</title>
    <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="/vault/style.css">
    <style>
        input[type="text"] {
            /* background-color: var(--color-white); */
            margin: 10px 0px;
            background-color: #fff;
            min-width: 200px;
            padding: 10px 20px;
            box-shadow: var(--box-shadow);
            z-index: 1;
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
            <h1>Credentials Leak Check</h1>
            <?php
                session_start();
                if (!isset($_SESSION['User_ID'])) {
                    header("Location: /authentication");
                    die();
                }
                if ($_SERVER['REQUEST_METHOD'] !== "POST") {
                    $servername = "db";
                    $username = "root";
                    $password = "MYSQL_ROOT_PASSWORD";
                    $dbname = "PM_1";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $dec_key = getenv('AES_KEY');
                    $query = "SELECT * FROM `User_Info` WHERE `User_ID`=" . $_SESSION['User_ID'];

                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        $filename = "/var/www/html/password_maintenance/sql/User_Table.sql";
                        $file = fopen($filename, "w");

                        while ($row = $result->fetch_assoc()) {
                            $sqlData = "INSERT INTO `User_Table` (`Website`, `Username`, `Link`, `Password`, `Add_Date`, `Description`, `Wrd/Phr`) VALUES ('" . $row["Website"] . "', '" . $row["Username"] . "', '" . $row["Link"] . "','" . openssl_decrypt($row["Password"], "AES-256-CBC", $dec_key, iv: $row["IV"]) . "', '" . $row["Add_Date"] . "', '" . $row["Description"] . "', '" . $row["Wrd/Phr"] . "');";
                            fwrite($file, $sqlData);
                        }

                        fclose($file);
                ?>
                        <html>

                        <head>
                            <title>Password Details Export</title>
                        </head>

                        <body>
                            <h1>Details Export </h1>
                            <p>We understand that you may no longer be interested in using our product, or may just want to perform a regular local backup of your details.</p>
                            <p>Do not worry, we offer an "easy" way of doing so!</p>
                            <ol>
                                <li>
                                    Enter a password below, which will be used to encrypt/decrypt your database.
                                </li>
                                <li>
                                    After clicking on Export, download the encrypted database on being prompted.
                                </li>
                                <li>
                                    Enter the following command :<br>
                                    openssl enc -d -aes-256-cbc -md sha512 -pbkdf2 -iter 1000000 -salt -in "Path to encrypted file" -out "Path to new unencrypted file" -k "Password"
                                </li>
                                <li>
                                    Voila! The new unencrypted database is the one with your details securely stored!
                                </li>
                            </ol>
                            <form method="POST">
                                <input type="text" name="db-pwd" placeholder="Enter DB Import Password" id="db-pwd" required>
                                <input type="submit" name="submit" value="Import">
                            </form>
                        </body>

                        </html>
                <?php
                    } else {
                        echo "No results found.";
                        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/vault';
                        header("Refresh:0.5,url= $referer");
                        die();
                    }

                    $conn->close();
                } else {
                    $filename = "/var/www/html/password_maintenance/sql/User_Table.sql";
                    $encryptionPassword = $_POST['db-pwd'];
                    $encryptedFilename = "User_Table_encrypted.sql.enc";
                    $filePath = '/var/www/html/password_maintenance/sql/' . $encryptedFilename;
                    $command = "openssl enc -aes-256-cbc -md sha512 -pbkdf2 -iter 1000000 -salt -in $filename -out $filePath -k $encryptionPassword";
                    shell_exec($command);
                    echo "<script>alert('Data exported as SQL table and encrypted with a password to $encryptedFilename.')</script>";
                    if (file_exists($filePath)) {
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                        header('Content-Length: ' . filesize($filePath));
                        ob_clean();
                        flush();
                        readfile($filePath);
                        unlink($filePath);
                        unlink($filename);
                    }
                    header("Refresh:0.5,url=/vault");
                    die();
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