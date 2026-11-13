<?php
	require_once __DIR__ . "/inc/bootstrap.php";

	$stallId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
	$stall = $catalog -> getStall($stallId);

	if(!$stall){
		http_response_code(404);
		$pageTitle = "Stall not found";
		$backUrl = "index.php";
		$backLabel = "all stalls";
		require __DIR__ . "/inc/header.php";
		?>
		<h1>We could not find that stall</h1>
		<p>It may have been removed. Go back and pick another one.</p>
		<?php
		require __DIR__ . "/inc/footer.php";
		exit;
	}

	/* Post a stall review, then redirect so a refresh does not post it twice. */
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
			$catalog -> saveStallReview($customerId, $stallId, $rating, $comment);
			flash_set("Thanks. Your review is saved.");
		}

		header("Location: stall.php?id=" . $stallId . "#reviews");
		exit;
	}

	/* Filter and sort come from the query string: stall.php?id=3&q=rice&state=low&sort=price-asc */
	$q = trim(get_str("q"));
	$state = get_choice("state", ["all", "available", "low", "unavailable"], "all");
	$sort = get_choice("sort", ["name", "rating", "price-asc", "price-desc"], "name");

	$allProducts = $catalog -> getProducts($stallId);
	$products = Catalog::filterProducts($allProducts, $q, $state, $sort);

	$counts = ["available" => 0, "low" => 0, "unavailable" => 0];
	foreach($allProducts as $p){
		$counts[$p["state"]]++;
	}

	$summary = $catalog -> getStallSummary($stallId);
	$vendorName = trim($stall["firstname"] . " " . $stall["lastname"]);

	$pageTitle = $stall["stallname"];
	$backUrl = "index.php";
	$backLabel = "all stalls";
	require __DIR__ . "/inc/header.php";
?>

<h1>Stall <?= (int) $stall["Stall_ID"] ?>: <?= e($stall["stallname"]) ?></h1>
<p>Location: <?= e($stall["location"]) ?></p>
<?php if($vendorName !== ""): ?><p>Run by <?= e($vendorName) ?></p><?php endif; ?>
<p>Status: <?= e(stall_status_text($stall["status"])) ?></p>
<p>Rating: <?= e(rating_text($summary["avg"], $summary["count"])) ?> <a href="#reviews">See reviews</a></p>
<p>Products: <?= $counts["available"] ?> available, <?= $counts["low"] ?> low stock, <?= $counts["unavailable"] ?> unavailable</p>

<?php if($stall["status"] !== "Open"): ?>
	<p>This stall is closed right now. Stock shown is the last update from the vendor.</p>
<?php endif; ?>

<h2>Products</h2>

<form method="get" action="stall.php">
	<input type="hidden" name="id" value="<?= (int) $stallId ?>">
	<label>Search <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search in this stall"></label>
	<label>Availability
		<select name="state">
			<option value="all"<?= sel($state, "all") ?>>All (<?= count($allProducts) ?>)</option>
			<option value="available"<?= sel($state, "available") ?>>Available (<?= $counts["available"] ?>)</option>
			<option value="low"<?= sel($state, "low") ?>>Low stock (<?= $counts["low"] ?>)</option>
			<option value="unavailable"<?= sel($state, "unavailable") ?>>Unavailable (<?= $counts["unavailable"] ?>)</option>
		</select>
	</label>
	<label>Sort
		<select name="sort">
			<option value="name"<?= sel($sort, "name") ?>>Name</option>
			<option value="rating"<?= sel($sort, "rating") ?>>Top rated</option>
			<option value="price-asc"<?= sel($sort, "price-asc") ?>>Price: low to high</option>
			<option value="price-desc"<?= sel($sort, "price-desc") ?>>Price: high to low</option>
		</select>
	</label>
	<button type="submit">Apply</button>
	<a href="stall.php?id=<?= (int) $stallId ?>">Reset</a>
</form>

<?php if(count($allProducts) === 0): ?>
	<p>No products yet. This stall has not listed anything.</p>
<?php elseif(count($products) === 0): ?>
	<p>No products match. Try a different word or availability.</p>
<?php else: ?>
	<?php foreach($products as $p): ?>
		<article>
			<h3><a href="product.php?id=<?= (int) $p["Product_ID"] ?>"><?= e($p["productname"]) ?></a></h3>
			<p>Status: <?= e(state_label($p["state"])) ?></p>
			<p>Price: <?= e(peso($p["price"])) ?></p>
			<p>Stock: <?= e(stock_text($p)) ?></p>
			<p>Rating: <?= e(rating_text($p["avg_rating"], $p["review_count"])) ?></p>
		</article>
	<?php endforeach; ?>
<?php endif; ?>

<h2>Stall reviews</h2>
<?php
	$rvNoun = $stall["stallname"];
	$rvSummary = $summary;
	$rvReviews = $catalog -> getStallReviews($stallId);
	$rvMine = $catalog -> getMyStallReview($customerId, $stallId);
	require __DIR__ . "/inc/reviews.php";
?>

<?php require __DIR__ . "/inc/footer.php"; ?>
