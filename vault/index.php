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

$query = "SELECT * FROM `User_Info` WHERE `User_ID`=" . $_SESSION['User_ID'] . " ORDER BY `Description` ASC";
$result = mysqli_query($con, $query);
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
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost:8000/vault/">
    <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Vault</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        body.blur {
            overflow: hidden;
        }

        body.blur::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('');
            backdrop-filter: blur(5px);
            pointer-events: none;
            z-index: 9998;
        }

        .toggle-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            max-height: 80vh;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            overflow-y: auto;
            border-radius: 30px;
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
        }

        .toggle-container::-webkit-scrollbar {
            width: 4px;
        }

        .toggle-container::-webkit-scrollbar-thumb {
            background-color: transparent;
        }

        body.blur * {
            user-select: none;
            pointer-events: none;
        }

        body.blur .toggle-container * {
            pointer-events: auto;
        }

        .toggle-container .dropdown-content {
            cursor: pointer;
        }

        /* Styling for the heading */
        .toggle-container .heading {
            text-align: center;
        }

        .toggle-container .head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toggle-container #closeButton {
            cursor: pointer;
        }

        /* Styling for the form and its elements */
        .toggle-container .add_password {
            max-width: 400px;
            margin: 0 auto;
        }

        .toggle-container .input-box {
            margin: 10px 0;
        }

        .toggle-container #insertbutton {
            cursor: pointer;
        }

        .toggle-container input[type="text"],
        .toggle-container input[type="password"],
        .toggle-container textarea {
            /* background-color: var(--color-white); */
            background-color: #fff;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 10px;
        }

        .toggle-container input[type="radio"],
        input[type="checkbox"] {
            margin-right: 5px;
        }

        /* Styling for buttons */
        .toggle-container .button_R,
        .button_C {
            background-color: #0074d9;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .toggle-container .button_R {
            background-color: #d9534f;
        }

        .toggle-container .button_C {
            background-color: #5cb85c;
        }

        main .dropdownrow input {
            background-color: var(--color-white);
            border: 0px;
            width: 100%;
            border-radius: 20px;
            text-align: center;
            display: inline-block;
            height: 20px;
            color: var(--color-dark-variant);
        }

        main .dropdownrow button {
            background-color: var(--color-white);
            height: 20px;
            color: var(--color-dark-variant);
            cursor: pointer;
        }

        main .dropdown:hover {
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
            <h1>Dashboard</h1>
            <!-- passwords -->
            <div class="passwords">
                <h2>Your Passwords</h2>
                <table style="width:100%;">
                    <thead>
                        <tr>
                            <th>Website</th>
                            <th>Link</th>
                            <th>Add Date</th>
                            <th>Word/Phrase</th>
                            <th>Reset Reminder</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="dashboard-body">
                        <tr>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                $changeColour = (strtotime(date("Y-m-d")) > strtotime($row['Add_Date']) + 86400 * 180);
                                $rowClass = $changeColour && $row['RST'] ? 'remind-color' : '';
                                echo '<tr class="' . $rowClass . '">';
                            ?>
                                <td>
                                    <?php echo $row['Website']; ?>
                                </td>
                                <td>
                                    <a href="<?php echo $row['Link']; ?>" target="_blank"><?php echo $row['Link']; ?></a>
                                </td>
                                <td>
                                    <?php echo $row['Add_Date']; ?>
                                </td>
                                <td>
                                    <?php
                                    if ($row['Wrd/Phr'] == 1) echo 'P';
                                    else echo 'W';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($row['RST'] == 1) echo
                                    '<span class="material-icons-sharp">
                                        check
                                    </span>';
                                    else echo
                                    '<span class="material-icons-sharp">
                                        close
                                    </span>';
                                    ?>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <span class="material-icons-sharp">more_vert</span>
                                        <div class="dropdown-content">
                                            <div class="view-button">
                                                <a href="#"><span class="material-icons-sharp" id=<?php echo "expbtn" . $row['Link']; ?>>expand_more</span>View</a>
                                            </div>
                                            <div class="edit-button">
                                                <a><span class="material-icons-sharp">edit</span>Edit</a>
                                            </div>
                                            <div onclick="mydelete(this)" class="del-button">
                                                <a href="#"><span class="material-icons-sharp">delete</span>Delete</a>
                                            </div>
                                            <div onclick="mypwstrength(this)" class="strength-button">
                                                <a><span class="material-icons-sharp">fitness_center</span>Password Strength</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                        </tr>
                        <tr id=<?php echo $row['Link']; ?> class="dropdownrow" id="view-details" data-website="<?php echo $row['Website']; ?>">
                            <td style="max-width: 20px"><span class="material-icons-sharp">person <br><input type="text" placeholder="username" id="namefield_<?php echo $row['Website']; ?>" readonly></span></td>
                            <td style="max-width: 20px"><button class="material-icons-sharp" onclick="viewPwd(this)" style="margin-right: 24px;color:#d9534f">visibility_off</button><button onclick="copyPwd(this)" class="material-icons-sharp" style="color:chocolate;">content_paste</button><br><input type="password" placeholder="Password" id="passwordfield_<?php echo $row['Website']; ?>" readonly></td>
                            <td style="max-width: 20px"><span class="material-icons-sharp">update <br><input type="text" placeholder="Expiry" id="datefield_<?php echo $row['Website']; ?>" readonly></span></td>
                            <td style="max-width: 20px"><input type="hidden" id="hiddenpwd_<?php echo $row['Website']; ?>" readonly></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php
                            }
                    ?>
                    </tbody>
                </table>

                </table>

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
            <br>
            <div class="toggle-container" id="myElement">
                <div class="heading">
                    <form class="add_password" id="edit-password" action="/vault/edit-entry" method="post">
                        <div class="head">
                            <h2>Edit Password</h2>
                            <button id="closeButton" type="button" onclick="closeEdit()">
                                <span class="material-icons-sharp">close</span>
                            </button>
                        </div>
                        <div class="input-box">
                            <input type="text" placeholder="Website" name="Website" id="websiteField" readonly><br>
                        </div>
                        <div class="input-box">
                            <input type="text" placeholder="Link" name="Link" id="linkField" required><br>
                        </div>
                        <div class="input-box">
                            <input type="text" placeholder="Username" name="Username" id="usernameField" required><br>
                        </div>
                        <div class="input-box">
                            <input type="text" placeholder="Password" name="Password" id="passwordField" required><br>
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
                            <textarea placeholder="Description" name="Description" id="descriptionField" rows="5" cols="40" required></textarea><br>
                        </div>
                        <input type="hidden" value=<?php echo $_SESSION['User_ID']; ?> name="User_ID">
                        <input type="reset" value="Reset Changes" class="button_R" id="button_R" />
                        <input type="submit" value="Change" class="button_C" id="button_R" />
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="index.js"></script>
    <script src="/vault/logic.js"></script>

</body>

</html>