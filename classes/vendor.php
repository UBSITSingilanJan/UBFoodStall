<?php
class Vendor {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($fname, $lname, $contact, $email) {
        $sql = "INSERT INTO vendor (First_Name, Last_Name, Contact, Email)
                VALUES (:fname, :lname, :contact, :email)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            "fname" => $fname, "lname" => $lname,
            "contact" => $contact, "email" => $email
        ]);
        return $this->conn->lastInsertId();
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM vendor ORDER BY Vendor_ID DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM vendor WHERE Vendor_ID = :id");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch();
    }

    public function login($contact, $email) {
        $sql = "SELECT * FROM vendor WHERE Contact = :c AND Email = :e";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["c" => $contact, "e" => $email]);
        return $stmt->fetch();
    }
}
?>