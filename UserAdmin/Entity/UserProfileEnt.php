<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class UserProfileEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all user profiles
    public function fetchAllProfiles(): array {
        $sql = "SELECT * FROM profiles ORDER BY profileID ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // insert profile for profile creation
    public function insertProfile(string $role, string $permissions, string $description): string {
        if (empty($role) || empty($permissions) || empty($description)) {
            return "All fields are required.";
        }

        $stmt = $this->conn->prepare("SELECT status FROM profiles WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            if ($result['status'] === 'Active') {
                return "Profile with this role already exists and is active.";
            } elseif ($result['status'] === 'Inactive') {
                return "Profile with this role exists but is inactive.";
            }
        }

        $status = "Active";
        $stmt = $this->conn->prepare("
            INSERT INTO profiles (role, permissions, description, status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $role, $permissions, $description, $status);

        return $stmt->execute()
            ? "User profile created successfully."
            : "Error creating user profile: " . $this->conn->error;
    }


    // update for profile updates
    public function updateProfileByID(int $id, string $role, string $permissions, string $description): string {
        if (empty($role) || empty($permissions) || empty($description)) {
            return "All fields are required.";
        }

        $stmt = $this->conn->prepare("
            UPDATE profiles
            SET role = ?, permissions = ?, description = ?
            WHERE profileID = ?
        ");
        $stmt->bind_param("sssi", $role, $permissions, $description, $id);

        return $stmt->execute()
            ? "User profile updated successfully."
            : "Error updating profile: " . $this->conn->error;
    }

    // toggle for status being active or inactive
    public function toggleStatus(int $id): string {
        $stmt = $this->conn->prepare("SELECT status FROM profiles WHERE profileID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return "Profile not found.";
        }

        $newStatus = ($result['status'] === 'Active') ? 'Inactive' : 'Active';

        $update = $this->conn->prepare("UPDATE profiles SET status = ? WHERE profileID = ?");
        $update->bind_param("si", $newStatus, $id);
        $update->execute();

        return "Profile status updated to {$newStatus}.";
    }


    // search for searching profiles
    public function searchProfiles(string $role, string $permissions, string $description, string $status): array {
        $sql = "SELECT * FROM profiles WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($role)) {
            $words = preg_split('/\s+/', trim($role));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/'), $words));
            $sql .= " AND role REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($permissions)) {
            $words = preg_split('/\s+/', trim($permissions));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/'), $words));
            $sql .= " AND permissions REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($description)) {
            $words = preg_split('/\s+/', trim($description));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/'), $words));
            $sql .= " AND description REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
