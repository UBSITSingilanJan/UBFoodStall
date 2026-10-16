<?php
	session_start();
	require_once "dbconn.php";        
	require_once "classes/Vendor.php"; 

	if(isset($_POST['login_btn'])){
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);

		if($username === "admin" && $password === "admin123"){
			$_SESSION["ROLE"]  = "admin";
			$_SESSION["FNAME"] = "Super";
			$_SESSION["LNAME"] = "Admin";
			header("location:admin/index.php");
			exit;
		}

		$vendorObj = new Vendor($pdo);
		$row = $vendorObj->login($username, $password);

		if($row){
			$_SESSION["ROLE"]      = "vendor";
			$_SESSION["Vendor_ID"] = $row['Vendor_ID'];
			$_SESSION["FNAME"]     = $row['First_Name'];
			$_SESSION["LNAME"]     = $row['Last_Name'];
			header("location:vendor/index.php");
			exit;
		}

		echo "<script>alert('Invalid credentials.'); window.location='index.php';</script>";
		exit;
	}
?>
