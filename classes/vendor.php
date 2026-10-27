<?php
class Vendor {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($fname, $lname, $contact, $email) {
        $select = "INSERT INTO vendor (First_Name, Last_Name, Contact, Email)
                VALUES (:fname, :lname, :contact, :email)";
        $prep = $this->conn->prepare($select);
        $prep->execute([
            "fname" => $fname, "lname" => $lname,
            "contact" => $contact, "email" => $email
        ]);
        return $this->conn->lastInsertId();
    }

    public function getAll() {
        $prep = $this->conn->prepare("SELECT * FROM vendor ORDER BY Vendor_ID DESC");
        $prep->execute();
        return $prep->fetchAll();
    }

    public function getById($id) {
        $prep = $this->conn->prepare("SELECT * FROM vendor WHERE Vendor_ID = :id");
        $prep->execute(["id" => $id]);
        return $prep->fetch();
    }

    public function login($contact, $email) {
        $select = "SELECT * FROM vendor WHERE Contact = :c AND Email = :e";
        $prep = $this->conn->prepare($select);
        $prep->execute(["c" => $contact, "e" => $email]);
        return $prep->fetch();
    }
}
?>