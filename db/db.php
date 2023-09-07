<?php
$conn=mysqli_connect('db','root','MYSQL_ROOT_PASSWORD');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$dbmake=mysqli_query($conn,'CREATE DATABASE IF NOT EXISTS PM_1');
if (!$dbmake) {
    die("Database could not be created! " . $conn->connect_error);
}
$dbconn=mysqli_connect('db','root','MYSQL_ROOT_PASSWORD','PM_1');
$sql="CREATE TABLE IF NOT EXISTS `Credentials`(
    `User_ID` int(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `Username` varchar(256),
    `Email` varchar(256),
    `Password` varchar(1024),
    `Salt` varchar(256),
    `Secret_Key` varchar(64),
    `IV` binary(16)
  )";
$tbconn=mysqli_query($dbconn,$sql);
if (!$tbconn) {
    die("Table could not be created! " . $conn->connect_error);
}
$sql="CREATE TABLE IF NOT EXISTS `Password_Reset` (
    `User_ID` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `Username` varchar(256) DEFAULT NULL,
    `Email` varchar(256) DEFAULT NULL,
    `Reset_Key` varchar(128) DEFAULT NULL,
    `ExpDate` datetime DEFAULT NULL,
    FOREIGN KEY(`User_ID`) REFERENCES `Credentials`(`User_ID`)
  )";
$tbconn=mysqli_query($dbconn,$sql);
if (!$tbconn) {
    die("Table could not be created! " . $conn->connect_error);
}
$sql="CREATE TABLE IF NOT EXISTS `User_Info` (
    `User_ID` int(8) NOT NULL,
    `Description` varchar(512) DEFAULT NULL,
    `Link` varchar(512) DEFAULT NULL,
    `Password` varchar(256) DEFAULT NULL,
    FOREIGN KEY(`User_ID`) REFERENCES `Credentials`(`User_ID`)
  )";
$tbconn=mysqli_query($dbconn,$sql);
if (!$tbconn) {
    die("Table could not be created! " . $conn->connect_error);
}
$dbconn->close();
$conn->close();
?>
