<?php
	require_once "../dbconn.php";
	require_once "../classes/Vendor.php";
	session_start();

	if(!isset($_SESSION["ROLE"]) || $_SESSION["ROLE"] != "admin"){
		session_destroy();
		header("location:../index.php");
		exit;
	}

	$vendorObj = new Vendor($pdo);
	$vendors = $vendorObj->getAll();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Admin - Vendors</title>
</head>
<body>
	<h1>Welcome Admin <?php echo $_SESSION['FNAME']; ?></h1>
	<a href="profile.php">My Profile</a> |
	<a href="logout.php">Logout</a>
	<br><br><hr>

	<a href="create.php">+ Add Vendor</a>
	<br><br>

	<table border="1" cellpadding="8">
		<tr>
			<th>ID</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Contact</th>
			<th>Email</th>
		</tr>
		<?php
			foreach($vendors as $v){
		?>
				<tr>
					<td><?php echo $v['Vendor_ID']; ?></td>
					<td><?php echo $v['First_Name']; ?></td>
					<td><?php echo $v['Last_Name']; ?></td>
					<td><?php echo $v['Contact']; ?></td>
					<td><?php echo $v['Email']; ?></td>
				</tr>
		<?php
			}
		?>
	</table>
</body>
</html>