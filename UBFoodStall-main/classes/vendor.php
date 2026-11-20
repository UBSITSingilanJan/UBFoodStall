<?php
	class Vendor{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function email_exists($email, $except_id = 0){
			$prep = $this -> pdo -> prepare("SELECT 1 FROM vendor WHERE email = :email AND Vendor_ID <> :id");
			$prep -> execute([":email" => $email, ":id" => $except_id]);
			return (bool) $prep -> fetch();
		}

		public function insert_data($firstname, $lastname, $contact, $email, $password, $role, $location, $stallname){
			$this -> pdo -> beginTransaction();

			try{
				$prep_vendor = $this -> pdo -> prepare("INSERT INTO vendor(firstname, lastname, contact, email, password, role) VALUES(:firstname, :lastname, :contact, :email, :password, :role)");
				$prep_vendor -> execute([
					":firstname" => $firstname,
					":lastname" => $lastname,
					":contact" => $contact,
					":email" => $email,
					":password" => password_hash($password, PASSWORD_DEFAULT),
					":role" => $role,
				]);

				$vendor_id = $this -> pdo -> lastInsertId();

				$prep_stall = $this -> pdo -> prepare("INSERT INTO food_stall(Vendor_ID, location, stallname) VALUES(:Vendor_ID, :location, :stallname)");
				$prep_stall -> execute([
					":Vendor_ID" => $vendor_id,
					":location" => $location,
					":stallname" => $stallname,
				]);

				$this -> pdo -> commit();
				return true;
			}
			catch(PDOException $e){
				$this -> pdo -> rollBack();
				throw $e;
			}
		}

		public function login($email, $password){
			$prep = $this -> pdo -> prepare("SELECT * FROM vendor WHERE email = :email");
			$prep -> execute([":email" => $email]);
			$row = $prep -> fetch();

			if($row && password_verify($password, $row['password'])){
				return $row;
			}
			return false;
		}

		public function fetch_data(){
			$prep = $this -> pdo -> prepare("SELECT * FROM vendor INNER JOIN food_stall ON vendor.Vendor_ID = food_stall.Vendor_ID ORDER BY vendor.Vendor_ID DESC");
			$prep -> execute();
			return $prep -> fetchAll();
		}

		// Finds the stall that belongs to a vendor.
		public function find_stall($Vendor_ID){
			$prep = $this -> pdo -> prepare("SELECT * FROM food_stall WHERE Vendor_ID = :Vendor_ID");
			$prep -> execute([":Vendor_ID" => $Vendor_ID]);
			return $prep -> fetch();
		}

		public function delete_data($Vendor_ID){
			$this -> pdo -> beginTransaction();

			try{
				// Remove everything that points to the vendor's stall first (foreign keys).
				$this -> pdo -> prepare("DELETE FROM product WHERE Stall_ID IN (SELECT Stall_ID FROM food_stall WHERE Vendor_ID = :id)") -> execute([":id" => $Vendor_ID]);
				$this -> pdo -> prepare("DELETE FROM review WHERE Vendor_ID = :id") -> execute([":id" => $Vendor_ID]);
				$this -> pdo -> prepare("DELETE FROM food_stall WHERE Vendor_ID = :id") -> execute([":id" => $Vendor_ID]);
				$this -> pdo -> prepare("DELETE FROM vendor WHERE Vendor_ID = :id") -> execute([":id" => $Vendor_ID]);

				$this -> pdo -> commit();
				return true;
			}
			catch(PDOException $e){
				$this -> pdo -> rollBack();
				throw $e;
			}
		}

		// If $password is empty, the current password is kept.
		public function update_data($Vendor_ID, $firstname, $lastname, $contact, $email, $password, $role, $location, $stallname){
			$this -> pdo -> beginTransaction();

			try{
				$params = [
					":firstname" => $firstname,
					":lastname" => $lastname,
					":contact" => $contact,
					":email" => $email,
					":role" => $role,
					":Vendor_ID" => $Vendor_ID,
				];
				$password_sql = "";
				if($password !== ""){
					$password_sql = ", password = :password";
					$params[":password"] = password_hash($password, PASSWORD_DEFAULT);
				}

				$this -> pdo -> prepare("UPDATE vendor SET firstname = :firstname, lastname = :lastname, contact = :contact, email = :email, role = :role" . $password_sql . " WHERE Vendor_ID = :Vendor_ID") -> execute($params);

				$this -> pdo -> prepare("UPDATE food_stall SET location = :location, stallname = :stallname WHERE Vendor_ID = :Vendor_ID") -> execute([
					":location" => $location,
					":stallname" => $stallname,
					":Vendor_ID" => $Vendor_ID,
				]);

				$this -> pdo -> commit();
				return true;
			}
			catch(PDOException $e){
				$this -> pdo -> rollBack();
				throw $e;
			}
		}

		public function show_update_data($Vendor_ID){
			$prep = $this -> pdo -> prepare("SELECT * FROM vendor INNER JOIN food_stall ON vendor.Vendor_ID = food_stall.Vendor_ID WHERE vendor.Vendor_ID = :Vendor_ID");
			$prep -> execute([":Vendor_ID" => $Vendor_ID]);
			return $prep -> fetch();
		}
	}
?>
