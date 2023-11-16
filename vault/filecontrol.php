<?php
session_start();

define('SITE_ROOT', realpath(dirname(__FILE__)));
$folderPath = SITE_ROOT . '/Files/';
$file = isset($_GET['file']) ? $_GET['file'] : null;

if ($file) {
    $fileLocation = "$folderPath/$file";

    if (file_exists($fileLocation)) {
        $decryptedContent = decryptFile($fileLocation, $_SESSION['pwdkey']);
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($fileLocation) . "\"");
        echo $decryptedContent;
        exit;
    } else {
        echo '<script>alert("File not found.")</script>';
    }
}

function decryptFile($filePath, $key)
{
    $encryptedContent = file_get_contents($filePath);
    $decryptedContent = openssl_decrypt($encryptedContent, "AES-256-CBC", $key);

    return $decryptedContent;
}
