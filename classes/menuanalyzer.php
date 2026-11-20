<?php
	// Analysis for the customer dashboard. $menu is a list of product rows (productname, price).
	class MenuAnalyzer{
		private array $menu;
		private float $budget;

		public function __construct(array $menuData, float $budget){
			$this -> menu = $menuData;
			$this -> budget = $budget;
		}

		public function getAveragePrice(){
			if(count($this -> menu) == 0){
				return 0;
			}
			return array_sum(array_column($this -> menu, 'price')) / count($this -> menu);
		}

		public function getCheapestItem(){
			if(count($this -> menu) == 0){
				return null;
			}
			$cheapest = $this -> menu[0];
			foreach($this -> menu as $food){
				if($food['price'] < $cheapest['price']){
					$cheapest = $food;
				}
			}
			return $cheapest;
		}

		public function getMostExpensiveItem(){
			if(count($this -> menu) == 0){
				return null;
			}
			$most = $this -> menu[0];
			foreach($this -> menu as $food){
				if($food['price'] > $most['price']){
					$most = $food;
				}
			}
			return $most;
		}

		public function getAffordableItems(){
			$affordable = [];
			foreach($this -> menu as $food){
				if($food['price'] <= $this -> budget){
					$affordable[] = $food;
				}
			}
			return $affordable;
		}
	}
?>
