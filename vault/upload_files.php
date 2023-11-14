<?php
include_once(__DIR__ . '/config/db.php');
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
if (!$_SESSION['Premium']) {
    echo "<script>alert('Only available to premium users!');</script>";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/vault';
    header("Refresh:0.5,url= $referer");
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
date_default_timezone_set('Asia/Kolkata');
require_once(__DIR__ . '/config/db.php');
$namequery = "SELECT `Username` FROM `Credentials` WHERE `User_ID`=" . $_SESSION['User_ID'];
$nameres = mysqli_query($con, $namequery);
$namerow = $nameres->fetch_row();
$statusMsg = '';
define('SITE_ROOT', realpath(dirname(__FILE__)));
$targetDir = SITE_ROOT . '/Files/';
if (isset($_POST["submit"]) && $_POST["submit"] === "Upload") {
    if (!empty($_FILES["file"]["name"])) {
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $size = $con->query("SELECT SUM(`Size`) FROM `Files` WHERE `User_ID`=" . $_SESSION['User_ID']);
        $row = mysqli_fetch_row($size);
        $sum = is_null($row[0]) ? "0" : $row[0];
        $search = $con->query("SELECT * FROM `Files` WHERE `User_ID`= " . $_SESSION['User_ID'] . " AND `File_Name`='$fileName'");
        if ($search->num_rows === 0) {
            $allowTypes = array('pdf', 'txt', 'jpg', 'png', 'jpeg', 'docx');
            if (in_array($fileType, $allowTypes)) {
                $fileSize = $_FILES["file"]["size"];
                if ($sum + $fileSize < 16700000) {
                    $tempFilePath = $_FILES["file"]["tmp_name"];
                    $fileContent = file_get_contents($tempFilePath);
                    $encryptedContent = openssl_encrypt($fileContent, 'AES-256-CBC', $_SESSION['pwdkey']);
                    if (file_put_contents($targetFilePath, $encryptedContent) !== false) {
                        $insert = $con->query("INSERT INTO `Files` (`User_ID`,`File_Name`, `Upload_Date`,`Size`) VALUES ('" . $_SESSION['User_ID'] . "','" . $fileName . "', CONVERT_TZ(NOW(), 'UTC',  'Asia/Kolkata'), $fileSize)");
                        if ($insert) {
                            $statusMsg = "The file " . $fileName . " has been uploaded successfully.";
                        } else {
                            $statusMsg = "File upload failed, please try again.";
                        }
                    } else {
                        $statusMsg = "Sorry, there was an error uploading your file.";
                    }
                } else
                    $statusMsg = "Cannot upload files exceeding 16MB total vault size!";
            } else {
                $statusMsg = 'Sorry, only PDF, TXT, DOCX, JPG, JPEG, and PNG files are allowed to be uploaded.';
            }
        } else {
            $statusMsg = 'Please do not reupload your files!';
        }
    } else {
        $statusMsg = 'Please select a file to upload.';
    }
    echo "<script>alert('" . $statusMsg . "');</script>";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/vault/uploads';
    header("Refresh:0.5,url= $referer");
    die();
} else if (isset($_GET["submit"]) && $_GET["submit"] === "Retrieve") { ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>List of Files</title>
        <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
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
                    <a href="/vault">
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
                    <a href="/vault/uploads" class="active">
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
                <h1>List of Files</h1>
                <?php
                $size = $con->query("SELECT SUM(`Size`) FROM `Files` WHERE `User_ID`= " . $_SESSION['User_ID']);
                $row = mysqli_fetch_row($size);
                $sum = is_null($row[0]) ? "0" : $row[0];
                $avail = 16700000 - $sum;
                ?>
                <h3>Available space:<?php echo $avail / 1000000; ?> MB</h3>
                <ol>
                    <?php
                    $folderPath = SITE_ROOT . '/Files/';
                    $sql = "SELECT `File_Name`, `User_ID` FROM `Files`";
                    $result = $con->query($sql);

                    $allowedFiles = [];

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $file = $row["File_Name"];
                            $user = $row["User_ID"];
                            $allowedFiles[$file] = $user;
                        }
                    }
                    if (is_dir($folderPath)) {
                        $filesInDir = array();

                        if ($handle = opendir($folderPath)) {
                            while (false !== ($file = readdir($handle))) {
                                if ($file && isset($allowedFiles[$file]) && $allowedFiles[$file] == $_SESSION['User_ID']) {
                                    if ($file != "." && $file != "..") {
                                        $filesInDir[] = $file;
                                    }
                                }
                            }
                            closedir($handle);

                            natcasesort($filesInDir);
                            foreach ($filesInDir as $file) {
                                $fileParam = urlencode($file);
                                echo "<li><a href='/vault/filecontrol?file=$fileParam' style='color: blue; font-size: 15px;'>$file</a></li>";
                            }
                        }
                    } else {
                        echo '<script>alert("The folder does not exist.")</script>';
                    }
                    ?>
                </ol>
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
            </div>


        </div>
        <!-- <script src="orders.js"></script> -->
        <script src="index.js"></script>
    </body>

    </html>
