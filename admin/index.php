<?php
	require_once "../includes/helpers.php";
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";

	require_role("admin");

	$conn = new Vendor($pdo);
	$rows = $conn -> fetch_data();

	$base = "../";
	$page_title = "Admin";
	$nav = ["Add Vendor" => "add_vendor.php"];
	$show_logout = true;
	require "../includes/header.php";

	$notice = isset($_GET["deleted"]) ? "Vendor deleted." : (isset($_GET["saved"]) ? "Vendor saved." : "");
?>
<h1 class="h3 mb-3">Vendors</h1>
<?php alert($notice, "success"); ?>
<div class="table-responsive">
	<table class="table table-striped table-hover bg-white align-middle">
		<thead class="table-light">
			<tr>
				<th>ID</th><th>First name</th><th>Last name</th><th>Contact</th><th>Email</th><th>Stall</th><th>Location</th><th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rows as $row): ?>
				<tr>
					<td><?php echo e($row['Vendor_ID']); ?></td>
					<td><?php echo e($row['firstname']); ?></td>
					<td><?php echo e($row['lastname']); ?></td>
					<td><?php echo e($row['contact']); ?></td>
					<td><?php echo e($row['email']); ?></td>
					<td><?php echo e($row['stallname']); ?></td>
					<td><?php echo e($row['location']); ?></td>
					<td class="text-nowrap">
						<a class="btn btn-sm btn-outline-secondary" href="update.php?id=<?php echo e($row['Vendor_ID']); ?>">Update</a>
						<a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?php echo e($row['Vendor_ID']); ?>">Delete</a>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if(count($rows) == 0): ?>
				<tr><td colspan="8" class="text-center text-muted">No vendors yet.</td></tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php require "../includes/footer.php"; ?>
