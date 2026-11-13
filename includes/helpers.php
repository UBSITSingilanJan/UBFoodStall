<?php
	// Shared helper functions used by every page.

	if(session_status() === PHP_SESSION_NONE){
		session_start();
	}

	// Escape text before printing it in HTML (prevents XSS).
	function e($value){
		return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
	}

	// Where each role lands after login. $base is "" from the root folder or "../" from a subfolder.
	function home_for_role($role, $base = ""){
		if($role === "admin"){
			return $base . "admin/index.php";
		}
		if($role === "vendor"){
			return $base . "vendor/index.php";
		}
		return $base . "customer/index.php";
	}

	// Only let the given role into the page, otherwise send them back to the login page.
	function require_role($role, $base = "../"){
		if(($_SESSION["ROLE"] ?? "") !== $role){
			header("Location: " . $base . "index.php");
			exit;
		}
	}

	// Show the "UB Food Stall" bootstrap alert if $message is not empty.
	function alert($message, $type = "danger"){
		if($message !== ""){
			echo '<div class="alert alert-' . e($type) . '" role="alert">' . e($message) . '</div>';
		}
	}
?>
