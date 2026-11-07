<?php
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";

	$conn = new Vendor($pdo);

	$id = $_GET['id'];

	$conn -> delete_data($id);

	header("Location: index.php");
	exit();
?>