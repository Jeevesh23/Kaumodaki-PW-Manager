<?php
session_start();
$servername = "db";
$username = "root";
$password = "MYSQL_ROOT_PASSWORD";
$dbname = "PM_1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM Files WHERE User_ID=" . $_SESSION['User_ID'];

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $filename = "exported.sql";
    $file = fopen($filename, "w");

    while ($row = $result->fetch_assoc()) {
        $sqlData = "INSERT INTO Files (User_ID, File_Name, Upload_Date, `Size`) VALUES ('" . $row["User_ID"] . "', '" . $row["File_Name"] . "', '" . $row["Upload_Date"] . "','" . $row["Size"] . "');\n";
        fwrite($file, $sqlData);
    }

    fclose($file);

    $encryptionPassword = "your_encryption_password";
    $encryptedFilename = "exported_encrypted.sql";
    $command = "openssl enc -aes-256-cbc -md sha512 -pbkdf2 -iter 1000000 -salt -in $filename -out $encryptedFilename -k $encryptionPassword";
    shell_exec($command);

    echo "Data exported as SQL table and encrypted with a password to $encryptedFilename. \n";
} else {
    echo "No results found.";
}

$conn->close();
