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

	$messages = [
		"deleted"  => "Product deleted.",
		"notfound" => "Product not found.",
		"error"    => "A database error occurred. Please try again."
	];
	$message = $messages[$_GET["msg"] ?? ""] ?? "";

	$stall = $stallObj->getByVendor($_SESSION["Vendor_ID"]);
	$products = $stall ? $productObj->getByStall($stall["Stall_ID"]) : [];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My Products</title>
</head>
<body>
	<h1>My Products</h1>
	<a href="../vendor/index.php">Back to Dashboard</a> |
	<a href="../vendor/mystall.php">My Stall</a> |
	<a href="../vendor/logout.php">Logout</a>
	<br><br><hr>

	<?php if(!$stall): ?>
		<p>You need a stall before adding products. <a href="../vendor/mystall.php">Create your stall</a></p>
	<?php else: ?>
		<h3><?= htmlspecialchars($message) ?></h3>

		<a href="create.php">+ Add Product</a>
		<br><br>

		<table border="1" cellpadding="8">
			<tr>
				<th>ID</th>
				<th>Product Name</th>
				<th>Price</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
			<?php if(count($products) == 0): ?>
				<tr>
					<td colspan="5">No products yet.</td>
				</tr>
			<?php endif; ?>
			<?php foreach($products as $p): ?>
				<tr>
					<td><?= htmlspecialchars($p['Product_ID']) ?></td>
					<td><?= htmlspecialchars($p['ProductName']) ?></td>
					<td><?= number_format($p['Price'], 2) ?></td>
					<td><?= htmlspecialchars($p['Status']) ?></td>
					<td>
						<a href="update.php?id=<?= $p['Product_ID'] ?>">Edit</a> |
						<form method="post" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this product?');">
							<input type="hidden" name="product_id" value="<?= $p['Product_ID'] ?>">
							<button name="delete">Delete</button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</body>
</html>