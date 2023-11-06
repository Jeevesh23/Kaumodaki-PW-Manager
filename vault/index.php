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
    <style>
        .toggle-container {
            display: none;
        }

        .dropdown-content {
            cursor: pointer;
        }

        /* Styling for the heading */
        .heading {
            text-align: center;
        }

        /* Styling for the form and its elements */
        .add_password {
            max-width: 400px;
            margin: 0 auto;
        }

        .input-box {
            margin: 10px 0;
        }

        input[type="text"],
        input[type="password"],
        textarea {
            /* background-color: var(--color-white); */
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 10px;
        }

        input[type="radio"],
        input[type="checkbox"] {
            margin-right: 5px;
        }

        /* Styling for buttons */
        .button_R,
        .button_C {
            background-color: #0074d9;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button_R {
            background-color: #d9534f;
        }

        .button_C {
            background-color: #5cb85c;
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
            <h1>Dashboard</h1>
            <!-- passwords -->
            <div class="passwords">
                <h2>Your Passwords</h2>
                <table>
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
                                            <a href="#" onclick="myview(this)" class="view-button"><span class="material-icons-sharp" id=<?php echo "expbtn" . $row['Link']; ?>>expand_more</span>View</a>
<<<<<<< Updated upstream
                                            <a><span class="material-icons-sharp" onclick="toggleElement()">edit</span>Edit</a>
                                            <a href="#" onclick="mydelete(this)" class="del-button"><span class="material-icons-sharp">delete</span>Delete</a>
=======
                                            <a><span class="material-icons-sharp">edit</span>Edit</a>
                                            <a href="#" onclick="mydelete(this)" class="del-button"><span class="material-icons-sharp">delete</span>Delete</a>
                                        </div>
>>>>>>> Stashed changes
                                    </div>
                                </td>
                        </tr>
                        <tr id=<?php echo $row['Link']; ?> class="dropdownrow">
                            <td><span class="material-icons-sharp" onclick="myFunction(this)" id=<?php echo "expbtn" . $row['Link']; ?>>link</span>Link<br></td>
                            <td><span class="material-icons-sharp">person</span>Username<br></td>
                            <td><span class="material-icons-sharp">visibility</span>Password<br></td>
                            <td><span class="material-icons-sharp"></span>Expiry<br></td>
                            <td></td>
                            <td></td>

                        </tr>
                    <?php
                            }
                    ?>
                    </tbody>
<<<<<<< Updated upstream
                </table>
=======

                </table>

>>>>>>> Stashed changes
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

            <div class="toggle-container" id="myElement">
                <div class="heading">
                    <form class="add_password" action="/vault/enter-password" method="post">
                        <h2>Edit Password</h2>
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
                            <input type="password" placeholder="Password" name="Password" required><br>
                        </div>
                        <div class="input-box">
                            Type:<br>
                            Password
                            <input type="radio" name="Type" value="0" required>
                            Passphrase
                            <input type="radio" name="Type" value="1" required>
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
                        <input type="submit" value="Change" class="button_C" />
                    </form>
                </div>
            </div>
        </div>


    </div>
    <script src="index.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropdownBtns = document.querySelectorAll(".dropdown-btn");
            dropdownBtns.forEach((btn) => {
                btn.addEventListener("click", function() {
                    const dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            });
        });

        /* When the user clicks on the button, 
           toggle between hiding and showing the dropdown content */
        function myview(elem) {
            var dropdownContent = elem.closest('td').parentElement.nextElementSibling;
            if (dropdownContent.style.display === 'table-row') {
                dropdownContent.style.display = 'none';
            } else {
                dropdownContent.style.display = 'table-row';
            }
        }

        function mydelete(elem) {
            var delContent = elem.closest('tr').firstElementChild.textContent.trim();
            var result = confirm('Do you want to delete account ' + delContent + ' ?');
            if (result) {
                var xhr = new XMLHttpRequest();
                var url = "/vault/delete";

                xhr.open("POST", url, true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = xhr.responseText;
                        alert(response);
                        location.reload();
                    }
                };
                xhr.send("data=" + delContent);
            }
        }

        document.getElementById("dashboard-body").addEventListener("click", function(event) {
            var clickedElement = event.target;
            if (clickedElement.classList.contains("view-button")) {
                var clickedparElement = event.target.closest('td').parentElement;
                var reqelem = document.getElementById('dashboard-body');
                if (clickedparElement === reqelem.children[reqelem.children.length - 2]) {
                    var table = document.querySelector("main .passwords table");
                    var rows = table.querySelectorAll("tr");
                    var secondToLastRow = rows[rows.length - 2];
                    var tds = secondToLastRow.querySelectorAll("td");
                    if (tds[0].style.borderBottomLeftRadius != "0px") {
                        tds[0].style.borderBottomLeftRadius = "0px";
                        tds[tds.length - 1].style.borderBottomRightRadius = "0px";
                    } else {
                        tds[0].style.borderBottomLeftRadius = "2rem";
                        tds[tds.length - 1].style.borderBottomRightRadius = "2rem";
                    }
                }
            }
        });
        const card = document.querySelector('.user-profile');

        card.addEventListener('click', () => {
            const cardInner = document.querySelector('.flip-card-inner');
            cardInner.classList.toggle('flipped');
            card.style.transform = card.style.transform === 'rotateY(180deg)' ? 'rotateY(0deg)' : 'rotateY(180deg)';
        });


        function toggleElement() {
            var element = document.getElementById("myElement");
            if (element.style.display === "none" || element.style.display === "") {
                element.style.display = "block"; // Open the element
            } else {
                element.style.display = "none"; // Close the element
            }
        }
    </script>

</body>

</html>