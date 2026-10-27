<?php
	require_once "../dbconn.php";
	require_once "../classes/stall.php";
	session_start();

	if(!isset($_SESSION["ROLE"]) || $_SESSION["ROLE"] != "vendor"){
		session_destroy();
		header("location:../index.php");
		exit;
	}

	$myStall = new Stall($pdo);
	$message = "";
	$statuses = ["Open", "Closed"];

	$vendor_id = $_SESSION["Vendor_ID"];

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$name     = trim($_POST["name"] ?? "");
		$location = trim($_POST["location"] ?? "");
		$status   = trim($_POST["status"] ?? "");

		if($name=="" || $location=="" || $status==""){
			$message = "All fields are required.";
		} elseif(strlen($name) > 50){
			$message = "Stall name must be 50 characters or less.";
		} elseif(strlen($location) > 100){
			$message = "Location must be 100 characters or less.";
		} elseif(!in_array($status, $statuses)){
			$message = "Invalid status.";
		} else {
			try {
				$stall = $myStall->getByVendor($vendor_id);

				if($stall){
					$count = $myStall->update($stall["Stall_ID"], $vendor_id, $name, $location, $status);
					$message = ($count > 0) ? "Stall updated." : "No changes were made.";
				} else {
					$myStall->create($vendor_id, $name, $location, $status);
					$message = "Stall successfully created.";
				}
			} catch(PDOException $e){
				$message = "A database error occurred. Please try again.";
			}
		}
	}
	$stall = $myStall->getByVendor($vendor_id);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My Stall</title>
</head>
<body>
	<h1>My Stall</h1>
	<a href="index.php">Back to Dashboard</a> |
	<a href="logout.php">Logout</a>
	<br><br><hr>

	<h3><?= htmlspecialchars($message) ?></h3>

	<?php if($stall): ?>
		<h3>Stall Information</h3>
		<table border="1" cellpadding="8">
			<tr>
				<th>Stall ID</th>
				<td><?= htmlspecialchars($stall['Stall_ID']) ?></td>
			</tr>
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
		<br>
		<h3>Edit Stall</h3>
	<?php else: ?>
		<h3>You have no stall yet. Create one:</h3>
	<?php endif; ?>

	<form method="post">
		<table>
			<tr>
				<td><input type="text" name="name" placeholder="Stall Name" value="<?= htmlspecialchars($stall['StallName'] ?? '') ?>" required></td>
			</tr>
			<tr>
				<td><input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($stall['Location'] ?? '') ?>" required></td>
			</tr>
			<tr>
				<td>
					<select name="status">
						<?php foreach($statuses as $s): ?>
							<option value="<?= htmlspecialchars($s) ?>" <?= (($stall['Status'] ?? 'Open') == $s) ? 'selected' : '' ?>>
								<?= htmlspecialchars($s) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><button name="save"><?= $stall ? "Update Stall" : "Create Stall" ?></button></td>
			</tr>
		</table>
	</form>
</body>
</html>