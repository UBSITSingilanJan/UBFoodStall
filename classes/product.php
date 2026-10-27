<?php
class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Add a product to a stall
    public function create($stall_id, $name, $price, $status) {
        $insert = "INSERT INTO product (Stall_ID, ProductName, Price, Status)
                VALUES (:sid, :name, :price, :status)";
        $prep = $this->conn->prepare($insert);
        $prep->execute([
            "sid" => $stall_id, "name" => $name,
            "price" => $price, "status" => $status
        ]);
        return $this->conn->lastInsertId();
    }

    // All products of one stall
    public function getByStall($stall_id) {
        $select = "SELECT * FROM product WHERE Stall_ID = :sid ORDER BY Product_ID DESC";
        $prep = $this->conn->prepare($select);
        $prep->execute(["sid" => $stall_id]);
        return $prep->fetchAll();
    }
    public function getById($product_id, $stall_id) {
        $select = "SELECT * FROM product WHERE Product_ID = :pid AND Stall_ID = :sid";
        $prep = $this->conn->prepare($select);
        $prep->execute(["pid" => $product_id, "sid" => $stall_id]);
        return $prep->fetch();
    }

    public function update($product_id, $stall_id, $name, $price, $status) {
        $update = "UPDATE product
                SET ProductName = :name, Price = :price, Status = :status
                WHERE Product_ID = :pid AND Stall_ID = :sid";
        $prep = $this->conn->prepare($update);
        $prep->execute([
            "name" => $name, "price" => $price, "status" => $status,
            "pid" => $product_id, "sid" => $stall_id
        ]);
        return $prep->rowCount();
    }

    public function delete($product_id, $stall_id) {
        $delete = "DELETE FROM product WHERE Product_ID = :pid AND Stall_ID = :sid";
        $prep = $this->conn->prepare($delete);
        $prep->execute(["pid" => $product_id, "sid" => $stall_id]);
        return $prep->rowCount();
    }
}
?>