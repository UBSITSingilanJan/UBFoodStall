<?php
	require_once "../includes/helpers.php";
	require_once "../dbconn.php";
	require_once "../classes/stall.php";

	require_role("customer");

	$stalls = (new Stall($pdo)) -> fetch_all();

	$base = "../";
	$page_title = "Food Stalls";
	$nav = ["Food Stalls" => "index.php"];
	$show_logout = true;
	require "../includes/header.php";
?>
<h1 class="h3 mb-1">Welcome, <?php echo e($_SESSION["FNAME"]); ?>!</h1>
<p class="text-muted">Pick a food stall to see what is available and what others say about it.</p>

<?php if(count($stalls) == 0): ?>
	<div class="alert alert-info">There are no food stalls yet.</div>
<?php endif; ?>

<div class="row g-4">
	<?php foreach($stalls as $s): ?>
		<?php $open = $s['status'] === "Open"; ?>
		<div class="col-sm-6 col-lg-4">
			<div class="card h-100 shadow-sm overflow-hidden <?php echo $open ? '' : 'opacity-75'; ?>">
				<div class="stall-cover <?php echo cover_class($s['Stall_ID']); ?>"><?php echo e($s['stallname']); ?></div>
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-2">
						<span class="badge text-bg-<?php echo $open ? 'success' : 'danger'; ?>"><?php echo $open ? "Open" : "Closed"; ?></span>
						<span class="small">
							<?php if($s['review_count'] > 0): ?>
								<span class="star"><?php echo stars($s['avg_rating']); ?></span>
								<?php echo number_format($s['avg_rating'], 1); ?> (<?php echo e($s['review_count']); ?>)
							<?php else: ?>
								<span class="text-muted">No reviews yet</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="text-muted small">📍 <?php echo e($s['location']); ?></div>
				</div>
				<div class="card-footer bg-white border-0 pb-3">
					<a class="btn btn-ub w-100" href="stall.php?id=<?php echo e($s['Stall_ID']); ?>">View products &amp; reviews</a>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<?php require "../includes/footer.php"; ?>
