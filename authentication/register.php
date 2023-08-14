<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'PM_1');
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT `Salt`,`Password` FROM `Credentials` WHERE `Username`='$username'";
$result = mysqli_query($conn, $sql);

if (!$result)
	echo "Connection failed";
else if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$hasheddata = hash('sha512', $password . $row["Salt"]);
		if ($row["Password"] == $hasheddata) {
			echo "User exists! Sign in instead.";
			$conn->close();
			exit();
		}
	}
}
require_once('func.php');
$salt = getRandomStringRand();
$hasheddata = hash('sha512', $password . $salt);

$_SESSION['username'] = $username;
$_SESSION['password'] = $hasheddata;
$_SESSION['salt'] = $salt;

$conn->close();

header("Location: register-form.php");
exit();

?>