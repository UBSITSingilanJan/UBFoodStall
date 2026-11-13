<?php
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";

	$conn = new Vendor($pdo);

	if (isset($_POST['add_vendor'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$contact = $_POST['contact'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$location = $_POST['location'];
		$stallname = $_POST['stallname'];

		$conn -> insert_data($firstname, $lastname, $contact, $email, $password, "Vendor", $location, $stallname);

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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
</head>
<body>
	<h1>UB FOOD STALL REGISTER</h1>
	<form method="post">
		<table>
			<tr>
				<td><h3>Customer Information</h3></td>
			</tr>
			<tr>
				<td><input type="text" name="firstname" placeholder="Firstname" required></td>
			</tr>
			<tr>
				<td><input type="text" name="lastname" placeholder="Lastname" required></td>
			</tr>
			<tr>
				<td><input type="tel" name="contact" placeholder="Contact Number" required></td>
			</tr>
			<tr>
				<td><input type="email" name="email" placeholder="Email" required></td>
			</tr>
			<tr>
				<td><input type="password" name="password" placeholder="Password" required></td>
			</tr>
			<tr>
				<td><h3>Stall Information</h3></td>
			</tr>
			<tr>
				<td><input type="text" name="location" placeholder="Location" required></td>
			</tr>
			<tr>
				<td><input type="text" name="stallname" placeholder="Stall Name" required></td>
			</tr>
			<tr>
				<td><a href="index.php">Cancel</a></td>
				<td><button name="add_vendor">Add Vendor</button></td>
			</tr>
		</table>
	</form>
	<script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
</body>
</html>
