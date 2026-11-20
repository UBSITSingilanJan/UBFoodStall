<?php
	session_start();

	// Only logged-in customers can see this page.
	if (!isset($_SESSION["Customer_ID"])) {
		header("Location: ../index.php");
		exit;
	}

	require_once "../dbconn.php";

	// Read-only: load the logged-in customer's profile for the Account tab.
	$profile = [
		"firstname"  => $_SESSION["FNAME"] ?? "Customer",
		"lastname"   => $_SESSION["LNAME"] ?? "",
		"email"      => "",
		"department" => "",
		"year_level" => "",
		"user_type"  => $_SESSION["USER_TYPE"] ?? "Student",
	];
	try {
		$prep = $pdo->prepare("SELECT firstname, lastname, email, department, year_level, user_type FROM customer WHERE Customer_ID = :id");
		$prep->execute([":id" => $_SESSION["Customer_ID"]]);
		$row = $prep->fetch();
		if ($row) { $profile = array_merge($profile, $row); }
	} catch (PDOException $e) {
		// Keep the session values if the lookup fails.
	}

	$user = [
		"id"         => (int) $_SESSION["Customer_ID"],
		"first"      => $profile["firstname"],
		"last"       => $profile["lastname"],
		"short"      => trim($profile["firstname"] . " " . substr($profile["lastname"], 0, 1) . "."),
		"email"      => $profile["email"],
		"department" => $profile["department"],
		"year"       => $profile["year_level"],
		"type"       => $profile["user_type"],
	];
	$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>UB FoodStall</title>
</head>
<body>
	<h1>UB FOOD STALL</h1>
	<p>Welcome <?= $h($user["first"]) ?> | <a href="logout.php">Logout</a></p>

	<!-- Food stall navigator -->
	<nav>
		<a href="#/stalls">Stalls</a> |
		<a href="#/food">Food</a> |
		<a href="#/search">Search</a> |
		<a href="#/account">Account</a>
	</nav>
	<hr>

	<p id="msg" role="status"></p>
	<div id="app"></div>

	<script>window.UBFS_USER = <?= json_encode($user, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
	<script src="../assets/js/store.js"></script>
	<script src="../assets/js/customer.js"></script>
</body>
</html>
