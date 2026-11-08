<?php
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";

	$conn = new Vendor($pdo);

	$id = $_GET['id'];

	$row = $conn -> show_update_data($id);

	if (isset($_POST['update_vendor'])) {
		$firstname = htmlspecialchars($_POST['firstname']);
		$lastname = htmlspecialchars($_POST['lastname']);
		$contact = $_POST['contact'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$location = $_POST['location'];
		$stallname = $_POST['stallname'];
		$Vendor_ID = $_POST['Vendor_ID'];

		$conn -> update_data($Vendor_ID, $firstname, $lastname, $contact, $email, $password, "Vendor", $location, $stallname);

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
				<td><input type="text" name="firstname" placeholder="Firstname" value="<?php echo $row['firstname']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="text" name="lastname" placeholder="Lastname" value="<?php echo $row['lastname']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="tel" name="contact" placeholder="Contact Number" value="<?php echo $row['contact']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="email" name="email" placeholder="Email" value="<?php echo $row['email']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="password" name="password" placeholder="Password" value="<?php echo $row['password']; ?>" required></td>
			</tr>
			<tr>
				<td><h3>Stall Information</h3></td>
			</tr>
			<tr>
				<td><input type="text" name="location" placeholder="Location" value="<?php echo $row['location']; ?>" required></td>
			</tr>
			<tr>
				<td><input type="text" name="stallname" placeholder="Stall Name" value="<?php echo $row['stallname']; ?>" required></td>
			</tr>
			<tr>
				<td><a href="index.php">Cancel</a></td>
				<td><button name="update_vendor">Update</button></td>
				<td><input type="hidden" name="Vendor_ID" value="<?php echo $row['Vendor_ID']; ?>"></td>
			</tr>
		</table>
	</form>
	<script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
</body>
</html>
