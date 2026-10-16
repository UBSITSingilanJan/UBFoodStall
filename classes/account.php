<?php
class Account {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($username, $password, $fname, $lname, $role, $vendor_id = null) {
        $sql = "INSERT INTO accounts (username, password, firstname, lastname, role, Vendor_ID)
                VALUES (:u, :p, :f, :l, :r, :vid)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            "u" => $username, "p" => $password,
            "f" => $fname, "l" => $lname,
            "r" => $role, "vid" => $vendor_id
        ]);
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM accounts WHERE username = :u";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["u" => $username]);
        $row = $stmt->fetch();

        if($row && $password == $row['password']){
            return $row;
        }
        return false;
    }
}
?>