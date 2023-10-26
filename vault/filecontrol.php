<?php
session_start();

define('SITE_ROOT', realpath(dirname(__FILE__)));
$folderPath = SITE_ROOT . '/Files/';
$file = isset($_GET['file']) ? $_GET['file'] : null;

if ($file) {
    $fileLocation = "$folderPath/$file";

    if (file_exists($fileLocation)) {
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($fileLocation) . "\"");
        readfile($fileLocation);
        exit;
    } else {
        echo 'File not found.';
    }
}
