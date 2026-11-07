<?php
	require_once "dbconn.php";
	require_once "classes/customer.php";

	$conn = new Customer($pdo);

	if (isset($_POST['register_btn'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$email = $_POST['email'];
		$department = $_POST['department'];
		$year_level = $_POST['year_level'];
		$user_type = $_POST['user_type'];
		$password = $_POST['password'];

		$conn -> insert_data($firstname, $lastname, $email, $department, $year_level, $user_type, $password, "Customer");

		header("Location: index.php");
		exit();
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
	<h1>UB FOOD STALL REGISTER</h1>
	<form method="post">
		<table>
			<tr>
				<td><input type="text" name="firstname" placeholder="Firstname" required></td>
			</tr>
			<tr>
				<td><input type="text" name="lastname" placeholder="Lastname" required></td>
			</tr>
			<tr>
				<td><input type="email" name="email" placeholder="Email" required></td>
			</tr>
			<tr>
				<td>
					<select name="department">
						<option value="BSIT">BSIT</option>
						<option value="SON">SON</option>
						<option value="SOE">SOE</option>
						<option value="Pogi Ako">Pogi Ako</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="number" name="year_level" required></td>
			</tr>
			<tr>
				<td>
					<select name="user_type">
						<option value="Student">Student</option>
						<option value="Instructor">Instructor</option>
						<option value="Staff">Staff</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="password" name="password" placeholder="Password" required></td>
			</tr>
			<tr>
				<td><a href="index.php" name="cancel">Cancel</a></td>
				<td><button name="register_btn">Register</button></td>
			</tr>
		</table>
	</form>
</body>
</html>