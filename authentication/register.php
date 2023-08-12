<?php

$conn = mysqli_connect('localhost', 'root', '', 'PM_1');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];
require_once('func.php');
$salt = getRandomStringRand();
$hasheddata = hash('sha512', $password . $salt);

$sql = "INSERT INTO `Credentials` (`Username`, `Password`, `Salt`) VALUES ('$username','$hasheddata','$salt')";
$result = mysqli_query($conn, $sql);

if (!$result)
  echo "Connection failed";
else
  echo "User registered!";

$conn->close();

?>