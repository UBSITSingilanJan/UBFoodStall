<?php
	require_once "includes/helpers.php";
	require_once "dbconn.php";
	require_once "classes/customer.php";
	require_once "classes/vendor.php";

	// Built-in admin account (kept from the original project).
	const ADMIN_EMAIL = "admin@gmail.com";
	const ADMIN_PASSWORD = "admin123";

	if(!isset($_POST['login_btn'])){
		header("Location: index.php");
		exit;
	}

	$email = trim($_POST['email']);
	$password = $_POST['password'];

	if($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD){
		session_regenerate_id(true);
		$_SESSION["ROLE"] = "admin";
		$_SESSION["FNAME"] = "Admin";
		$_SESSION["LNAME"] = "Admin";

		header("Location: admin/index.php");
		exit;
	}

	$customer = new Customer($pdo);
	$row = $customer -> login($email, $password);

	if($row){
		session_regenerate_id(true);
		$_SESSION["ROLE"] = "customer";
		$_SESSION["USER_TYPE"] = $row['user_type'];
		$_SESSION["Customer_ID"] = $row['Customer_ID'];
		$_SESSION["FNAME"] = $row['firstname'];
		$_SESSION["LNAME"] = $row['lastname'];

		header("Location: customer/index.php");
		exit;
	}

	$vendor = new Vendor($pdo);
	$row = $vendor -> login($email, $password);

	if($row){
		session_regenerate_id(true);
		$_SESSION["ROLE"] = "vendor";
		$_SESSION["USER_TYPE"] = "Vendor";
		$_SESSION["Vendor_ID"] = $row['Vendor_ID'];
		$_SESSION["FNAME"] = $row['firstname'];
		$_SESSION["LNAME"] = $row['lastname'];

		header("Location: vendor/index.php");
		exit;
	}

	header("Location: index.php?error=1");
	exit;
?>
