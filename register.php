<?php
	require_once "includes/helpers.php";
	require_once "dbconn.php";
	require_once "classes/customer.php";
	require_once "classes/vendor.php";

	$customer = new Customer($pdo);
	$vendor = new Vendor($pdo);
	$message = "";

	$departments = ["BSIT", "SON", "SOE", "Others"];
	$user_types = ["Student", "Instructor", "Staff"];

	if(isset($_POST['register_btn'])){
		$firstname = trim($_POST['firstname']);
		$lastname = trim($_POST['lastname']);
		$email = trim($_POST['email']);
		$department = $_POST['department'];
		$year_level = (int) $_POST['year_level'];
		$user_type = $_POST['user_type'];
		$password = $_POST['password'];

		if(!in_array($department, $departments, true) || !in_array($user_type, $user_types, true)){
			$message = "Please choose a valid department and user type.";
		}
		elseif($year_level < 1 || $year_level > 9){
			$message = "Year level must be between 1 and 9.";
		}
		elseif(strlen($password) < 6){
			$message = "Password must be at least 6 characters.";
		}
		elseif($customer -> email_exists($email) || $vendor -> email_exists($email) || $email === "admin@gmail.com"){
			$message = "That email is already registered.";
		}
		else{
			$customer -> insert_data($firstname, $lastname, $email, $department, $year_level, $user_type, $password, "Customer");

			header("Location: index.php?registered=1");
			exit;
		}
	}

	$base = "";
	$page_title = "Register";
	require "includes/header.php";
?>
<div class="row justify-content-center">
	<div class="col-md-6">
		<div class="card shadow-sm">
			<div class="card-body p-4">
				<h1 class="h4 text-ub text-center mb-4">UB FOOD STALL REGISTER</h1>
				<?php alert($message); ?>
				<form method="post">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label">First name</label>
							<input class="form-control" type="text" name="firstname" value="<?php echo e($_POST['firstname'] ?? ''); ?>" required>
						</div>
						<div class="col-md-6">
							<label class="form-label">Last name</label>
							<input class="form-control" type="text" name="lastname" value="<?php echo e($_POST['lastname'] ?? ''); ?>" required>
						</div>
						<div class="col-12">
							<label class="form-label">Email</label>
							<input class="form-control" type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
						</div>
						<div class="col-md-6">
							<label class="form-label">Department</label>
							<select class="form-select" name="department">
								<?php foreach($departments as $d): ?>
									<option value="<?php echo e($d); ?>"><?php echo e($d); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-6">
							<label class="form-label">Year level</label>
							<input class="form-control" type="number" name="year_level" min="1" max="9" value="<?php echo e($_POST['year_level'] ?? '1'); ?>" required>
						</div>
						<div class="col-12">
							<label class="form-label">I am a</label>
							<select class="form-select" name="user_type">
								<?php foreach($user_types as $t): ?>
									<option value="<?php echo e($t); ?>"><?php echo e($t); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-12">
							<label class="form-label">Password</label>
							<input class="form-control" type="password" name="password" minlength="6" required>
						</div>
					</div>
					<div class="d-flex justify-content-between align-items-center mt-4">
						<a href="index.php">Cancel</a>
						<button class="btn btn-ub" name="register_btn">Register</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php require "includes/footer.php"; ?>
