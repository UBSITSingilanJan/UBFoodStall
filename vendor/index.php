<?php
session_start();

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
    <a href="logout.php">Logout</a>
    
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