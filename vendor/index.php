<?php
session_start();
require_once "../dbconn.php";

class Database {
    public function connect() {
        return true;
    }
}

class InventoryAnalyzer {
    private $inventory;

    public function __construct($inventoryData) {
        $this->inventory = $inventoryData;
    }

    public function getAveragePrice() {
        if (count($this->inventory) == 0) return 0;
        $total = 0;
        foreach ($this->inventory as $item) {
            $total += $item['price'];
        }
        return $total / count($this->inventory);
    }

    public function getMinStock() {
        if (count($this->inventory) == 0) return 0;
        $stocks = array_column($this->inventory, 'stock');
        return min($stocks);
    }

    public function getMaxStock() {
        if (count($this->inventory) == 0) return 0;
        $stocks = array_column($this->inventory, 'stock');
        return max($stocks);
    }

    public function getLowStockAlerts($threshold = 10) {
        $alerts = [];
        foreach ($this->inventory as $item) {
            if ($item['stock'] < $threshold) {
                $alerts[] = "ALERT: " . $item['item'] . " is running low! Only " . $item['stock'] . " left.";
            }
        }
        return $alerts;
    }
}

class Dashboard {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // get the stall of the logged in vendor
    public function getStall($vendorId) {
        $prep = $this->pdo->prepare("SELECT Stall_ID, stallname, status FROM food_stall WHERE Vendor_ID = :Vendor_ID");
        $prep->execute([":Vendor_ID" => $vendorId]);
        return $prep->fetch();
    }

    // count of all products, available and not available
    public function countProducts($stallId) {
        $prep = $this->pdo->prepare("
            SELECT
                COUNT(*) AS total_products,
                COALESCE(SUM(status = 'Available'), 0) AS available_products,
                COALESCE(SUM(status = 'Not Available'), 0) AS unavailable_products
            FROM product
            WHERE Stall_ID = :Stall_ID
        ");
        $prep->execute([":Stall_ID" => $stallId]);
        return $prep->fetch();
    }

    // count of reviews, customers who rated, average rating, and how many gave each star
    public function summarizeReviews($stallId) {
        $prep = $this->pdo->prepare("
            SELECT
                COUNT(*) AS total_reviews,
                COUNT(DISTINCT Customer_ID) AS total_customers,
                COALESCE(ROUND(AVG(rating), 1), 0) AS average_rating,
                COALESCE(SUM(rating = 5), 0) AS star_5,
                COALESCE(SUM(rating = 4), 0) AS star_4,
                COALESCE(SUM(rating = 3), 0) AS star_3,
                COALESCE(SUM(rating = 2), 0) AS star_2,
                COALESCE(SUM(rating = 1), 0) AS star_1
            FROM review
            WHERE Stall_ID = :Stall_ID
        ");
        $prep->execute([":Stall_ID" => $stallId]);
        return $prep->fetch();
    }
}

if (!isset($_SESSION['inventory_data'])) {
    $_SESSION['inventory_data'] = [
        ["item" => "Pork Siomai", "price" => 25.00, "stock" => 50]
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST["item_name"] ?? "";
    $itemPrice = $_POST["item_price"] ?? 0;
    $itemStock = $_POST["item_stock"] ?? 0;

    if ($itemName != "" && $itemPrice > 0) {
        $_SESSION['inventory_data'][] = [
            "item" => $itemName,
            "price" => (float)$itemPrice,
            "stock" => (int)$itemStock
        ];
    }
}

$database = new Database();
$conn = $database->connect();
$analyzer = new InventoryAnalyzer($_SESSION['inventory_data']);
$sales_report = ["Today" => 1500, "This Week" => 8500];

$vendorName = isset($_SESSION["FNAME"]) ? $_SESSION["FNAME"] : "Vendor";

// ---------- STALL DASHBOARD (products, reviews, ratings) ----------
$dashboard = new Dashboard($pdo);

$products = ["total_products" => 0, "available_products" => 0, "unavailable_products" => 0];
$reviews = ["total_reviews" => 0, "total_customers" => 0, "average_rating" => 0, "star_5" => 0, "star_4" => 0, "star_3" => 0, "star_2" => 0, "star_1" => 0];
$stall = false;

if (isset($_SESSION["Vendor_ID"])) {
    $stall = $dashboard->getStall($_SESSION["Vendor_ID"]);
}

if ($stall) {
    $products = $dashboard->countProducts($stall["Stall_ID"]);
    $reviews = $dashboard->summarizeReviews($stall["Stall_ID"]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
</head>
<body>
    <h1>Welcome <?php echo htmlspecialchars($vendorName); ?></h1>
    <a href="logout.php">Logout</a> |
    <a href="reviews.php">View Customer Reviews</a>
    
    <hr>

    <h2>Data Analysis Dashboard</h2>
    <form method="POST">
        <label>Item Name:</label>
        <input type="text" name="item_name" required>
        <label>Price:</label>
        <input type="number" step="0.01" name="item_price" required>
        <label>Stock:</label>
        <input type="number" name="item_stock" required>
        <button type="submit">Add Data</button>
    </form>
    <br>

    <h3>Aggregations</h3>
    <p>Average Item Price: <?php echo number_format($analyzer->getAveragePrice(), 2); ?></p>
    <p>Minimum Stock Level: <?php echo $analyzer->getMinStock(); ?></p>
    <p>Maximum Stock Level: <?php echo $analyzer->getMaxStock(); ?></p>

    <p>Total Products: <?php echo $products["total_products"]; ?></p>
    <p>Available Products: <?php echo $products["available_products"]; ?></p>
    <p>Not Available Products: <?php echo $products["unavailable_products"]; ?></p>
    <p>Total Reviews: <?php echo $reviews["total_reviews"]; ?></p>
    <p>Customers Who Rated: <?php echo $reviews["total_customers"]; ?></p>
    <p>Average Rating: <?php echo $reviews["average_rating"]; ?> / 5</p>

    <table border="1">
        <tr>
            <th>Stars</th>
            <th>Customers</th>
        </tr>
        <?php for ($star = 5; $star >= 1; $star--): ?>
        <tr>
            <td><?php echo $star; ?> star</td>
            <td><?php echo $reviews["star_" . $star]; ?></td>
        </tr>
        <?php endfor; ?>
    </table>

    <h3>Reports</h3>
    <ul>
        <?php foreach ($sales_report as $period => $amount): ?>
            <li><?php echo $period; ?>: <?php echo number_format($amount, 2); ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Alerts</h3>
    <?php
    $alerts = $analyzer->getLowStockAlerts();
    if (count($alerts) > 0) {
        foreach ($alerts as $alert) {
            echo "<p>" . $alert . "</p>";
        }
    } else {
        echo "<p>All items are sufficiently stocked.</p>";
    }
    ?>

    <h3>Current Inventory Data</h3>
    <table border="1">
        <tr>
            <th>Item</th>
            <th>Price</th>
            <th>Stock</th>
        </tr>
        <?php foreach ($_SESSION['inventory_data'] as $item): ?>
        <tr>
            <td><?php echo $item['item']; ?></td>
            <td><?php echo $item['price']; ?></td>
            <td><?php echo $item['stock']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>