<?php
	class Customer{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function insert_data($firstname, $lastname, $email, $department, $year_level, $user_type, $password, $role){
			$insert = "INSERT INTO customer(firstname, lastname, email, department, year_level, user_type, password, role) VALUES(:firstname, :lastname, :email, :department, :year_level, :user_type, :password, :role)";

			$prep = $this -> pdo -> prepare($insert);
			return $prep -> execute([
				":firstname" => $firstname,
				":lastname" => $lastname,
				":email" => $email,
				":department" => $department,
				":year_level" => $year_level,
				":user_type" => $user_type,
				":password" => $password,
				":role" => $role,
			]);
		}

		public function login($email, $password) {
	        $fetch = "SELECT * FROM customer WHERE email = :email";

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
	}
?>