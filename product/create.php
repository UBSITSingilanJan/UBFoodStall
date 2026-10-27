<?php
	require_once "../dbconn.php";
	require_once "../classes/stall.php";
	require_once "../classes/product.php";
	session_start();

	if(!isset($_SESSION["ROLE"]) || $_SESSION["ROLE"] != "vendor"){
		session_destroy();
		header("location:../index.php");
		exit;
	}

	$stallObj = new Stall($pdo);
	$productObj = new Product($pdo);
	$message = "";
	$statuses = ["Available", "Not Available"];

	$stall = $stallObj->getByVendor($_SESSION["Vendor_ID"]);
	if(!$stall){
		header("location:index.php");
		exit;
	}

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$name   = trim($_POST["name"] ?? "");
		$price  = trim($_POST["price"] ?? "");
		$status = trim($_POST["status"] ?? "");

		if($name=="" || $price=="" || $status==""){
			$message = "All fields are required.";
		} elseif(strlen($name) > 100){
			$message = "Product name must be 100 characters or less.";
		} elseif(!is_numeric($price) || $price < 0 || $price > 99999999.99){
			$message = "Invalid price.";
		} elseif(!in_array($status, $statuses)){
			$message = "Invalid status.";
		} else {
			try {
				$productObj->create($stall["Stall_ID"], $name, number_format($price, 2, ".", ""), $status);
				$message = "Product successfully added.";
			} catch(PDOException $e){
				$message = "A database error occurred. Please try again.";
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Add Product</title>
	<style>
		table{ margin:auto; }
		table tr td{ padding:5px; }
		table tr td input{ padding:5px; }
	</style>
</head>
<body>
	<h2 style="text-align:center;"><?= htmlspecialchars($message) ?></h2>

	<form method="post">
		<table>
			<tr>
				<td><b>Product Info</b></td>
			</tr>
			<tr>
				<td><input type="text" name="name" placeholder="Product Name" required></td>
			</tr>
			<tr>
				<td><input type="number" name="price" placeholder="Price" step="0.01" min="0" required></td>
			</tr>
			<tr>
				<td>
					<select name="status">
						<?php foreach($statuses as $s): ?>
							<option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<a href="index.php">Back</a> |
					<button name="save">Save</button>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>