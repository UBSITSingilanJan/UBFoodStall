<?php
	require_once "../includes/helpers.php";
	require_once "../dbconn.php";
	require_once "../classes/vendor.php";
	require_once "../classes/customer.php";

	require_role("admin");

	$conn = new Vendor($pdo);
	$customer = new Customer($pdo);
	$message = "";

	if(isset($_POST['add_vendor'])){
		$firstname = trim($_POST['firstname']);
		$lastname = trim($_POST['lastname']);
		$contact = trim($_POST['contact']);
		$email = trim($_POST['email']);
		$password = $_POST['password'];
		$location = trim($_POST['location']);
		$stallname = trim($_POST['stallname']);

		if(strlen($password) < 6){
			$message = "Password must be at least 6 characters.";
		}
		elseif($conn -> email_exists($email) || $customer -> email_exists($email) || $email === "admin@gmail.com"){
			$message = "That email is already registered.";
		}
		else{
			$conn -> insert_data($firstname, $lastname, $contact, $email, $password, "Vendor", $location, $stallname);

			header("Location: index.php?saved=1");
			exit;
		}
	}

	$base = "../";
	$page_title = "Add Vendor";
	$nav = ["Vendors" => "index.php"];
	$show_logout = true;
	require "../includes/header.php";
?>
<div class="row justify-content-center">
	<div class="col-md-7">
		<div class="card shadow-sm"><div class="card-body p-4">
			<h1 class="h4 text-ub mb-3">Add Vendor</h1>
			<?php alert($message); ?>
			<form method="post">
				<h2 class="h6 text-muted">Vendor information</h2>
				<div class="row g-3 mb-4">
					<div class="col-md-6"><input class="form-control" type="text" name="firstname" placeholder="First name" value="<?php echo e($_POST['firstname'] ?? ''); ?>" required></div>
					<div class="col-md-6"><input class="form-control" type="text" name="lastname" placeholder="Last name" value="<?php echo e($_POST['lastname'] ?? ''); ?>" required></div>
					<div class="col-md-6"><input class="form-control" type="tel" name="contact" placeholder="Contact number" value="<?php echo e($_POST['contact'] ?? ''); ?>" required></div>
					<div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="Email" value="<?php echo e($_POST['email'] ?? ''); ?>" required></div>
					<div class="col-12"><input class="form-control" type="password" name="password" placeholder="Password (min. 6 characters)" minlength="6" required></div>
				</div>
				<h2 class="h6 text-muted">Stall information</h2>
				<div class="row g-3 mb-4">
					<div class="col-md-6"><input class="form-control" type="text" name="stallname" placeholder="Stall name" value="<?php echo e($_POST['stallname'] ?? ''); ?>" required></div>
					<div class="col-md-6"><input class="form-control" type="text" name="location" placeholder="Location" value="<?php echo e($_POST['location'] ?? ''); ?>" required></div>
				</div>
				<div class="d-flex justify-content-between">
					<a href="index.php">Cancel</a>
					<button class="btn btn-ub" name="add_vendor">Add Vendor</button>
				</div>
			</form>
		</div></div>
	</div>
</div>
<?php require "../includes/footer.php"; ?>
