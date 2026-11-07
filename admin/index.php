<?php
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";
	session_start();

	if(!isset($_SESSION["ROLE"]) || $_SESSION["ROLE"] != "admin"){
		session_destroy();
		header("Location: ../index.php");
		exit;
	}

	$conn = new Vendor($pdo);
	$rows = $conn -> fetch_data();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<h1>Welcome tanga admin</h1>
	<a href="logout.php">Logout</a> | 
	<a href="add_vendor.php">Add Vendor</a>

	<table border="1" cellpadding="8">
		<tr>
			<th>ID</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Contact</th>
			<th>Email</th>
			<th>Stall Name</th>
			<th>Actions</th>
		</tr>
		<?php
			foreach($rows as $row){
		?>
				<tr>
					<td><?php echo $row['Vendor_ID']; ?></td>
					<td><?php echo $row['firstname']; ?></td>
					<td><?php echo $row['lastname']; ?></td>
					<td><?php echo $row['contact']; ?></td>
					<td><?php echo $row['email']; ?></td>
					<td><?php echo $row['stallname']; ?></td>
					<td>
						<a href="update.php?id=<?php echo $row['Vendor_ID']; ?>">Update | </a>
						<a href="delete.php?id=<?php echo $row['Vendor_ID']; ?>">Delete</a>
					</td>
				</tr>
		<?php
			}
		?>
	</table>
</body>
</html>