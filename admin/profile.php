<?php
	require_once "../dbconn.php";
	require_once "../classes/Vendor.php";
	session_start();

	if(!isset($_SESSION["ROLE"]) || $_SESSION["ROLE"] != "vendor"){
		session_destroy();
		header("location:../index.php");
		exit;
	}

	$vendorObj = new Vendor($pdo);
	$vendor = $vendorObj->getById($_SESSION["Vendor_ID"]);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Vendor Profile</title>
</head>
<body>
	<h1>Vendor Profile</h1>
	<a href="index.php">Back to Dashboard</a> |
	<a href="logout.php">Logout</a>
	<br><br><hr>

	<h3>My Information</h3>
	<table border="1" cellpadding="8">
		<tr>
            <th>Vendor ID</th><td><?php echo $vendor['Vendor_ID']; ?></td>
        </tr>
		<tr>
            <th>First Name</th><td><?php echo $vendor['First_Name']; ?></td>
        </tr>
		<tr>
            <th>Last Name</th><td><?php echo $vendor['Last_Name']; ?></td>
        </tr>
		<tr>
            <th>Contact</th><td><?php echo $vendor['Contact']; ?></td>
        </tr>
		<tr>
            <th>Email</th><td><?php echo $vendor['Email']; ?></td>
        </tr>
	</table>
</body>
</html>