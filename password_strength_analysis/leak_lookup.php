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
            <h1>Credentials Leak Check</h1>
            <?php
                session_start();
                if (!isset($_SESSION['User_ID'])) {
                    header("Location: /authentication");
                    die();
                }
                $url = 'https://leak-lookup.com/api/search';

                $api_key = getenv('LEAK_LOOKUP_API_KEY');

                $email = empty($_POST['email']) ? $_SESSION['email'] : $_POST['email'];
                $data = array(
                    'key' => $api_key,
                    'type' => 'email_address',
                    'query' => $email
                );

                // Initialize the CURL session
                $ch = curl_init();

                // Set CURL options
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                // Error checking
                if (curl_errno($ch)) {
                    echo 'CURL error: ' . curl_error($ch);
                }
                // Close CURL session
                curl_close($ch);

                // Handling the API response
                if ($response) {
                    echo "Email checked:" . $email . "<br>";
                    // Decode JSON response into an array and print
                    $result = json_decode($response, true);
                    if ($result['error'] === 'false') {
                        $message = $result['message'];
                        if (!empty($message)) {
                            foreach ($message as $siteName => $breachData) {
                                echo "Site Name: $siteName<br>";
                            }
                            echo "You should change your credentials!<br>";
                            echo "If the leak corresponds to a particular website, changing the corresponding credentials will help.<br>";
                            echo "If not, the leak could be a part of an internet-wide attack. It's best to search about it and change appropriate credentials.<br>";
                        } else {
                            echo "You are safe for now! Practice good internet safety techniques!";
                        }
                    } else {
                        echo "Error: " . $data['error'];
                    }
                } else {
                    // Handle the case when there is no response
                    echo 'No response from the API';
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