<?php
include_once(__DIR__ . '/config/db.php');
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
date_default_timezone_set('Asia/Kolkata');
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
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
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
    echo $statusMsg;
} else if (isset($_GET["submit"]) && $_GET["submit"] === "Retrieve") { ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>List of Files</title>
    </head>

    <body>
        <h1>List of Files</h1>
        <?php
        $size = $con->query("SELECT SUM(`Size`) FROM `Files` WHERE `User_ID`= " . $_SESSION['User_ID']);
        $row = mysqli_fetch_row($size);
        $sum = is_null($row[0]) ? "0" : $row[0];
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
                    while (false !== ($file = readdir($handle))) {
                        if ($file && isset($allowedFiles[$file]) && $allowedFiles[$file] == $_SESSION['User_ID']) {
                            // To remove trivial file pointers
                            if ($file != "." && $file != "..") {
                                $fileParam = urlencode($file);
                                echo "<li><a href='/vault/filecontrol?file=$fileParam'>$file</a></li>";
                            }
                        }
                    }
                    closedir($handle);
                }
            } else {
                echo "The folder does not exist.";
            }
            ?>
        </ul>
    </body>

    </html>
<?php
} else if (isset($_GET["submit"]) && $_GET["submit"] === "Delete") { ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Delete Files</title>
    </head>

    <body>
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
                    while (false !== ($file = readdir($handle))) {
                        if ($file && isset($allowedFiles[$file]) && $allowedFiles[$file] == $_SESSION['User_ID']) {
                            // To remove trivial file pointers
                            if ($file != "." && $file != "..") {
                                echo "<tr><td><input type='checkbox' name='files[]' value='$file'>$file</td><td>" . (filesize($folderPath . $file) / 1000) . "</td></tr>";
                            }
                        }
                    }
                    echo "</table>";
                    closedir($handle);
                }
            } else {
                echo "The folder does not exist.";
            }
            echo '<input type="submit" name="submit" value="Delete Selected Files">';
            echo '</form>';
            ?>
        </ul>
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
                    echo "File '$file' has been deleted.<br>";
                } else {
                    echo "File '$file' not found in the folder.<br>";
                }
            } else {
                echo "Error deleting record for file '$file': " . $conn->error . "<br>";
            }
        } else {
            echo "You are not authorized to delete file '$file'.<br>";
        }
        header('Refresh:3, url=/vault/uploads');
    }
} else { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Upload Files to our Password Manager</title>
    </head>

    <body>
        <form method="post" enctype="multipart/form-data" onsubmit="return checkFileSize()">
            Upload PDF, TXT, JPG, JPEG, PNG, or DOCX Files:-
            <input type="file" name="file" id="file">
            <input type="submit" name="submit" value="Upload">
        </form>
        <form>
            Get Files From Storage:-
            <input type="submit" name="submit" value="Retrieve">
        </form>
        <form>
            Delete Files From Storage:-
            <input type="submit" name="submit" value="Delete">
        </form>
    </body>

    <script>
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
    </script>

    </html>
<?php } ?>