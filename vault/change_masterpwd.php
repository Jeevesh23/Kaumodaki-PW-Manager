<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
include_once(__DIR__ . '/config/db.php');
include_once(__DIR__ . '/../authentication/func.php');
function decryptFile($filePath, $key)
{
    $encryptedContent = file_get_contents($filePath);
    $decryptedContent = openssl_decrypt($encryptedContent, "AES-256-CBC", $key);

    return $decryptedContent;
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $n_master_pwd = $_POST['reset-password'];
    $salt = getRandomStringRand();
    $hasheddata = hash('sha512', $n_master_pwd . $salt);
    $sql = "UPDATE `Credentials` SET `Password`='$hasheddata',`Salt`='$salt'";
    $req = mysqli_query($con, $sql);
    if (!$req) {
        echo "<script>alert('Updating master password failed!');</script>";
    } else {
        $pwdkey = hash_pbkdf2("sha512", $n_master_pwd, $_SESSION['salt2'], 500000, 64);
        $req = mysqli_query($con, "SELECT `Password`,`IV` FROM `User_Info` WHERE `User_ID`=" . $_SESSION['User_ID']);
        while ($row = $req->fetch_assoc()) {
            $pwd = openssl_decrypt($row['Password'], "AES-256-CBC", $_SESSION['pwdkey'], iv: hex2bin($row['IV']));
            $iv = openssl_random_pseudo_bytes(16);
            $newenc = openssl_encrypt($pwd, "AES-256-CBC", $pwdkey, iv: $iv);
            mysqli_query($con, "UPDATE `User_Info` SET `Password`='" . $newenc . "', `IV`='" . bin2hex($iv) . "' WHERE `User_ID`=" . $_SESSION['User_ID'] . " AND `Password`='" . $row['Password'] . "'");
        }
        $req = mysqli_query($con, "SELECT `File_Name` FROM `Files` WHERE `User_ID`=" . $_SESSION['User_ID']);
        define('SITE_ROOT', realpath(dirname(__FILE__)));
        $folderPath = SITE_ROOT . '/Files/';
        while ($row = $req->fetch_assoc()) {
            $fileLocation = $folderPath . $row['File_Name'];
            $decryptedContent = decryptFile($fileLocation, $_SESSION['pwdkey']);
            $encryptedContent = openssl_encrypt($decryptedContent, "AES-256-CBC", $pwdkey);
            file_put_contents($fileLocation, $encryptedContent);
        }
        $_SESSION['pwdkey'] = $pwdkey;
        echo "<script>alert('Master Password updated successfully!');</script>";
    }
    $con->close();
    header("Refresh:0.5,url=/vault/settings");
    die();
}
