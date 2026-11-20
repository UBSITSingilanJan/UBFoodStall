<?php
	require_once "../includes/helpers.php";
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";

	require_role("admin");

	$conn = new Vendor($pdo);

	// Step 2: the admin confirmed (POST), so delete.
	if(isset($_POST['confirm_delete'])){
		$conn -> delete_data((int) $_POST['Vendor_ID']);

		header("Location: index.php?deleted=1");
		exit;
	}

	// Step 1: show a confirmation page.
	$id = (int) ($_GET['id'] ?? 0);
	$row = $conn -> show_update_data($id);

	if(!$row){
		header("Location: index.php");
		exit;
	}

	$base = "../";
	$page_title = "Delete Vendor";
	$nav = ["Vendors" => "index.php"];
	$show_logout = true;
	require "../includes/header.php";
?>
<div class="row justify-content-center">
	<div class="col-md-6">
		<div class="card shadow-sm border-danger"><div class="card-body p-4">
			<h1 class="h4 text-danger">Delete this vendor?</h1>
			<p>
				<strong><?php echo e($row['firstname'] . " " . $row['lastname']); ?></strong>
				and the stall <strong><?php echo e($row['stallname']); ?></strong>,
				including all of its products, will be permanently deleted.
			</p>
			<form method="post" class="d-flex justify-content-between">
				<input type="hidden" name="Vendor_ID" value="<?php echo e($row['Vendor_ID']); ?>">
				<a class="btn btn-outline-secondary" href="index.php">Cancel</a>
				<button class="btn btn-danger" name="confirm_delete">Yes, delete</button>
			</form>
		</div></div>
	</div>
</div>
<?php require "../includes/footer.php"; ?>
