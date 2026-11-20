<?php
	require_once "../includes/helpers.php";
	require_once "../dbconn.php";
	require_once "../classes/stall.php";
	require_once "../classes/product.php";
	require_once "../classes/review.php";
	require_once "../classes/menuanalyzer.php";

	require_role("customer");

	$stall_id = (int) ($_GET['id'] ?? $_POST['Stall_ID'] ?? 0);
	$stall = (new Stall($pdo)) -> find($stall_id);

	if(!$stall){
		header("Location: index.php");
		exit;
	}

	$reviews = new Review($pdo);
	$customer_id = $_SESSION["Customer_ID"];
	$message = "";
	$type = "success";

	if(isset($_POST['save_review'])){
		$rating = (int) $_POST['rating'];
		$comment = trim($_POST['reviews']);

		if($rating < 1 || $rating > 5 || $comment === ""){
			$message = "Please choose a rating from 1 to 5 and write a short review.";
			$type = "danger";
		}
		else{
			$reviews -> save($stall_id, $stall['Vendor_ID'], $customer_id, $rating, mb_substr($comment, 0, 1000));
			header("Location: stall.php?id=" . $stall_id . "&saved=1");
			exit;
		}
	}
	elseif(isset($_POST['delete_review'])){
		$reviews -> delete_by_customer($stall_id, $customer_id);
		header("Location: stall.php?id=" . $stall_id . "&deleted=1");
		exit;
	}

	if(isset($_GET['saved'])){ $message = "Thanks! Your review was saved."; }
	if(isset($_GET['deleted'])){ $message = "Your review was deleted."; }

	// Products of this stall and the budget analysis.
	$products = (new Product($pdo)) -> fetch_by_stall($stall_id);
	$available = array_values(array_filter($products, fn($p) => $p['status'] === "Available"));

	$budget = isset($_GET["budget"]) ? max(0, (float) $_GET["budget"]) : 50.00;
	$analyzer = new MenuAnalyzer($available, $budget);
	$cheapest = $analyzer -> getCheapestItem();
	$most_expensive = $analyzer -> getMostExpensiveItem();
	$affordable = $analyzer -> getAffordableItems();

	$all_reviews = $reviews -> fetch_by_stall($stall_id);
	$mine = $reviews -> find_by_customer($stall_id, $customer_id);
	$open = $stall['status'] === "Open";

	$base = "../";
	$page_title = $stall['stallname'];
	$nav = ["Food Stalls" => "index.php"];
	$show_logout = true;
	require "../includes/header.php";
?>
<a href="index.php" class="text-decoration-none">&larr; Back to all stalls</a>

<div class="stall-cover large <?php echo cover_class($stall['Stall_ID']); ?> mt-2 mb-3"><?php echo e($stall['stallname']); ?></div>
<div class="d-flex flex-wrap gap-3 align-items-center mb-4">
	<span class="badge fs-6 text-bg-<?php echo $open ? 'success' : 'danger'; ?>"><?php echo $open ? "Open" : "Closed"; ?></span>
	<span class="text-muted">📍 <?php echo e($stall['location']); ?></span>
	<span>
		<?php if($stall['review_count'] > 0): ?>
			<span class="star"><?php echo stars($stall['avg_rating']); ?></span>
			<?php echo number_format($stall['avg_rating'], 1); ?> from <?php echo e($stall['review_count']); ?> review(s)
		<?php else: ?>
			<span class="text-muted">No reviews yet</span>
		<?php endif; ?>
	</span>
</div>

<?php alert($message, $type); ?>
<?php if(!$open): ?>
	<div class="alert alert-warning">This stall is currently closed. The list below is what it usually sells.</div>
<?php endif; ?>

