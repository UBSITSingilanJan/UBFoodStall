<?php
	// Analysis for the vendor dashboard. $inventory is a list of product rows (productname, price, stock).
	class InventoryAnalyzer{
		private array $inventory;

		public function __construct(array $inventoryData){
			$this -> inventory = $inventoryData;
		}

		public function getAveragePrice(){
			if(count($this -> inventory) == 0){
				return 0;
			}
			return array_sum(array_column($this -> inventory, 'price')) / count($this -> inventory);
		}

		public function getMinStock(){
			if(count($this -> inventory) == 0){
				return 0;
			}
			return min(array_column($this -> inventory, 'stock'));
		}

		public function getMaxStock(){
			if(count($this -> inventory) == 0){
				return 0;
			}
			return max(array_column($this -> inventory, 'stock'));
		}

		// Total value of everything still in stock (price x stock).
		public function getInventoryValue(){
			$total = 0;
			foreach($this -> inventory as $item){
				$total += $item['price'] * $item['stock'];
			}
			return $total;
		}

		public function getLowStockAlerts($threshold = 10){
			$alerts = [];
			foreach($this -> inventory as $item){
				if($item['stock'] < $threshold){
					$alerts[] = $item['productname'] . " is running low! Only " . $item['stock'] . " left.";
				}
			}
			return $alerts;
		}
	}
?>
