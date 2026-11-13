<?php
	// Expects: $page_title (string), $base ("" or "../"), optional $nav (array of label => link)
	$page_title = $page_title ?? "UB Food Stall";
	$base = $base ?? "";
	$nav = $nav ?? [];
	$show_logout = $show_logout ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($page_title); ?> | UB Food Stall</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.navbar-ub { background-color: #7a1f2b; }
		.btn-ub { background-color: #7a1f2b; border-color: #7a1f2b; color: #fff; }
		.btn-ub:hover { background-color: #5e1721; border-color: #5e1721; color: #fff; }
		.text-ub { color: #7a1f2b; }
	</style>
</head>
<body class="bg-light">
	<nav class="navbar navbar-expand navbar-dark navbar-ub mb-4">
		<div class="container">
			<span class="navbar-brand fw-bold">UB Food Stall</span>
			<ul class="navbar-nav ms-auto">
				<?php foreach($nav as $label => $link): ?>
					<li class="nav-item"><a class="nav-link" href="<?php echo e($link); ?>"><?php echo e($label); ?></a></li>
				<?php endforeach; ?>
				<?php if($show_logout): ?>
					<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</nav>
	<div class="container pb-5">
