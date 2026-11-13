<?php
	require_once "../includes/helpers.php";
	require_once "../dbconn.php";
	require_once "../classes/product.php";
	require_once "../classes/menuanalyzer.php";

	require_role("customer");

	$products = new Product($pdo);

	// Filters (from the form)
	$budget = isset($_GET["budget"]) ? max(0, (float) $_GET["budget"]) : 50.00;
	$stall_id = isset($_GET["stall"]) ? (int) $_GET["stall"] : 0;

	$stalls = $products -> fetch_stalls();
	$menu = $products -> fetch_available($stall_id);

	$analyzer = new MenuAnalyzer($menu, $budget);
	$cheapest = $analyzer -> getCheapestItem();
	$most_expensive = $analyzer -> getMostExpensiveItem();
	$affordable = $analyzer -> getAffordableItems();

	$base = "../";
	$page_title = "Customer";
	$show_logout = true;
	require "../includes/header.php";
?>
<h1 class="h3 mb-1">Welcome, <?php echo e($_SESSION["FNAME"]); ?>!</h1>
<p class="text-muted">Find food that fits your budget.</p>

<div class="card shadow-sm mb-4">
	<div class="card-body">
		<form method="get" class="row g-3 align-items-end">
			<div class="col-md-4">
				<label class="form-label">My budget (₱)</label>
				<input class="form-control" type="number" step="0.01" min="0" name="budget" value="<?php echo e($budget); ?>" required>
			</div>
			<div class="col-md-5">
				<label class="form-label">Food stall</label>
				<select class="form-select" name="stall">
					<option value="0">All stalls</option>
					<?php foreach($stalls as $s): ?>
						<option value="<?php echo e($s['Stall_ID']); ?>" <?php echo $stall_id == $s['Stall_ID'] ? "selected" : ""; ?>><?php echo e($s['stallname']); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-3">
				<button class="btn btn-ub w-100">Analyze</button>
			</div>
		</form>
	</div>
</div>

<div class="row g-3 mb-4">
	<div class="col-md-4">
		<div class="card h-100 shadow-sm"><div class="card-body">
			<div class="text-muted small">Average price</div>
			<div class="fs-4 fw-bold">₱<?php echo number_format($analyzer -> getAveragePrice(), 2); ?></div>
		</div></div>
	</div>
	<div class="col-md-4">
		<div class="card h-100 shadow-sm"><div class="card-body">
			<div class="text-muted small">Cheapest item</div>
			<?php if($cheapest): ?>
				<div class="fs-5 fw-bold"><?php echo e($cheapest['productname']); ?></div>
				<div>₱<?php echo number_format($cheapest['price'], 2); ?> · <?php echo e($cheapest['stallname']); ?></div>
			<?php else: ?>
				<div class="fs-5">None</div>
			<?php endif; ?>
		</div></div>
	</div>
	<div class="col-md-4">
		<div class="card h-100 shadow-sm"><div class="card-body">
			<div class="text-muted small">Most expensive item</div>
			<?php if($most_expensive): ?>
				<div class="fs-5 fw-bold"><?php echo e($most_expensive['productname']); ?></div>
				<div>₱<?php echo number_format($most_expensive['price'], 2); ?> · <?php echo e($most_expensive['stallname']); ?></div>
			<?php else: ?>
				<div class="fs-5">None</div>
			<?php endif; ?>
		</div></div>
	</div>
</div>

<h2 class="h5">Items within your ₱<?php echo number_format($budget, 2); ?> budget</h2>
<?php if(count($affordable) > 0): ?>
	<div class="table-responsive">
		<table class="table table-striped table-hover bg-white align-middle">
			<thead class="table-light">
				<tr><th>Food</th><th>Stall</th><th>Location</th><th class="text-end">Price</th></tr>
			</thead>
			<tbody>
				<?php foreach($affordable as $item): ?>
					<tr>
						<td><?php echo e($item['productname']); ?></td>
						<td><?php echo e($item['stallname']); ?></td>
						<td><?php echo e($item['location']); ?></td>
						<td class="text-end">₱<?php echo number_format($item['price'], 2); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else: ?>
	<div class="alert alert-warning">No items fit your budget.</div>
<?php endif; ?>
<?php require "../includes/footer.php"; ?>
