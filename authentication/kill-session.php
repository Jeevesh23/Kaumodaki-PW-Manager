<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == "GET" && $_GET['action'] == "reset" && $_SESSION['User_ID'] == $_GET['userid']) {
    session_start();
    session_destroy();
    echo
    '<script>
        alert("Account access has been removed! Secure account by re-setting master password.");
    </script>';
    header("Refresh:1,url=/authentication");
} else {
    echo
    '<script>
        alert("Error! The link access is invalid!");
    </script>';
    header("Refresh:1,url=/authentication");
}

exit();
