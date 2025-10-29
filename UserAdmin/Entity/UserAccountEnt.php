<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class UserAccountEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all user accounts
    public function fetchAllUsers(): array {
        $sql = "SELECT userID, name, email, password, role, status FROM users ORDER BY userID ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // fetch all available roles from active profiles
    public function fetchAllProfiles(): array {
        $sql = "SELECT role FROM profiles WHERE status = 'Active' ORDER BY role ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // insert for account creation
    public function insertUser(string $name, string $email, string $password, string $role): string {
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            return "All fields are required.";
        }

        $check = $this->conn->prepare("SELECT userID FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            return "Error: Email already exists.";
        }

        $stmt = $this->conn->prepare("
            INSERT INTO users (name, email, password, role, status)
            VALUES (?, ?, ?, ?, 'Active')
        ");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        return $stmt->execute() ? "User account created successfully." : "Error creating user: " . $this->conn->error;
    }

    // update for account updates
    public function updateUser(int $id, string $name, string $email, string $password, string $role): string {
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            return "All fields are required.";
        }

        $sql = "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE userID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return "Database error preparing statement.";

        $stmt->bind_param("ssssi", $name, $email, $password, $role, $id);
        $success = $stmt->execute();

        return $success ? "User account updated successfully." : "Error updating user account.";
    }

    // toggle active or suspend status for suspend
    public function toggleStatus(int $id): bool {
        $sql = "SELECT status FROM users WHERE userID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (!$user) return false;

        $newStatus = ($user['status'] === 'Active') ? 'Inactive' : 'Active';

        $update = $this->conn->prepare("UPDATE users SET status = ? WHERE userID = ?");
        if (!$update) return false;

        $update->bind_param("si", $newStatus, $id);
        return $update->execute();
    }

    // search for searching account
    public function searchUsers(string $name, string $email, string $role, string $status): array {
        $sql = "SELECT userID, name, email, role, status FROM users WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($name)) {
            $words = preg_split('/\s+/', trim($name));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/'), $words));
            $sql .= " AND name REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($email)) {
            $words = preg_split('/\s+/', trim($email));
            $regex = implode('.*', array_map(fn($w) => preg_quote($w, '/'), $words)); 
            $sql .= " AND email REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($role)) {
            $words = preg_split('/\s+/', trim($role));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/'), $words));
            $sql .= " AND role REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
