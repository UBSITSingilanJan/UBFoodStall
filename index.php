<?php
	require_once "includes/helpers.php";

	// Already logged in? Go straight to your own dashboard.
	if(isset($_SESSION["ROLE"])){
		header("Location: " . home_for_role($_SESSION["ROLE"]));
		exit;
	}

	$base = "";
	$page_title = "Login";

	$message = "";
	if(isset($_GET["error"])){
		$message = "Invalid email or password.";
	}
	$success = isset($_GET["registered"]) ? "Account created! You can now log in." : "";

	require "includes/header.php";
?>
<div class="row justify-content-center">
	<div class="col-md-5">
		<div class="card shadow-sm">
			<div class="card-body p-4">
				<h1 class="h4 text-ub text-center mb-4">UB FOOD STALL LOGIN</h1>
				<?php alert($message); alert($success, "success"); ?>
				<form method="post" action="login.php">
					<div class="mb-3">
						<label class="form-label" for="email">Email</label>
						<input class="form-control" type="email" id="email" name="email" placeholder="Email" required>
					</div>
					<div class="mb-3">
						<label class="form-label" for="password">Password</label>
						<input class="form-control" type="password" id="password" name="password" placeholder="Password" required>
					</div>
					<button class="btn btn-ub w-100" name="login_btn">Login</button>
				</form>
				<p class="text-center mt-3 mb-0">No account yet? <a href="register.php">Register</a></p>
			</div>
		</div>
	</div>
</div>
<?php require "includes/footer.php"; ?>
