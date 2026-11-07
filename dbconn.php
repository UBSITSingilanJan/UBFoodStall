<?php
	$host = "localhost:3308";
	$user = "root";
	$pass = "";
	$db = "ubfoodstall";
	$charset = "utf8mb4";

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$attributes = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];

	try{
		$pdo = new PDO($dsn, $user, $pass, $attributes);
	}
	catch(PDOException $e){
		die("Connection Failed" . $e -> getMessage());
	}
?>