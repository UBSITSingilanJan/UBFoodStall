<?php
	session_start();

	require_once __DIR__ . "/../../dbconn.php";
	require_once __DIR__ . "/../../classes/catalog.php";

	// Customers only.
	if(!isset($_SESSION["Customer_ID"])){
		header("Location: ../index.php");
		exit;
	}

	$catalog = new Catalog($pdo);
	$customerId = (int) $_SESSION["Customer_ID"];
	$customerName = isset($_SESSION["FNAME"]) ? $_SESSION["FNAME"] : "Customer";

	/* ---------- Helpers ---------- */

	// Escape text before printing it in HTML.
	function e($value): string{
		return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
	}

	function peso($amount): string{
		return "₱" . number_format((float) $amount, 2);
	}

	function plural(int $n, string $word): string{
		return $n . " " . $word . ($n === 1 ? "" : "s");
	}

	// Read a text value from the query string. Arrays and missing keys give the default.
	function get_str(string $key, string $default = ""): string{
		return (isset($_GET[$key]) && is_string($_GET[$key])) ? $_GET[$key] : $default;
	}

	// Read a text value from the query string, but only if it is on the allowed list.
	function get_choice(string $key, array $allowed, string $default): string{
		$value = get_str($key, $default);
		return in_array($value, $allowed, true) ? $value : $default;
	}

	// Prints " selected" when two values match (for <option>).
	function sel($current, $value): string{
		return (string) $current === (string) $value ? " selected" : "";
	}

	function csrf_token(): string{
		if(empty($_SESSION["csrf"])){
			$_SESSION["csrf"] = bin2hex(random_bytes(32));
		}
		return $_SESSION["csrf"];
	}

	function csrf_ok(): bool{
		return isset($_POST["csrf"], $_SESSION["csrf"]) && hash_equals($_SESSION["csrf"], (string) $_POST["csrf"]);
	}

	// One-time message shown after a redirect.
	function flash_set(string $message): void{
		$_SESSION["flash"] = $message;
	}

	function flash_get(): ?string{
		$message = isset($_SESSION["flash"]) ? $_SESSION["flash"] : null;
		unset($_SESSION["flash"]);
		return $message;
	}

	// available | low | unavailable  ->  text
	function state_label(string $state): string{
		$labels = ["available" => "Available", "low" => "Low stock", "unavailable" => "Unavailable"];
		return isset($labels[$state]) ? $labels[$state] : "";
	}

	function stock_text(array $product): string{
		$stock = (int) $product["stock"];
		if($product["state"] === "unavailable"){
			return "Out of stock";
		}
		if($product["state"] === "low"){
			return "Only " . $stock . " left";
		}
		return $stock . " in stock";
	}

	// "4.5 out of 5 (3 reviews)" or "No reviews yet"
	function rating_text($avg, $count): string{
		if((int) $count < 1){
			return "No reviews yet";
		}
		return number_format((float) $avg, 1) . " out of 5 (" . plural((int) $count, "review") . ")";
	}

	function stall_status_text(string $status): string{
		return $status === "Open" ? "Open" : "Closed";
	}
?>
