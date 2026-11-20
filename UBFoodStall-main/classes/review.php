<?php
	class Review{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		public function fetch_by_stall($Stall_ID){
			$prep = $this -> pdo -> prepare("SELECT review.*, customer.firstname, customer.lastname
				FROM review LEFT JOIN customer ON review.Customer_ID = customer.Customer_ID
				WHERE review.Stall_ID = :Stall_ID
				ORDER BY review.reviewDate DESC, review.Review_ID DESC");
			$prep -> execute([":Stall_ID" => $Stall_ID]);
			return $prep -> fetchAll();
		}

		public function find_by_customer($Stall_ID, $Customer_ID){
			$prep = $this -> pdo -> prepare("SELECT * FROM review WHERE Stall_ID = :Stall_ID AND Customer_ID = :Customer_ID");
			$prep -> execute([":Stall_ID" => $Stall_ID, ":Customer_ID" => $Customer_ID]);
			return $prep -> fetch();
		}

		// One review per customer per stall: posting again replaces the old one.
		public function save($Stall_ID, $Vendor_ID, $Customer_ID, $rating, $reviews){
			$old = $this -> find_by_customer($Stall_ID, $Customer_ID);

			if($old){
				$prep = $this -> pdo -> prepare("UPDATE review SET rating = :rating, reviews = :reviews, reviewDate = CURDATE() WHERE Review_ID = :Review_ID");
				return $prep -> execute([":rating" => $rating, ":reviews" => $reviews, ":Review_ID" => $old['Review_ID']]);
			}

			$prep = $this -> pdo -> prepare("INSERT INTO review(Customer_ID, Stall_ID, Vendor_ID, rating, reviews, reviewDate) VALUES(:Customer_ID, :Stall_ID, :Vendor_ID, :rating, :reviews, CURDATE())");
			return $prep -> execute([
				":Customer_ID" => $Customer_ID,
				":Stall_ID" => $Stall_ID,
				":Vendor_ID" => $Vendor_ID,
				":rating" => $rating,
				":reviews" => $reviews,
			]);
		}

		public function delete_by_customer($Stall_ID, $Customer_ID){
			$prep = $this -> pdo -> prepare("DELETE FROM review WHERE Stall_ID = :Stall_ID AND Customer_ID = :Customer_ID");
			return $prep -> execute([":Stall_ID" => $Stall_ID, ":Customer_ID" => $Customer_ID]);
		}
	}
?>
