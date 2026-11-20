<?php
session_start();
require_once "../dbconn.php";

// only a logged in vendor can open this page
if (!isset($_SESSION["Vendor_ID"])) {
    header("Location: ../index.php");
    exit;
}

class VendorReviews {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // all reviews of the vendor's stall, with the customer who wrote each one
    public function getReviews($vendorId) {
        $prep = $this->pdo->prepare("
            SELECT review.Review_ID, review.rating, review.reviews, review.reviewDate,
                   customer.firstname, customer.lastname, customer.department, customer.year_level
            FROM review
            INNER JOIN food_stall ON review.Stall_ID = food_stall.Stall_ID
            LEFT JOIN customer ON review.Customer_ID = customer.Customer_ID
            WHERE food_stall.Vendor_ID = :Vendor_ID
            ORDER BY review.reviewDate DESC, review.Review_ID DESC
        ");
        $prep->execute([":Vendor_ID" => $vendorId]);
        return $prep->fetchAll();
    }
}

$vendorReviews = new VendorReviews($pdo);
$rows = $vendorReviews->getReviews($_SESSION["Vendor_ID"]);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Reviews</title>
</head>
<body>
    <h1>Customer Reviews</h1>
    <a href="index.php">Back to Dashboard</a> |
    <a href="logout.php">Logout</a>

    <hr>

    <table border="1" cellpadding="8">
        <tr>
            <th>Date</th>
            <th>Customer</th>
            <th>Department</th>
            <th>Year</th>
            <th>Rating</th>
            <th>Review</th>
        </tr>
        <?php
            if (count($rows) > 0) {
                foreach ($rows as $row) {
        ?>
                    <tr>
                        <td><?php echo $row["reviewDate"]; ?></td>
                        <td><?php echo $row["firstname"] ? htmlspecialchars($row["firstname"] . " " . $row["lastname"]) : "Anonymous"; ?></td>
                        <td><?php echo htmlspecialchars($row["department"] ?? ""); ?></td>
                        <td><?php echo $row["year_level"]; ?></td>
                        <td><?php echo $row["rating"]; ?> / 5</td>
                        <td><?php echo htmlspecialchars($row["reviews"]); ?></td>
                    </tr>
        <?php
                }
            } else {
        ?>
                <tr>
                    <td colspan="6">No reviews yet.</td>
                </tr>
        <?php
            }
        ?>
    </table>
</body>
</html>