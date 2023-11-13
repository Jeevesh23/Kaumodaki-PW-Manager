<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if ($_POST['tnc']) {
		session_start();

		$conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$username = $_POST['username'];
		$email = $_POST['email'];
		$hashemail = hash('md5', $email);
		$password = $_POST['password'];
		$key = getenv('AES_KEY');
		$sql = "SELECT `Salt`, `Password` FROM `Credentials` WHERE `Email`='" . openssl_encrypt($email, "AES-256-CBC", $key) . "'";
		$result = mysqli_query($conn, $sql);

		if (!$result) {
			header("Refresh:3, url= /authentication");
			echo '<script>alert("Connection failed")</script>';
			$conn->close();
			exit();
		} else if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$hasheddata = hash('sha512', $password . $row["Salt"]);
				if ($row["Password"] == $hasheddata) {
					echo '<script>alert("User exists! Sign in instead.")</script>';
					header("Refresh:3,url=/authentication");
					$conn->close();
					exit();
				}
			}
			header("Refresh:3,url=/authentication");
			echo '<script>alert("Email already taken! Use another one.")</script>';
			$conn->close();
			exit();
		}
		require_once(__DIR__ . '/func.php');
		$salt = getRandomStringRand();
		$hasheddata = hash('sha512', $password . $salt);

		$_SESSION['Username'] = $username;
		$_SESSION['email'] = $email;
		$_SESSION['hashemail'] = $hashemail;
		$_SESSION['password'] = $hasheddata;
		$_SESSION['salt'] = $salt;

		$conn->close();
		setcookie($_SESSION['hashemail'], 'register', time() + 360, path: '/');
		header("Location: /authentication/otp");
		exit();
	} else {
		echo "<script>alert('Terms and Conditions not accepted!');</script>";
		header("Refresh:0.5,url=/authentication");
		exit();
	}
} else {
	header("Location: /authentication");
	exit();
}
