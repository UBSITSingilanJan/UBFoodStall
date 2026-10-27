<?php
	require_once "../dbconn.php";
	require_once "../classes/Vendor.php";
	require_once "../classes/stall.php"; 
	session_start();

	if(!isset($_SESSION["ROLE"]) || $_SESSION["ROLE"] != "vendor"){
		session_destroy();
		header("location:../index.php");
		exit;
	}

	$vendorObj = new Vendor($pdo);
	$vendor = $vendorObj->getById($_SESSION["Vendor_ID"]);

	$stallObj = new Stall($pdo);
	$stall = $stallObj->getByVendor($_SESSION["Vendor_ID"]);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Vendor Dashboard</title>
</head>
<body>
	<h1>Welcome Vendor <?= htmlspecialchars($_SESSION['FNAME']) ?></h1>
		<a href="stall.php">My Stall</a> |
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
		<h3>My Stall</h3>
		<?php if($stall): ?>
			<table border="1" cellpadding="8">
				<tr>
					<th>Stall Name</th>
					<td><?= htmlspecialchars($stall['StallName']) ?></td>
				</tr>
				<tr>
					<th>Location</th>
					<td><?= htmlspecialchars($stall['Location']) ?></td>
				</tr>
				<tr>
					<th>Status</th>
					<td><?= htmlspecialchars($stall['Status']) ?></td>
				</tr>
			</table>
		<?php else: ?>
			<p>No stall yet. <a href="stall.php">Create your stall</a></p>
		<?php endif; ?>
</body>
</html>