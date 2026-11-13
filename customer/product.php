<?php
	require_once __DIR__ . "/inc/bootstrap.php";

	$productId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
	$product = $catalog -> getProduct($productId);

	if(!$product){
		http_response_code(404);
		$pageTitle = "Product not found";
		$backUrl = "index.php";
		$backLabel = "all stalls";
		require __DIR__ . "/inc/header.php";
		?>
		<h1>We could not find that product</h1>
		<p>It may have been removed. Go back and pick another one.</p>
		<?php
		require __DIR__ . "/inc/footer.php";
		exit;
	}

	/* Post a product review, then redirect so a refresh does not post it twice. */
	if($_SERVER["REQUEST_METHOD"] === "POST"){
		$rating = isset($_POST["rating"]) ? (int) $_POST["rating"] : 0;
		$comment = (isset($_POST["comment"]) && is_string($_POST["comment"])) ? $_POST["comment"] : "";

		if(!csrf_ok()){
			flash_set("Your session expired. Please try again.");
		}
		elseif($rating < 1 || $rating > 5){
			flash_set("Choose a star rating before you post.");
		}
		else{
			$catalog -> saveProductReview($customerId, $productId, $rating, $comment);
			flash_set("Thanks. Your review is saved.");
		}

		header("Location: product.php?id=" . $productId . "#reviews");
		exit;
	}

	$summary = $catalog -> getProductSummary($productId);
	$state = $product["state"];
	$stock = max(0, (int) $product["stock"]);
	$messages = [
		"available" => "In stock and ready at the stall.",
		"low" => "Only a few are left. Visit the stall soon.",
		"unavailable" => "Not available right now. Check back later.",
	];

	$pageTitle = $product["productname"];
	$backUrl = "stall.php?id=" . (int) $product["Stall_ID"];
	$backLabel = $product["stallname"];
	require __DIR__ . "/inc/header.php";
?>

<h1><?= e($product["productname"]) ?></h1>
<p>From <a href="<?= e($backUrl) ?>">Stall <?= (int) $product["Stall_ID"] ?>: <?= e($product["stallname"]) ?></a></p>
<p>Rating: <?= e(rating_text($summary["avg"], $summary["count"])) ?> <a href="#reviews">See reviews</a></p>
<p>Price: <?= e(peso($product["price"])) ?></p>
<p>Status: <?= e(state_label($state)) ?></p>
<p>Stock left: <?= $stock ?></p>
<p><?= e($messages[$state]) ?></p>

<?php if(trim($product["description"]) !== ""): ?>
	<h2>About this item</h2>
	<p><?= nl2br(e($product["description"])) ?></p>
<?php endif; ?>

<h2>Details</h2>
<dl>
	<dt>Stall</dt><dd>Stall <?= (int) $product["Stall_ID"] ?>: <?= e($product["stallname"]) ?></dd>
	<dt>Location</dt><dd><?= e($product["location"]) ?></dd>
	<dt>Stall hours</dt><dd><?= $product["stall_status"] === "Open" ? "Open now" : "Closed now" ?></dd>
</dl>

<h2>Product reviews</h2>
<?php
	$rvNoun = $product["productname"];
	$rvSummary = $summary;
	$rvReviews = $catalog -> getProductReviews($productId);
	$rvMine = $catalog -> getMyProductReview($customerId, $productId);
	require __DIR__ . "/inc/reviews.php";
?>

<?php require __DIR__ . "/inc/footer.php"; ?>
