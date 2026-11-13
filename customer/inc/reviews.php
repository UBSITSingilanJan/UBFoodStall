<?php
	// Review block shared by stall.php and product.php.
	// Set these before including:
	//   $rvNoun     text, what is being reviewed (stall or product name)
	//   $rvSummary  from Catalog::getStallSummary / getProductSummary
	//   $rvReviews  from Catalog::getStallReviews / getProductReviews
	//   $rvMine     the signed-in customer's own review, or false
	$rvMyRating = $rvMine ? (int) $rvMine["rating"] : 0;
	$rvMyComment = $rvMine ? $rvMine["comment"] : "";
?>
<section id="reviews">
	<p>Average: <?= e(rating_text($rvSummary["avg"], $rvSummary["count"])) ?></p>
	<ul>
		<?php for($star = 5; $star >= 1; $star--): ?>
			<li><?= $star ?> star: <?= (int) $rvSummary["dist"][$star] ?></li>
		<?php endfor; ?>
	</ul>

	<form method="post">
		<h3><?= $rvMine ? "Edit your review" : "Write a review" ?></h3>
		<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
		<fieldset>
			<legend>Your rating for <?= e($rvNoun) ?></legend>
			<?php for($v = 1; $v <= 5; $v++): ?>
				<label><input type="radio" name="rating" value="<?= $v ?>" <?= $rvMyRating === $v ? "checked" : "" ?> required> <?= $v ?> star<?= $v > 1 ? "s" : "" ?></label>
			<?php endfor; ?>
		</fieldset>
		<label>Comment (optional)<br>
			<textarea name="comment" rows="4" maxlength="<?= Catalog::COMMENT_MAX ?>"><?= e($rvMyComment) ?></textarea>
		</label>
		<button type="submit"><?= $rvMine ? "Update review" : "Post review" ?></button>
	</form>

	<?php if(count($rvReviews) === 0): ?>
		<p>No reviews yet. Be the first to share how it was.</p>
	<?php else: ?>
		<ol>
			<?php foreach($rvReviews as $r): ?>
				<li>
					<strong><?= e($r["author"]) ?></strong>
					<?php if((int) $r["Customer_ID"] === $customerId): ?>(you)<?php endif; ?>
					<br>Rating: <?= (int) $r["rating"] ?> out of 5
					<br>Date: <?= e(date("M j, Y", strtotime($r["created"]))) ?>
					<?php if($r["comment"] !== ""): ?><p><?= nl2br(e($r["comment"])) ?></p><?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
</section>
