<?php
	session_start();

	// Only logged-in vendors can see this page.
	if (!isset($_SESSION["Vendor_ID"])) {
		header("Location: ../index.php");
		exit;
	}

	require_once "../dbconn.php";

	// Read-only: the vendor's name and stall (stall name/location are managed by admin).
	$info = null;
	try {
		$prep = $pdo->prepare("SELECT vendor.firstname, vendor.lastname, vendor.email, vendor.contact, food_stall.Stall_ID, food_stall.stallname, food_stall.location, food_stall.status
			FROM vendor LEFT JOIN food_stall ON vendor.Vendor_ID = food_stall.Vendor_ID WHERE vendor.Vendor_ID = :id");
		$prep->execute([":id" => $_SESSION["Vendor_ID"]]);
		$info = $prep->fetch();
	} catch (PDOException $e) {
		$info = null;
	}

	$vendor = [
		"id"        => (int) $_SESSION["Vendor_ID"],
		"first"     => $info["firstname"] ?? ($_SESSION["FNAME"] ?? "Vendor"),
		"last"      => $info["lastname"] ?? ($_SESSION["LNAME"] ?? ""),
		"email"     => $info["email"] ?? "",
		"contact"   => $info["contact"] ?? "",
		"stall"     => ($info && $info["Stall_ID"]) ? [
			"Stall_ID"  => (int) $info["Stall_ID"],
			"stallname" => $info["stallname"],
			"location"  => $info["location"],
		] : null,
	];
	$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>UB FoodStall - Vendor</title>
</head>
<body>
	<h1>UB FOOD STALL - VENDOR</h1>
	<p>Welcome <?= $h($vendor["first"] . " " . $vendor["last"]) ?> | <a href="logout.php">Logout</a></p>

	<nav>
		<a href="#/dashboard">Dashboard</a> |
		<a href="#/stall">My Stall</a> |
		<a href="#/products">Products</a> |
		<a href="#/reviews">Reviews</a>
	</nav>
	<hr>

	<p id="msg" role="status"></p>
	<div id="app"></div>

	<script>window.UBFS_VENDOR = <?= json_encode($vendor, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
	<script src="../assets/js/store.js"></script>
	<script src="../assets/js/vendor.js"></script>
</body>
</html>