<h2 class="h5">Product availability</h2>
<div class="table-responsive mb-4">
	<table class="table table-striped bg-white align-middle">
		<thead class="table-light"><tr><th>Product</th><th class="text-end">Price</th><th>Availability</th></tr></thead>
		<tbody>
			<?php foreach($products as $p): ?>
				<tr>
					<td><?php echo e($p['productname']); ?></td>
					<td class="text-end">₱<?php echo number_format($p['price'], 2); ?></td>
					<td>
						<?php if($p['status'] === "Available"): ?>
							<span class="badge text-bg-success">Available</span>
							<?php if($p['stock'] < 10): ?><span class="small text-warning-emphasis">Only <?php echo e($p['stock']); ?> left</span><?php endif; ?>
						<?php else: ?>
							<span class="badge text-bg-secondary">Not available</span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if(count($products) == 0): ?>
				<tr><td colspan="3" class="text-center text-muted">This stall has not added any products yet.</td></tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<div class="card shadow-sm mb-4">
	<div class="card-body">
		<h2 class="h5">What can I afford?</h2>
		<form method="get" class="row g-2 align-items-end mb-3">
			<input type="hidden" name="id" value="<?php echo e($stall_id); ?>">
			<div class="col-8 col-md-4">
				<label class="form-label">My budget (₱)</label>
				<input class="form-control" type="number" step="0.01" min="0" name="budget" value="<?php echo e($budget); ?>" required>
			</div>
			<div class="col-4 col-md-2"><button class="btn btn-ub w-100">Check</button></div>
		</form>
		<?php if($cheapest): ?>
			<p class="mb-2 small text-muted">
				Average price ₱<?php echo number_format($analyzer -> getAveragePrice(), 2); ?> ·
				Cheapest: <?php echo e($cheapest['productname']); ?> (₱<?php echo number_format($cheapest['price'], 2); ?>) ·
				Most expensive: <?php echo e($most_expensive['productname']); ?> (₱<?php echo number_format($most_expensive['price'], 2); ?>)
			</p>
			<?php if(count($affordable) > 0): ?>
				<ul class="mb-0">
					<?php foreach($affordable as $item): ?>
						<li><?php echo e($item['productname']); ?> - ₱<?php echo number_format($item['price'], 2); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p class="mb-0">No available item fits your budget.</p>
			<?php endif; ?>
		<?php else: ?>
			<p class="mb-0 text-muted">No available products to analyze.</p>
		<?php endif; ?>
	</div>
</div>

<h2 class="h5">Reviews</h2>
<div class="row g-4">
	<div class="col-md-5">
		<div class="card shadow-sm">
			<div class="card-body">
				<h3 class="h6"><?php echo $mine ? "Edit your review" : "Write a review"; ?></h3>
				<form method="post">
					<input type="hidden" name="Stall_ID" value="<?php echo e($stall_id); ?>">
					<div class="mb-3">
						<label class="form-label">Rating</label>
						<select class="form-select" name="rating" required>
							<?php for($i = 5; $i >= 1; $i--): ?>
								<option value="<?php echo $i; ?>" <?php echo (int) ($_POST['rating'] ?? $mine['rating'] ?? 5) === $i ? "selected" : ""; ?>><?php echo stars($i) . " (" . $i . ")"; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label">Your review</label>
						<textarea class="form-control" name="reviews" rows="4" maxlength="1000" required><?php echo e($_POST['reviews'] ?? $mine['reviews'] ?? ''); ?></textarea>
					</div>
					<div class="d-flex gap-2">
						<button class="btn btn-ub" name="save_review"><?php echo $mine ? "Update review" : "Post review"; ?></button>
						<?php if($mine): ?><button class="btn btn-outline-danger" name="delete_review" formnovalidate>Delete</button><?php endif; ?>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-7">
		<?php foreach($all_reviews as $r): ?>
			<div class="card shadow-sm mb-3">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<strong><?php echo e(trim(($r['firstname'] ?? '') . " " . ($r['lastname'] ?? '')) ?: "Former customer"); ?><?php echo $r['Customer_ID'] == $customer_id ? ' <span class="badge text-bg-light">You</span>' : ''; ?></strong>
						<span class="small text-muted"><?php echo e($r['reviewDate']); ?></span>
					</div>
					<div class="star mb-1"><?php echo stars($r['rating']); ?></div>
					<p class="mb-0"><?php echo nl2br(e($r['reviews'])); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
		<?php if(count($all_reviews) == 0): ?>
			<div class="text-muted">Be the first to review this stall.</div>
		<?php endif; ?>
	</div>
</div>
<?php require "../includes/footer.php"; ?>
