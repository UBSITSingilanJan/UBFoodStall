<?php
  $host = "localhost";
  $user = "root";
  $pass = "";
  $db = "ubfoodstall";
  $charset = "utf8mb4";
  $port = 3307;

  $dsn = ("mysql:host=$host;dbname=$db;charset=$charset;port=$port");

  $attribute = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    $pdo = new PDO ($dsn, $user, $pass, $attribute);
  } catch (PDOException $e) {
    die("Database Connection Failed..." . $e->getMessage());
  }
?>