<?php
} else if (isset($_GET["submit"]) && $_GET["submit"] === "Delete") { ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Delete Files</title>
        <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
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
                    <a href="/vault">
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
                    <a href="/vault/uploads" class="active">
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
                <h1>Delete Files</h1>
                <?php
                $sum = 0;
                $files = $con->query("SELECT `File_Name`,`Size` FROM `Files` WHERE `User_ID`=" . $_SESSION['User_ID']);
                while ($row = $files->fetch_assoc())
                    $sum = is_null($row['Size']) ? "0" : $sum + $row['Size'];
                $avail = 16700000 - $sum;
                ?>
                <h3>Available space:<?php echo $avail / 1000000; ?> MB</h3>
                <ul>
                    <?php
                    $folderPath = SITE_ROOT . '/Files/';
                    $sql = "SELECT `File_Name`, `User_ID` FROM `Files`";
                    $result = $con->query($sql);

                    $allowedFiles = [];

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $file = $row["File_Name"];
                            $user = $row["User_ID"];
                            $allowedFiles[$file] = $user;
                        }
                    }
                    if (is_dir($folderPath)) {
                        if ($handle = opendir($folderPath)) {
                            echo '<form method="post" action="/vault/uploads">';
                            echo "<table>";
                            echo "<tr><th>File Name</th><th>File Size (KB)</th></tr>";
                            $fileList = array();

                            while (false !== ($file = readdir($handle))) {
                                if ($file && isset($allowedFiles[$file]) && $allowedFiles[$file] == $_SESSION['User_ID']) {
                                    if ($file != "." && $file != "..") {
                                        $filePath = $folderPath . $file;
                                        $fileSizeKB = filesize($filePath) / 1000;
                                        $fileList[] = array('name' => $file, 'size' => $fileSizeKB);
                                    }
                                }
                            }
                            usort($fileList, function ($a, $b) {
                                return strcasecmp($a['name'], $b['name']);
                            });
                            foreach ($fileList as $fileInfo) {
                                $file = $fileInfo['name'];
                                $fileSizeKB = $fileInfo['size'];
                                echo "<tr><td><input type='checkbox' name='files[]' value='$file'>$file</td><td>$fileSizeKB</td></tr>";
                            }
                            echo "</table>";
                            closedir($handle);
                        }
                    } else {
                        echo '<script>alert("The folder does not exist.")</script>';
                    }
                    echo '<input type="submit" name="submit" value="Delete Selected Files">';
                    echo '</form>';
                    ?>
                </ul>
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
            </div>


        </div>
        <!-- <script src="orders.js"></script> -->
        <script src="index.js"></script>
    </body>

    </html>
