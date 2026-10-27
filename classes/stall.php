<?php
class Stall {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    // Add a stall for a vendor
    public function create($vendor_id, $name, $location, $status) {
        $update = "INSERT INTO food_stall (Vendor_ID, StallName, Location, Status)
                VALUES (:vid, :name, :location, :status)";
        $prep = $this->conn->prepare($update);
        $prep->execute([
            "vid" => $vendor_id, "name" => $name,
            "location" => $location, "status" => $status
        ]);
        return $this->conn->lastInsertId();
    }

    public function getByVendor($vendor_id) {
        $prep = $this->conn->prepare("SELECT * FROM food_stall WHERE Vendor_ID = :vid LIMIT 1");
        $prep->execute(["vid" => $vendor_id]);
        return $prep->fetch();
    }

    public function update($stall_id, $vendor_id, $name, $location, $status) {
        $update = "UPDATE food_stall
                SET StallName = :name, Location = :location, Status = :status
                WHERE Stall_ID = :sid AND Vendor_ID = :vid";
        $prep = $this->conn->prepare($update);
        $prep->execute([
            "name" => $name, "location" => $location, "status" => $status,
            "sid" => $stall_id, "vid" => $vendor_id
        ]);
        return $prep->rowCount();
    }
}
?>