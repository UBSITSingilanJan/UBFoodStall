<?php
	require_once "../includes/helpers.php";
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";
	require_once "../classes/product.php";
	require_once "../classes/inventoryanalyzer.php";

	require_role("vendor");

	$vendor = new Vendor($pdo);
	$products = new Product($pdo);

	$stall = $vendor -> find_stall($_SESSION["Vendor_ID"]);
	if(!$stall){
		session_destroy();
		header("Location: ../index.php");
		exit;
	}
	$stall_id = $stall['Stall_ID'];
	$message = "";
	$type = "success";

	if($_SERVER["REQUEST_METHOD"] === "POST"){
		if(isset($_POST['add_product'])){
			$name = trim($_POST['item_name']);
			$price = (float) $_POST['item_price'];
			$stock = (int) $_POST['item_stock'];

			if($name === "" || $price <= 0 || $stock < 0){
				$message = "Please enter a name, a price above 0 and a stock of 0 or more.";
				$type = "danger";
			}
			else{
				$products -> insert_data($stall_id, $name, $price, $stock);
				$message = "Product added.";
			}
		}
		elseif(isset($_POST['update_stock'])){
			$products -> update_stock((int) $_POST['Product_ID'], $stall_id, max(0, (int) $_POST['stock']));
			$message = "Stock updated.";
		}
		elseif(isset($_POST['delete_product'])){
			$products -> delete_data((int) $_POST['Product_ID'], $stall_id);
			$message = "Product deleted.";
		}
	}

	$inventory = $products -> fetch_by_stall($stall_id);
	$analyzer = new InventoryAnalyzer($inventory);
	$alerts = $analyzer -> getLowStockAlerts();

	$base = "../";
	$page_title = "Vendor";
	$show_logout = true;
	require "../includes/header.php";
?>
<h1 class="h3 mb-1">Welcome, <?php echo e($_SESSION["FNAME"]); ?>!</h1>
<p class="text-muted"><?php echo e($stall['stallname']); ?> · <?php echo e($stall['location']); ?> · <span class="badge text-bg-secondary"><?php echo e($stall['status']); ?></span></p>

<?php alert($message, $type); ?>

<div class="row g-3 mb-4">
	<div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
		<div class="text-muted small">Average item price</div>
		<div class="fs-4 fw-bold">₱<?php echo number_format($analyzer -> getAveragePrice(), 2); ?></div>
	</div></div></div>
	<div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
		<div class="text-muted small">Lowest stock</div>
		<div class="fs-4 fw-bold"><?php echo $analyzer -> getMinStock(); ?></div>
	</div></div></div>
	<div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
		<div class="text-muted small">Highest stock</div>
		<div class="fs-4 fw-bold"><?php echo $analyzer -> getMaxStock(); ?></div>
	</div></div></div>
	<div class="col-md-3"><div class="card shadow-sm h-100"><div class="card-body">
		<div class="text-muted small">Inventory value</div>
		<div class="fs-4 fw-bold">₱<?php echo number_format($analyzer -> getInventoryValue(), 2); ?></div>
	</div></div></div>
</div>

<h2 class="h5">Alerts</h2>
<?php if(count($alerts) > 0): ?>
	<?php foreach($alerts as $a): ?>
		<div class="alert alert-warning py-2"><?php echo e($a); ?></div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="alert alert-success py-2">All items are sufficiently stocked.</div>
<?php endif; ?>

<div class="card shadow-sm my-4">
	<div class="card-body">
		<h2 class="h5">Add a product</h2>
		<form method="post" class="row g-3 align-items-end">
			<div class="col-md-5">
				<label class="form-label">Item name</label>
				<input class="form-control" type="text" name="item_name" required>
			</div>
			<div class="col-md-3">
				<label class="form-label">Price (₱)</label>
				<input class="form-control" type="number" step="0.01" min="0.01" name="item_price" required>
			</div>
			<div class="col-md-2">
				<label class="form-label">Stock</label>
				<input class="form-control" type="number" min="0" name="item_stock" required>
			</div>
			<div class="col-md-2">
				<button class="btn btn-ub w-100" name="add_product">Add</button>
			</div>
		</form>
	</div>
</div>

<h2 class="h5">Current inventory</h2>
<div class="table-responsive">
	<table class="table table-striped bg-white align-middle">
		<thead class="table-light">
			<tr><th>Item</th><th class="text-end">Price</th><th>Status</th><th style="width:260px">Stock</th><th></th></tr>
		</thead>
		<tbody>
			<?php foreach($inventory as $item): ?>
				<tr>
					<td><?php echo e($item['productname']); ?></td>
					<td class="text-end">₱<?php echo number_format($item['price'], 2); ?></td>
					<td><span class="badge text-bg-<?php echo $item['status'] === 'Available' ? 'success' : 'secondary'; ?>"><?php echo e($item['status']); ?></span></td>
					<td>
						<form method="post" class="d-flex gap-2">
							<input type="hidden" name="Product_ID" value="<?php echo e($item['Product_ID']); ?>">
							<input class="form-control form-control-sm" type="number" min="0" name="stock" value="<?php echo e($item['stock']); ?>">
							<button class="btn btn-sm btn-outline-secondary" name="update_stock">Save</button>
						</form>
					</td>
					<td>
						<form method="post">
							<input type="hidden" name="Product_ID" value="<?php echo e($item['Product_ID']); ?>">
							<button class="btn btn-sm btn-outline-danger" name="delete_product">Delete</button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if(count($inventory) == 0): ?>
				<tr><td colspan="5" class="text-center text-muted">No products yet. Add your first one above.</td></tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php require "../includes/footer.php"; ?>