<?php
} else if (isset($_POST["submit"]) && $_POST["submit"] === "Delete Selected Files") {
    $filesToDelete = isset($_POST['files']) ? $_POST['files'] : [];

    $user = $_SESSION['User_ID'];

    foreach ($filesToDelete as $file) {
        $sql = "SELECT * FROM `Files` WHERE `File_Name` = '$file' AND `User_ID` = '$user'";
        $result = $con->query($sql);

        if ($result->num_rows === 1) {

            $deleteSql = "DELETE FROM `Files` WHERE `File_Name` = '$file' AND `User_ID` = '$user'";
            if ($con->query($deleteSql) === TRUE) {

                // Delete the file from the folder
                $fileLocation = SITE_ROOT . "/Files/$file";

                if (file_exists($fileLocation)) {
                    unlink($fileLocation); // Delete file function in PHP
                    echo "<script>alert('File \'$file\' has been deleted.');</script>";
                } else {
                    echo "<script>alert('File \'$file\' not found in the folder.');</script>";
                }
            } else {
                echo "<script>alert('Error deleting record for file \'$file\': " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('You are not authorized to delete file \'$file\'.');</script>";
        }
    }
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/vault/uploads';
    header("Refresh:0.5,url= $referer");
    die();
} else { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Upload Files</title>
        <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
        <style>
            main .upload-file {
                font-size: 16px;
                height: 80%;
                text-align: center;
                position: relative;

            }

            .upload-file .upload {
                border: 5px dotted black;
                border-color: black;
                border-radius: 20px;
                padding: 30px;
                box-sizing: border-box;
                padding: 2rem;
            }

            main h2 {
                font-size: 26px;
                line-height: 1;
                color: #454cad;
                margin-bottom: 0;
            }

            main img {
                text-align: center;
                margin: 0 auto .5rem auto;
                width: auto;
                height: auto;
                max-width: 60px;
            }

            main .btn {
                margin: .5rem .5rem 1rem .5rem;
                font-weight: 700;
                font-size: 14px;
                border-radius: .7rem;
                outline: none;
                padding: 0 1rem;
                height: 36px;
                line-height: 36px;
                color: #fff;
                background: #454cad;
            }

            #dropArea input[type="submit"] {
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
                    <a href="/vault">
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
                    <a href="/vault/uploads" class="active">
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
                <div class="upload-file">
                    <h2>File & Image Upload</h2><br><br>
                    <div class="upload" id="upload">
                        <form method="post" enctype="multipart/form-data" onsubmit="return checkFileSize()" id="dropArea">
                            <br>Upload PDF, TXT, JPG, JPEG, PNG, or DOCX Files:-
                            <img src="/vault/Images/cloud.png">
                            <input type="file" name="file" id="file" class="btn"><br>
                            <input type="submit" name="submit" value="Upload" class="btn">
                        </form>
                        <div id="uploadedFilesDisplay" class="uploaded-files"></div>
                    </div><br>
                    <div class="delete_retrive">
                        <form id="fileList">
                            Get Files From Storage<br>
                            <input type="submit" name="submit" value="Retrieve" class="btn">
                        </form>
                        <form>
                            Delete Files From Storage<br>
                            <input type="submit" name="submit" value="Delete" class="btn">
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
        <!-- <script src="orders.js"></script> -->
        <script src="index.js"></script>
    </body>

    <script>
        const dropArea = document.getElementById('upload');
        const fileInput = document.getElementById('file');
        const uploadedFilesDisplay = document.getElementById('uploadedFilesDisplay');

        document.addEventListener('DOMContentLoaded', function() {
            const dropArea = document.getElementById('dropArea');

            dropArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropArea.classList.add('drag-over');
            });

            dropArea.addEventListener('dragleave', function() {
                dropArea.classList.remove('drag-over');
            });

            dropArea.addEventListener('drop', function(e) {
                e.preventDefault();
                dropArea.classList.remove('drag-over');
                handleFiles(e.dataTransfer.files);
            });

            dropArea.addEventListener('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                const fileInput = document.getElementById('file');
                fileInput.files = files;
            }
        });

        function checkFileSize() {
            var fileInput = document.getElementById('file');
            var fileSize = file.fileSize;

            var serverMaxSize = 16700000;

            if (fileSize > serverMaxSize) {
                alert("File size exceeds the server's limit. Please upload a smaller file.");
                return false;
            }

            return true;
        }

        const card = document.querySelector('.user-profile');

        card.addEventListener('click', () => {
            const cardInner = document.querySelector('.flip-card-inner');
            cardInner.classList.toggle('flipped');
            card.style.transform = card.style.transform === 'rotateY(180deg)' ? 'rotateY(0deg)' : 'rotateY(180deg)';
        });
    </script>

    </html>
<?php } ?>