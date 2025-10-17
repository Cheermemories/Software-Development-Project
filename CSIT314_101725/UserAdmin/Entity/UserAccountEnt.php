<?php
require_once __DIR__ . '/../Database/db_connect.php';

class UserAccountEnt {
    private mysqli $conn;

    private int $userID;
    private string $name;
    private string $email;
    private string $password;
    private string $role;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetching user account data
    public function fetchAllUsers(): array {
        $sql = "SELECT * FROM users";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

?>
