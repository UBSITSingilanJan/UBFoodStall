<?php
	require_once "../dbconn.php";
	require_once "../classes/Vendor.php";
	session_start();

	if(!isset($_SESSION["ROLE"]) || $_SESSION["ROLE"] != "admin"){
		session_destroy();
		header("location:../index.php");
		exit;
	}

	$message = "";

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$fname    = trim($_POST["fname"] ?? "");
		$lname    = trim($_POST["lname"] ?? "");
		$contact  = trim($_POST["contact"] ?? "");
		$email    = trim($_POST["email"] ?? "");

		if($fname=="" || $lname=="" || $contact=="" || $email==""){
			$message = "All fields are required.";
		} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$message = "Invalid email address.";
		} else {
			$vendorObj = new Vendor($pdo);
			$vendorObj->create($fname, $lname, $contact, $email);
			$message = "Vendor successfully added.";
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Add Vendor</title>
	<style>
		table{ margin:auto; }
		table tr td{ padding:5px; }
		table tr td input{ padding:5px; }
	</style>
</head>
<body>
	<h2 style="text-align:center;"><?php echo $message; ?></h2>

	<form method="post">
		<table>
			<tr>
                <td colspan="2"><b>Vendor Info</b></td>
            </tr>
			<tr>
                <td><input type="text" name="fname" placeholder="First Name" required></td>
            </tr>
			<tr>
                <td><input type="text" name="lname" placeholder="Last Name" required></td>
            </tr>
			<tr>
                <td><input type="text" name="contact" placeholder="Contact" required></td>
            </tr>
			<tr>
                <td><input type="email" name="email" placeholder="Email" required></td>
            </tr>
			<tr>
				<td>
					<a href="index.php">Cancel</a> |
					<button name="register">Save</button>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>