<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
include_once(__DIR__ . '/config/db.php');
$pwdkey = $_SESSION['pwdkey'];
$sql = "SELECT `Password`,`IV` FROM `User_Info` WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Website`='" . $_POST['Website'] . "'";
$req = mysqli_query($con, $sql);
if ($req->num_rows > 0) {
    while ($row = $req->fetch_assoc()) {
        $decpwd = openssl_decrypt($row['Password'], 'AES-256-CBC', $pwdkey, iv: hex2bin($row['IV']));
    }
}

$website = $_POST['Website'];
$new_desc = $_POST['Description'];
$new_user = $_POST['Username'];
$new_link = $_POST['Link'];
$new_type = $_POST['Type'];
$new_reset = isset($_POST['Reset']) ? 1 : 0;
$new_secret = $_POST['Password'];
if ($new_secret !== $decpwd) {
    $sql = "SELECT `Old_Hash` FROM `Old_Passwords` WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Website`='" . $_POST['Website'] . "'";
    $req = mysqli_query($con, $sql);
    if ($req->num_rows > 0) {
        while ($row = $req->fetch_assoc()) {
            if (password_verify($new_secret, $row['Old_Hash'])) {
                echo "<script>alert('This password has been recently used!')</script>";
                header("Refresh:0.5,url=/vault");
                die();
            }
        }
    }
    if ($req->num_rows === 5) {
        $sql = "DELETE FROM `Old_Passwords` WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Website`='" . $_SESSION['website'] . "' AND `Add_Date` IS NOT NULL ORDER BY `Add_Date` ASC LIMIT 1";
        $req = mysqli_query($con, $sql);
    }
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("AES-256-CBC"));
    $encrypted = openssl_encrypt($new_secret, "AES-256-CBC", $pwdkey, iv: $iv);
    $hexiv = bin2hex($iv);
    $addformat = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
    $_SESSION['website'] = $website;
    $_SESSION['adddate'] = $adddate = date("Y-m-d H:i:s", $addformat);
    $_SESSION['hash'] = password_hash($new_secret, PASSWORD_DEFAULT);
    $sql = "INSERT INTO `Old_Passwords`(`User_ID`,`Website`,`Link`,`Old_Hash`,`Add_Date`) VALUES (" . $_SESSION['User_ID'] . ", '$website', '$new_link', '" . $_SESSION['hash'] . "', CONVERT_TZ(NOW(), 'UTC',  'Asia/Kolkata') )";
    $req = mysqli_query($con, $sql);
    if (!$req)
        die("Error in updating database!");
    $sql = "UPDATE `User_Info` SET `Password`='" . $encrypted . "', `IV`='$hexiv', `Add_Date`= CONVERT_TZ(NOW(), 'UTC',  'Asia/Kolkata') WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Website`='" . $_POST['Website'] . "'";
    $req = mysqli_query($con, $sql);
    if (!$req)
        die("Error in updating database!");
}
$sql = "UPDATE `Old_Passwords` SET `Link`='" . $new_link . "' WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Website`='" . $_POST['Website'] . "'";
$req = mysqli_query($con, $sql);
if (!$req)
    die("Error in updating database!");
$sql = "UPDATE `User_Info` SET `Link`='$new_link', `Username`='$new_user', `Description`='$new_desc', `Wrd/Phr`='$new_type', `RST`='$new_reset' WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Website`='" . $_POST['Website'] . "'";
$req = mysqli_query($con, $sql);
if (!$req)
    die("Error in updating database!");
echo "<script>alert('All details were updated successfully!');</script>";
header("Refresh:0.5,url=/vault");
