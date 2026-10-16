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
	<title>Vendor Dashboard</title>
</head>
<body>
	<h1>Welcome Vendor <?= htmlspecialchars($_SESSION['FNAME']) ?></h1>
	<a href="logout.php">Logout</a>
	<br><br><hr>

	<h3>My Information</h3>
	<table border="1" cellpadding="8">
		<tr>
            <th>Vendor ID</th>
            <td><?= htmlspecialchars($vendor['Vendor_ID']) ?></td>
        </tr>
		<tr>
            <th>First Name</th>
            <td><?= htmlspecialchars($vendor['First_Name']) ?></td>
        </tr>
		<tr>
            <th>Last Name</th><
            td><?= htmlspecialchars($vendor['Last_Name']) ?></td>
        </tr>
		<tr>
            <th>Contact</th>
            <td><?= htmlspecialchars($vendor['Contact']) ?></td>
        </tr>
		<tr>
            <th>Email</th>
            <td><?= htmlspecialchars($vendor['Email']) ?></td>
        </tr>
	</table>
</body>
</html>