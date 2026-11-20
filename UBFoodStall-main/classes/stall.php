<?php
	class Stall{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		// Every stall with its average rating and number of reviews (customer dashboard).
		public function fetch_all(){
			$prep = $this -> pdo -> prepare("SELECT food_stall.*, AVG(review.rating) AS avg_rating, COUNT(review.Review_ID) AS review_count
				FROM food_stall LEFT JOIN review ON review.Stall_ID = food_stall.Stall_ID
				GROUP BY food_stall.Stall_ID
				ORDER BY food_stall.status ASC, food_stall.stallname ASC");
			$prep -> execute();
			return $prep -> fetchAll();
		}

		public function find($Stall_ID){
			$prep = $this -> pdo -> prepare("SELECT food_stall.*, AVG(review.rating) AS avg_rating, COUNT(review.Review_ID) AS review_count
				FROM food_stall LEFT JOIN review ON review.Stall_ID = food_stall.Stall_ID
				WHERE food_stall.Stall_ID = :Stall_ID
				GROUP BY food_stall.Stall_ID");
			$prep -> execute([":Stall_ID" => $Stall_ID]);
			return $prep -> fetch();
		}

		// Only the vendor who owns the stall can change its status.
		public function set_status($Stall_ID, $Vendor_ID, $status){
			if(!in_array($status, ["Open", "Closed"], true)){
				return false;
			}
			$prep = $this -> pdo -> prepare("UPDATE food_stall SET status = :status WHERE Stall_ID = :Stall_ID AND Vendor_ID = :Vendor_ID");
			return $prep -> execute([":status" => $status, ":Stall_ID" => $Stall_ID, ":Vendor_ID" => $Vendor_ID]);
		}
	}
?>
