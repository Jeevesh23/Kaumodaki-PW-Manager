<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
include_once(__DIR__ . '/config/db.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $website = $_POST["data"];
    $sql1 = "DELETE FROM `Old_Passwords` WHERE `Website`='$website'";
    $req1 = mysqli_query($con, $sql1);
    if ($req1) {
        $sql2 = "DELETE FROM `User_Info` WHERE `Website`='$website'";
        $req2 = mysqli_query($con, $sql2);
        if ($req2) {
            echo "Successfully deleted entry!";
        } else if (!$req2)
            echo "Error in deletion of entry!";
    } else if (!$req1)
        echo "Error in deletion of entry!";
}
