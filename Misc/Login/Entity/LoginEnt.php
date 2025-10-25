<?php
require_once __DIR__ . '/../../../Database/db_connect.php';

class LoginEnt {
    private mysqli $conn;

    private string $email;
    private string $password;
    private string $role;
    private string $status;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function verifyCredentials($email, $password): ?array {
        $sql = "SELECT * FROM users WHERE email = ? AND password = ? AND status = 'Active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }
}
?>
