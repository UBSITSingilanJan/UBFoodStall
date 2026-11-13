<?php
	// Variables a page can set before including this file:
	// $pageTitle, $backUrl (null on the first page), $backLabel
	$pageTitle = isset($pageTitle) ? $pageTitle : "Stalls";
	$backUrl = isset($backUrl) ? $backUrl : null;
	$backLabel = isset($backLabel) ? $backLabel : "previous page";
	$flashMessage = flash_get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= e($pageTitle) ?> | UB Food Stall</title>
</head>
<body>
<header>
	<a href="index.php">UB Food Stall</a>
	<?php if($backUrl): ?>
		<a href="<?= e($backUrl) ?>">Back to <?= e($backLabel) ?></a>
	<?php endif; ?>
	<span>Signed in as <?= e($customerName) ?></span>
	<a href="logout.php">Log out</a>
</header>
<?php if($flashMessage): ?>
	<p role="status"><?= e($flashMessage) ?></p>
<?php endif; ?>
<main>
