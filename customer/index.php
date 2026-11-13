<?php
	require_once __DIR__ . "/inc/bootstrap.php";

	/* Search, filter and sort come from the query string: index.php?q=siomai&status=open&sort=rating */
	$q = trim(get_str("q"));
	$status = get_choice("status", ["all", "open", "closed"], "all");
	$sort = get_choice("sort", ["number", "rating", "price", "name"], "number");

	$allStalls = $catalog -> getStalls();
	$stalls = Catalog::filterStalls($allStalls, $q, $status, $sort);

	$pageTitle = "Stalls";
	require __DIR__ . "/inc/header.php";
?>

<h1>Stalls</h1>
<p>Pick a stall to see its menu and what is in stock.</p>

<form method="get" action="index.php">
	<label>Search <input type="search" name="q" value="<?= e($q) ?>" placeholder="Name, number, or location"></label>
	<label>Status
		<select name="status">
			<option value="all"<?= sel($status, "all") ?>>All</option>
			<option value="open"<?= sel($status, "open") ?>>Open</option>
			<option value="closed"<?= sel($status, "closed") ?>>Closed</option>
		</select>
	</label>
	<label>Sort
		<select name="sort">
			<option value="number"<?= sel($sort, "number") ?>>Stall number</option>
			<option value="rating"<?= sel($sort, "rating") ?>>Top rated</option>
			<option value="price"<?= sel($sort, "price") ?>>Lowest price</option>
			<option value="name"<?= sel($sort, "name") ?>>Name</option>
		</select>
	</label>
	<button type="submit">Apply</button>
	<a href="index.php">Reset</a>
</form>

<p>Showing <?= count($stalls) ?> of <?= count($allStalls) ?> stalls</p>

<?php if(count($allStalls) === 0): ?>
	<p>No stalls yet. Stalls show up here once a vendor is added.</p>
<?php elseif(count($stalls) === 0): ?>
	<p>No stalls match. Try a different word or reset the filter.</p>
<?php else: ?>
	<?php foreach($stalls as $s):
		$unavailable = (int) $s["product_count"] - (int) $s["available_count"] - (int) $s["low_count"]; ?>
		<article>
			<h2><a href="stall.php?id=<?= (int) $s["Stall_ID"] ?>">Stall <?= (int) $s["Stall_ID"] ?>: <?= e($s["stallname"]) ?></a></h2>
			<p>Location: <?= e($s["location"]) ?></p>
			<p>Status: <?= e(stall_status_text($s["status"])) ?></p>
			<p>Rating: <?= e(rating_text($s["avg_rating"], $s["review_count"])) ?></p>
			<p>Lowest price: <?= $s["min_price"] !== null ? e(peso($s["min_price"])) : "Nothing in stock" ?></p>
			<p>Products: <?= (int) $s["available_count"] ?> available, <?= (int) $s["low_count"] ?> low stock, <?= $unavailable ?> unavailable</p>
		</article>
	<?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . "/inc/footer.php"; ?>
