<?php
class Account {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($username, $password, $fname, $lname, $role, $vendor_id = null) {
        $insert = "INSERT INTO accounts (username, password, firstname, lastname, role, Vendor_ID)
                VALUES (:u, :p, :f, :l, :r, :vid)";
        $prep = $this->conn->prepare($insert);
        return $prep->execute([
            "u" => $username, "p" => $password,
            "f" => $fname, "l" => $lname,
            "r" => $role, "vid" => $vendor_id
        ]);
    }

    public function login($username, $password) {
        $insert = "SELECT * FROM accounts WHERE username = :u";
        $prep = $this->conn->prepare($insert);
        $prep->execute(["u" => $username]);
        $row = $prep->fetch();

        if($row && $password == $row['password']){
            return $row;
        }
        return false;
    }
}
?>