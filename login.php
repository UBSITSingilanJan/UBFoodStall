<?php
	session_start();
	require_once "dbconn.php";
	require_once "classes/customer.php";
	require_once "classes/vendor.php";


	if (isset($_POST['login_btn'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];

		if($email === "admin@gmail.com" && $password === "admin123"){
			$_SESSION["ROLE"]  = "admin";
			$_SESSION["FNAME"] = "Admin";
			$_SESSION["LNAME"] = "Admin";

			header("Location: admin/index.php");
			exit();
		}

		$account = new Customer($pdo);
		$row = $account -> login($email, $password);

		if($row){
			$_SESSION["USER_TYPE"]      = "Student";
			$_SESSION["Customer_ID"] = $row['Customer_ID'];
			$_SESSION["FNAME"]     = $row['firstname'];
			$_SESSION["LNAME"]     = $row['lastname'];

			header("Location: customer/index.php");
			exit;
		}

		$account = new Vendor($pdo);
		$row = $account -> login($email, $password);

		if($row){
			$_SESSION["USER_TYPE"]      = "Vendor";
			$_SESSION["Vendor_ID"] = $row['Vendor_ID'];
			$_SESSION["FNAME"]     = $row['firstname'];
			$_SESSION["LNAME"]     = $row['lastname'];

			header("Location: vendor/index.php");
			exit;
		}

		echo "<script>alert('Invalid credentials.'); window.location='index.php';</script>";
		exit;
	}
?>