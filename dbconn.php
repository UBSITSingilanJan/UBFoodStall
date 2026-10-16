<?php
  $host = "localhost";
  $user = "root";
  $pass = "";
  $db = "ubfoodstall";
  $charset = "utf8mb4";

  $dsn = "mysql:host = $host; user = $user; password = $pass; dbname = $db; charset = $charset";

  $attribute = [
    PDO::ATTR_ERRMODE_EXCEPTION => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try{
  $pdo = new PDO($dsn, $user, $attribute, $pass);
  }catch(PDOException $e){
    die("DATABASE CONNECTION FAILED..." . $e->getMessage());
  }
?>
