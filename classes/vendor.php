<?php
	class Vendor{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		
		public function insert_data($firstname, $lastname, $contact, $email, $password, $role, $location, $stallname){

		    $this -> pdo -> beginTransaction();

		    $insert_vendor = "INSERT INTO vendor(firstname, lastname, contact, email, password, role) VALUES(:firstname, :lastname, :contact, :email, :password, :role)";

		    $prep_vendor = $this -> pdo -> prepare($insert_vendor);

		    $prep_vendor -> execute([
		        ":firstname" => $firstname,
		        ":lastname" => $lastname,
		        ":contact" => $contact,
		        ":email" => $email,
		        ":password" => $password,
		        ":role" => $role,
		    ]);

		    $vendor_id = $this -> pdo -> lastInsertId();

		    $insert_stall = "INSERT INTO food_stall(Vendor_ID, location, stallname) VALUES(:Vendor_ID, :location, :stallname)";

		    $prep_stall = $this -> pdo -> prepare($insert_stall);

		    $prep_stall -> execute([
		        ":Vendor_ID" => $vendor_id,
		        ":location" => $location,
		        ":stallname" => $stallname,
		    ]);

		    $this -> pdo -> commit();

		    return true;
		}


		public function login($email, $password){
	        $fetch = "SELECT * FROM vendor WHERE email = :email";

	        $prep = $this -> pdo ->prepare($fetch);
	        $prep -> execute([
	        	":email" => $email,
	        ]);
	        $row = $prep -> fetch();

	        if($row && $password == $row['password']){
	            return $row;
	        }
	        return false;
	    }

	    
		public function fetch_data(){
		    $prep = $this -> pdo -> prepare("SELECT * FROM vendor INNER JOIN food_stall ON vendor.Vendor_ID = food_stall.Vendor_ID ORDER BY vendor.Vendor_ID DESC");
		    $prep -> execute();
		    return $prep->fetchAll();
		}

		
		public function delete_data($Vendor_ID){

		    $this -> pdo -> beginTransaction();

		    $prep_stall = $this -> pdo -> prepare("DELETE FROM food_stall WHERE Vendor_ID = :Vendor_ID");
		    $prep_stall -> execute([
		        ":Vendor_ID" => $Vendor_ID
		    ]);

		    $prep_vendor = $this -> pdo -> prepare("DELETE FROM vendor WHERE Vendor_ID = :Vendor_ID");
		    $prep_vendor -> execute([
		        ":Vendor_ID" => $Vendor_ID
		    ]);

		    $this -> pdo -> commit();

		    return true;
		}

		public function update_data($Vendor_ID, $firstname, $lastname, $contact, $email, $password, $role, $location, $stallname){

		    $this -> pdo -> beginTransaction();

		    $update_vendor = "UPDATE vendor SET firstname = :firstname, lastname = :lastname, contact = :contact, email = :email, password = :password, role = :role WHERE Vendor_ID = :Vendor_ID";

		    $prep_vendor = $this -> pdo -> prepare($update_vendor);

		    $prep_vendor->execute([
		        ":firstname" => $firstname,
		        ":lastname" => $lastname,
		        ":contact" => $contact,
		        ":email" => $email,
		        ":password" => $password,
		        ":role" => $role,
		        ":Vendor_ID" => $Vendor_ID,
		    ]);

		    $update_stall = "UPDATE food_stall SET location = :location, stallname = :stallname WHERE Vendor_ID = :Vendor_ID";

		    $prep_stall = $this->pdo->prepare($update_stall);

		    $prep_stall -> execute([
		        ":location" => $location,
		        ":stallname" => $stallname,
		        ":Vendor_ID" => $Vendor_ID,
		    ]);

		    $this -> pdo -> commit();

		    return true;
		}

		public function show_update_data($Vendor_ID){
		    $prep = $this -> pdo -> prepare("SELECT * FROM vendor INNER JOIN food_stall ON vendor.Vendor_ID = food_stall.Vendor_ID WHERE vendor.Vendor_ID = :Vendor_ID");
			$prep -> execute([
				":Vendor_ID" => $Vendor_ID,
			]);
			return $prep -> fetch();
		}
	}
?>