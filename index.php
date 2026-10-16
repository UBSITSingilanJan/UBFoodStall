<?php
	session_start();
	require_once "dbconn.php";   

	if(isset($_SESSION["ROLE"])){
		if($_SESSION["ROLE"] == "admin"){
			header("location:admin/index.php");
		} else {
			header("location:vendor/index.php");
		}
		exit;
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>UBFoodStall - Login</title>
</head>
<body>
	<h1>UBFoodStall</h1>
	<h3>Login</h3>

	<form method="post" action="login.php">
		<input type="text" name="username" placeholder="Email" required><br><br>
		<input type="password" name="password" placeholder="Password" required><br><br>
		<button name="login_btn">Login</button>
	</form>
</body>
</html>
