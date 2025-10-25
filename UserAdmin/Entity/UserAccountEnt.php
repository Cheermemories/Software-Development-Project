<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class ViewUserAccountEnt {
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

class CreateUserAccountEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function insertUser(string $name, string $email, string $password, string $role): string {
        // check if email already exists
        $check = $this->conn->prepare("SELECT userID FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            return "Error: Email already exists.";
        }

        // insert new user
        $stmt = $this->conn->prepare("
            INSERT INTO users (name, email, password, role, status)
            VALUES (?, ?, ?, ?, 'Active')
        ");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            return "User account created successfully.";
        } else {
            return "Error creating user: " . $this->conn->error;
        }
    }
}

class UpdateUserAccountEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get all users
    public function fetchAllUsers(): array {
        $sql = "SELECT userID, name, email, password, role, status FROM users ORDER BY userID ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // get all roles from profiles table
    public function fetchAllProfiles(): array {
        $sql = "SELECT role FROM profiles WHERE status = 'Active' ORDER BY role ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // update a single user
    public function updateUser(int $id, string $name, string $email, string $password, string $role): bool {
        $sql = "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE userID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("ssssi", $name, $email, $password, $role, $id);
        return $stmt->execute();
    }
}

class SuspendUserAccountEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get all users
    public function fetchAllUsers(): array {
        $sql = "SELECT userID, name, email, role, status FROM users ORDER BY userID ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // get a user by ID
    public function getUserByID(int $id): ?array {
        $sql = "SELECT * FROM users WHERE userID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    // toggle user status between Active and Inactive
    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE users SET status = ? WHERE userID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}

class SearchUserAccountEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // able to cross search with name, email, role, and status
    public function searchUsers(string $name, string $email, string $role, string $status): array {
        $sql = "SELECT userID, name, email, role, status FROM users WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($name)) {
            $sql .= " AND name LIKE ?";
            $params[] = "%{$name}%";
            $types .= "s";
        }
        if (!empty($email)) {
            $sql .= " AND email LIKE ?";
            $params[] = "%{$email}%";
            $types .= "s";
        }
        if (!empty($role)) {
            $sql .= " AND role LIKE ?";
            $params[] = "%{$role}%";
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

    public function getAllRoles(): array {
        $sql = "SELECT role FROM profiles WHERE status = 'Active'";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
