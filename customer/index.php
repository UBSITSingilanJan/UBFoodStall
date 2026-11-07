<?php
session_start();

class Database {
    public function connect() {
        return true;
    }
}

class MenuAnalyzer {
    private $menu;
    private $budget;

    public function __construct($menuData, $budget) {
        $this->menu = $menuData;
        $this->budget = $budget;
    }

    public function getAveragePrice() {
        if (count($this->menu) == 0) return 0;
        $total = 0;
        foreach ($this->menu as $food) {
            $total += $food['price'];
        }
        return $total / count($this->menu);
    }

    public function getCheapestItem() {
        if (count($this->menu) == 0) return ["food" => "None", "price" => 0];
        $cheapest = $this->menu[0];
        foreach ($this->menu as $food) {
            if ($food['price'] < $cheapest['price']) {
                $cheapest = $food;
            }
        }
        return $cheapest;
    }

    public function getMostExpensiveItem() {
        if (count($this->menu) == 0) return ["food" => "None", "price" => 0];
        $mostExpensive = $this->menu[0];
        foreach ($this->menu as $food) {
            if ($food['price'] > $mostExpensive['price']) {
                $mostExpensive = $food;
            }
        }
        return $mostExpensive;
    }

    public function getAffordableItems() {
        $affordable = [];
        foreach ($this->menu as $food) {
            if ($food['price'] <= $this->budget) {
                $affordable[] = $food;
            }
        }
        return $affordable;
    }
}

if (!isset($_SESSION['menu_data'])) {
    $_SESSION['menu_data'] = [
        ["food" => "Pork Siomai (4pcs)", "price" => 25.00],
        ["food" => "Beef Siomai (4pcs)", "price" => 35.00]
    ];
}

$userBudget = 50.00;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $foodName = $_POST["food_name"] ?? "";
    $foodPrice = $_POST["food_price"] ?? 0;
    $userBudget = (float)($_POST["budget"] ?? 50.00);

    if ($foodName != "" && $foodPrice > 0) {
        $_SESSION['menu_data'][] = [
            "food" => $foodName,
            "price" => (float)$foodPrice
        ];
    }
}

$database = new Database();
$conn = $database->connect();
$analyzer = new MenuAnalyzer($_SESSION['menu_data'], $userBudget);
$cheapest = $analyzer->getCheapestItem();
$most_expensive = $analyzer->getMostExpensiveItem();
$affordable = $analyzer->getAffordableItems();

$customerName = isset($_SESSION["FNAME"]) ? $_SESSION["FNAME"] : "Customer";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
</head>
<body>
    <h1>Welcome <?php echo htmlspecialchars($customerName); ?></h1>
    <a href="logout.php">Logout</a>

    <hr>

    <h2>Menu Analysis Dashboard</h2>
    <form method="POST">
        <label>Food Name:</label>
        <input type="text" name="food_name" required>
        <label>Price:</label>
        <input type="number" step="0.01" name="food_price" required>
        <br><br>
        <label>My Budget (₱):</label>
        <input type="number" step="0.01" name="budget" value="<?php echo $userBudget; ?>" required>
        <button type="submit">Update Menu & Analyze</button>
    </form>
    <br>

    <h3>Aggregations</h3>
    <p>Average Price of Menu: <?php echo number_format($analyzer->getAveragePrice(), 2); ?></p>
    <p>Cheapest Item (MIN): <?php echo $cheapest['food']; ?> (<?php echo number_format($cheapest['price'], 2); ?>)</p>
    <p>Most Expensive Item (MAX): <?php echo $most_expensive['food']; ?> (<?php echo number_format($most_expensive['price'], 2); ?>)</p>
    
    <h3>Report: Affordable Items (Based on Budget)</h3>
    <?php if (count($affordable) > 0): ?>
        <ul>
            <?php foreach ($affordable as $item): ?>
                <li><?php echo $item['food']; ?> - ₱<?php echo number_format($item['price'], 2); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No items fit your budget.</p>
    <?php endif; ?>
</body>
</html>