<?php
	/*
	 * Catalog: read-only queries for the customer side (stalls, products, reviews)
	 * plus the two review writes. Customers cannot order, so nothing else is written.
	 */
	class Catalog{
		// A product with this many pieces or fewer is shown as "Low stock".
		const LOW_STOCK = 10;
		const COMMENT_MAX = 500;

		private PDO $pdo;

		public function __construct(PDO $pdo){
			$this -> pdo = $pdo;
		}

		/* available | low | unavailable */
		public static function productState(array $product): string{
			$stock = (int) $product['stock'];

			if($product['status'] !== 'Available' || $stock <= 0){
				return 'unavailable';
			}
			if($stock <= self::LOW_STOCK){
				return 'low';
			}
			return 'available';
		}

		/* ---------- Stalls ---------- */

		public function getStalls(): array{
			$low = (int) self::LOW_STOCK;

			$sql = "SELECT fs.Stall_ID, fs.stallname, fs.location, fs.status,
				(SELECT COUNT(*) FROM product p WHERE p.Stall_ID = fs.Stall_ID) AS product_count,
				(SELECT COUNT(*) FROM product p WHERE p.Stall_ID = fs.Stall_ID AND p.status = 'Available' AND p.stock > $low) AS available_count,
				(SELECT COUNT(*) FROM product p WHERE p.Stall_ID = fs.Stall_ID AND p.status = 'Available' AND p.stock BETWEEN 1 AND $low) AS low_count,
				(SELECT MIN(p.price) FROM product p WHERE p.Stall_ID = fs.Stall_ID AND p.status = 'Available' AND p.stock > 0) AS min_price,
				(SELECT ROUND(AVG(r.rating), 1) FROM review r WHERE r.Stall_ID = fs.Stall_ID) AS avg_rating,
				(SELECT COUNT(*) FROM review r WHERE r.Stall_ID = fs.Stall_ID) AS review_count
				FROM food_stall fs
				ORDER BY fs.Stall_ID ASC";

			return $this -> pdo -> query($sql) -> fetchAll();
		}

		public function getStall(int $stallId){
			$sql = "SELECT fs.Stall_ID, fs.Vendor_ID, fs.stallname, fs.location, fs.status, v.firstname, v.lastname
				FROM food_stall fs
				LEFT JOIN vendor v ON v.Vendor_ID = fs.Vendor_ID
				WHERE fs.Stall_ID = :id";

			$prep = $this -> pdo -> prepare($sql);
			$prep -> execute([":id" => $stallId]);
			return $prep -> fetch();
		}

		/* ---------- Products ---------- */

		public function getProducts(int $stallId): array{
			$sql = "SELECT p.Product_ID, p.productname, p.description, p.price, p.stock, p.status,
				(SELECT ROUND(AVG(pr.rating), 1) FROM product_review pr WHERE pr.Product_ID = p.Product_ID) AS avg_rating,
				(SELECT COUNT(*) FROM product_review pr WHERE pr.Product_ID = p.Product_ID) AS review_count
				FROM product p
				WHERE p.Stall_ID = :id
				ORDER BY p.productname ASC";

			$prep = $this -> pdo -> prepare($sql);
			$prep -> execute([":id" => $stallId]);
			$rows = $prep -> fetchAll();

			foreach($rows as $i => $row){
				$rows[$i]['state'] = self::productState($row);
			}
			return $rows;
		}

		public function getProduct(int $productId){
			$sql = "SELECT p.Product_ID, p.Stall_ID, p.productname, p.description, p.price, p.stock, p.status,
				fs.stallname, fs.location, fs.status AS stall_status
				FROM product p
				INNER JOIN food_stall fs ON fs.Stall_ID = p.Stall_ID
				WHERE p.Product_ID = :id";

			$prep = $this -> pdo -> prepare($sql);
			$prep -> execute([":id" => $productId]);
			$row = $prep -> fetch();

			if($row){
				$row['state'] = self::productState($row);
			}
			return $row;
		}

		/* ---------- Review summaries ---------- */

		public function getStallSummary(int $stallId): array{
			return $this -> summarize("review", "Stall_ID", $stallId);
		}

		public function getProductSummary(int $productId): array{
			return $this -> summarize("product_review", "Product_ID", $productId);
		}

		// $table and $column are fixed strings from this class, never user input.
		private function summarize(string $table, string $column, int $id): array{
			$prep = $this -> pdo -> prepare("SELECT rating, COUNT(*) AS n FROM $table WHERE $column = :id GROUP BY rating");
			$prep -> execute([":id" => $id]);

			$dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
			$count = 0;
			$sum = 0;

			foreach($prep -> fetchAll() as $row){
				$stars = (int) $row['rating'];
				if(isset($dist[$stars])){
					$dist[$stars] = (int) $row['n'];
					$count += (int) $row['n'];
					$sum += $stars * (int) $row['n'];
				}
			}

			return [
				"count" => $count,
				"avg" => $count > 0 ? round($sum / $count, 1) : 0,
				"dist" => $dist,
			];
		}

		/* ---------- Review lists ---------- */

		public function getStallReviews(int $stallId): array{
			$sql = "SELECT r.Customer_ID, r.rating, r.reviews AS comment, r.reviewDate AS created,
				COALESCE(CONCAT(c.firstname, ' ', LEFT(c.lastname, 1), '.'), 'Anonymous') AS author
				FROM review r
				LEFT JOIN customer c ON c.Customer_ID = r.Customer_ID
				WHERE r.Stall_ID = :id
				ORDER BY r.reviewDate DESC, r.Review_ID DESC";

			$prep = $this -> pdo -> prepare($sql);
			$prep -> execute([":id" => $stallId]);
			return $prep -> fetchAll();
		}

		public function getProductReviews(int $productId): array{
			$sql = "SELECT pr.Customer_ID, pr.rating, pr.review_text AS comment, pr.created_at AS created,
				COALESCE(CONCAT(c.firstname, ' ', LEFT(c.lastname, 1), '.'), 'Anonymous') AS author
				FROM product_review pr
				LEFT JOIN customer c ON c.Customer_ID = pr.Customer_ID
				WHERE pr.Product_ID = :id
				ORDER BY pr.created_at DESC, pr.ProductReview_ID DESC";

			$prep = $this -> pdo -> prepare($sql);
			$prep -> execute([":id" => $productId]);
			return $prep -> fetchAll();
		}

		/* The signed-in customer's own review, used to prefill the form. */

		public function getMyStallReview(int $customerId, int $stallId){
			$prep = $this -> pdo -> prepare("SELECT Review_ID, rating, reviews AS comment FROM review WHERE Customer_ID = :cid AND Stall_ID = :sid ORDER BY Review_ID DESC LIMIT 1");
			$prep -> execute([":cid" => $customerId, ":sid" => $stallId]);
			return $prep -> fetch();
		}

		public function getMyProductReview(int $customerId, int $productId){
			$prep = $this -> pdo -> prepare("SELECT rating, review_text AS comment FROM product_review WHERE Customer_ID = :cid AND Product_ID = :pid");
			$prep -> execute([":cid" => $customerId, ":pid" => $productId]);
			return $prep -> fetch();
		}

		/* ---------- Search, filter and sort (done in PHP, no JavaScript needed) ---------- */

		// $status: all | open | closed    $sort: number | rating | price | name
		public static function filterStalls(array $stalls, string $q, string $status, string $sort): array{
			$q = mb_strtolower(trim($q));

			$rows = array_values(array_filter($stalls, function($s) use ($q, $status){
				if($status === "open" && $s["status"] !== "Open"){
					return false;
				}
				if($status === "closed" && $s["status"] === "Open"){
					return false;
				}
				if($q !== ""){
					$haystack = mb_strtolower("stall " . $s["Stall_ID"] . " " . $s["stallname"] . " " . $s["location"]);
					if(mb_strpos($haystack, $q) === false){
						return false;
					}
				}
				return true;
			}));

			usort($rows, function($a, $b) use ($sort){
				if($sort === "rating"){
					$c = (float) $b["avg_rating"] <=> (float) $a["avg_rating"];
				}
				elseif($sort === "price"){
					$pa = $a["min_price"] !== null ? (float) $a["min_price"] : INF;
					$pb = $b["min_price"] !== null ? (float) $b["min_price"] : INF;
					$c = $pa <=> $pb;
				}
				elseif($sort === "name"){
					$c = strcasecmp($a["stallname"], $b["stallname"]);
				}
				else{
					$c = 0;
				}
				return $c !== 0 ? $c : ((int) $a["Stall_ID"] <=> (int) $b["Stall_ID"]);
			});

			return $rows;
		}

		// $state: all | available | low | unavailable    $sort: name | rating | price-asc | price-desc
		public static function filterProducts(array $products, string $q, string $state, string $sort): array{
			$q = mb_strtolower(trim($q));

			$rows = array_values(array_filter($products, function($p) use ($q, $state){
				if($state !== "all" && $p["state"] !== $state){
					return false;
				}
				if($q !== ""){
					$haystack = mb_strtolower($p["productname"] . " " . $p["description"]);
					if(mb_strpos($haystack, $q) === false){
						return false;
					}
				}
				return true;
			}));

			usort($rows, function($a, $b) use ($sort){
				if($sort === "rating"){
					$c = (float) $b["avg_rating"] <=> (float) $a["avg_rating"];
				}
				elseif($sort === "price-asc"){
					$c = (float) $a["price"] <=> (float) $b["price"];
				}
				elseif($sort === "price-desc"){
					$c = (float) $b["price"] <=> (float) $a["price"];
				}
				else{
					$c = 0;
				}
				return $c !== 0 ? $c : strcasecmp($a["productname"], $b["productname"]);
			});

			return $rows;
		}

		/* ---------- Review writes (one review per customer per stall or product) ---------- */

		public function saveStallReview(int $customerId, int $stallId, int $rating, string $comment): bool{
			$comment = $this -> cleanComment($comment);

			$stall = $this -> getStall($stallId);
			if(!$stall){
				return false;
			}

			$existing = $this -> getMyStallReview($customerId, $stallId);

			if($existing){
				$prep = $this -> pdo -> prepare("UPDATE review SET rating = :rating, reviews = :reviews, reviewDate = CURDATE() WHERE Review_ID = :rid");
				return $prep -> execute([
					":rating" => $rating,
					":reviews" => $comment,
					":rid" => $existing['Review_ID'],
				]);
			}

			$prep = $this -> pdo -> prepare("INSERT INTO review(Customer_ID, Stall_ID, Vendor_ID, rating, reviews, reviewDate) VALUES(:cid, :sid, :vid, :rating, :reviews, CURDATE())");
			return $prep -> execute([
				":cid" => $customerId,
				":sid" => $stallId,
				":vid" => $stall['Vendor_ID'],
				":rating" => $rating,
				":reviews" => $comment,
			]);
		}

		public function saveProductReview(int $customerId, int $productId, int $rating, string $comment): bool{
			$comment = $this -> cleanComment($comment);

			$sql = "INSERT INTO product_review(Product_ID, Customer_ID, rating, review_text)
				VALUES(:pid, :cid, :rating, :text)
				ON DUPLICATE KEY UPDATE rating = VALUES(rating), review_text = VALUES(review_text), created_at = CURRENT_TIMESTAMP";

			$prep = $this -> pdo -> prepare($sql);
			return $prep -> execute([
				":pid" => $productId,
				":cid" => $customerId,
				":rating" => $rating,
				":text" => $comment,
			]);
		}

		private function cleanComment(string $comment): string{
			$comment = trim($comment);
			return mb_substr($comment, 0, self::COMMENT_MAX);
		}
	}
?>
