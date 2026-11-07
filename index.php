<?php
	session_start();
	require_once "dbconn.php";

	if(isset($_SESSION["ROLE"])){
		if($_SESSION["ROLE"] == "admin"){
			header("Location: admin/index.php");
		} else {
			header("Location: customer/index.php");
		}
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<h1>UB FOOD STALL LOGIN</h1>
	<form method="post" action="login.php">
		<table>
			<tr>
				<td><input type="email" name="email" placeholder="Email" required></td>
			</tr>
			<tr>
				<td><input type="password" name="password" placeholder="Password" required></td>
			</tr>
			<tr>
				<td><a href="register.php">Register</a></td>
				<td><button name="login_btn">Login</button></td>
			</tr>
		</table>
	</form>
</body>
</html>