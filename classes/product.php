<?php
	class Product{
		private PDO $pdo;

		function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		private function status_for($stock){
			return $stock > 0 ? "Available" : "Not Available";
		}

		public function insert_data($Stall_ID, $productname, $price, $stock){
			$prep = $this -> pdo -> prepare("INSERT INTO product(Stall_ID, productname, price, stock, status) VALUES(:Stall_ID, :productname, :price, :stock, :status)");
			return $prep -> execute([
				":Stall_ID" => $Stall_ID,
				":productname" => $productname,
				":price" => $price,
				":stock" => $stock,
				":status" => $this -> status_for($stock),
			]);
		}

		// All products of one stall (vendor dashboard).
		public function fetch_by_stall($Stall_ID){
			$prep = $this -> pdo -> prepare("SELECT * FROM product WHERE Stall_ID = :Stall_ID ORDER BY Product_ID DESC");
			$prep -> execute([":Stall_ID" => $Stall_ID]);
			return $prep -> fetchAll();
		}

		// Stock can only be changed by the stall that owns the product.
		public function update_stock($Product_ID, $Stall_ID, $stock){
			$prep = $this -> pdo -> prepare("UPDATE product SET stock = :stock, status = :status WHERE Product_ID = :Product_ID AND Stall_ID = :Stall_ID");
			return $prep -> execute([
				":stock" => $stock,
				":status" => $this -> status_for($stock),
				":Product_ID" => $Product_ID,
				":Stall_ID" => $Stall_ID,
			]);
		}

		public function delete_data($Product_ID, $Stall_ID){
			$prep = $this -> pdo -> prepare("DELETE FROM product WHERE Product_ID = :Product_ID AND Stall_ID = :Stall_ID");
			return $prep -> execute([":Product_ID" => $Product_ID, ":Stall_ID" => $Stall_ID]);
		}

		// Available products of open stalls (customer dashboard). 0 = all stalls.
		public function fetch_available($Stall_ID = 0){
			$sql = "SELECT product.*, food_stall.stallname, food_stall.location
					FROM product INNER JOIN food_stall ON product.Stall_ID = food_stall.Stall_ID
					WHERE product.status = 'Available' AND food_stall.status = 'Open'";
			$params = [];
			if($Stall_ID > 0){
				$sql .= " AND product.Stall_ID = :Stall_ID";
				$params[":Stall_ID"] = $Stall_ID;
			}
			$sql .= " ORDER BY product.price ASC";

			$prep = $this -> pdo -> prepare($sql);
			$prep -> execute($params);
			return $prep -> fetchAll();
		}

		public function fetch_stalls(){
			$prep = $this -> pdo -> prepare("SELECT Stall_ID, stallname FROM food_stall WHERE status = 'Open' ORDER BY stallname");
			$prep -> execute();
			return $prep -> fetchAll();
		}
	}
?>
