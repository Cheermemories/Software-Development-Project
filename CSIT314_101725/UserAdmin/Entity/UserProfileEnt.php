<?php
require_once __DIR__ . '/../Database/db_connect.php';

class CreateUserProfileEnt {
    private int $profileID;
    private string $role;
    private string $permissions;
    private string $description;
    private string $status;

    private mysqli $conn;

    public function __construct($conn, $role = "", $permissions = "", $description = "", $status = "Active") {
        $this->conn = $conn;
        $this->role = $role;
        $this->permissions = $permissions;
        $this->description = $description;
        $this->status = $status;
    }

    public function getRole(): string {
        return $this->role;
    }
    public function getPermissions(): string {
        return $this->permissions;
    }
    public function getDescription(): string {
        return $this->description;
    }
    public function getStatus(): string {
        return $this->status;
    }

    public function setRole(string $role): void {
        $this->role = $role; 
    }
    public function setPermissions(string $permissions): void {
        $this->permissions = $permissions;
    }
    public function setDescription(string $description): void {
        $this->description = $description;
    }
    public function setStatus(string $status): void {
        $this->status = $status;
    }

    // check if the role already exist and retuens the status for message printing
    public function profileExists(): ?array {
        $stmt = $this->conn->prepare("SELECT status FROM profiles WHERE role = ?");
        $stmt->bind_param("s", $this->role);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    // insert the profile into the user profile table if no error
    public function insertProfile(): string {
        $existing = $this->profileExists();

        if ($existing) {
            if ($existing['status'] === 'Active') {
                return "User profile already exists.";
            } 
            elseif ($existing['status'] === 'Inactive') {
                return "User profile already exists but is currently inactive.";
            }
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO profiles (role, permissions, description, status) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $this->role, $this->permissions, $this->description, $this->status);

        if ($stmt->execute()) {
            $this->profileID = $stmt->insert_id;
            return "User profile created successfully.";
        } 
        else {
            return "Error creating user profile: " . $this->conn->error;
        }
    }
}

class ViewUserProfileEnt {
    private mysqli $conn;

    private int $profileID;
    private string $role;
    private string $permissions;
    private string $description;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all user profiles
    public function fetchAllProfiles(): array {
        $sql = "SELECT * FROM profiles";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

class UpdateUserProfileEnt {
    private mysqli $conn;

    private int $profileID;
    private string $role;
    private string $permissions;
    private string $description;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all profiles for list display
    public function fetchAllProfiles(): array {
        $sql = "SELECT * FROM profiles";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // update selected profile
    public function updateProfileByID(int $id, string $role, string $permissions, string $description): string {
        $stmt = $this->conn->prepare("
            UPDATE profiles 
            SET role = ?, permissions = ?, description = ?
            WHERE profileID = ?
        ");
        $stmt->bind_param("sssi", $role, $permissions, $description, $id);

        if ($stmt->execute()) {
            return "User profile updated successfully.";
        } else {
            return "Error updating profile: " . $this->conn->error;
        }
    }
}

class ManageUserProfileEnt {
    private mysqli $conn;

    private int $profileID;
    private string $role;
    private string $permissions;
    private string $description;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all profiles
    public function fetchAllProfiles(): array {
        $sql = "SELECT * FROM profiles";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // swaps between Active and Inactive
    public function toggleStatus(int $id): string {
        $stmt = $this->conn->prepare("SELECT * FROM profiles WHERE profileID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return "Profile not found.";
        }

        $this->profileID = $result['profileID'];
        $this->role = $result['role'];
        $this->permissions = $result['permissions'];
        $this->description = $result['description'];
        $this->status = $result['status'];

        $this->status = ($this->status === 'Active') ? 'Inactive' : 'Active';

        $update = $this->conn->prepare("UPDATE profiles SET status = ? WHERE profileID = ?");
        $update->bind_param("si", $this->status, $this->profileID);
        $update->execute();

        return "Profile status updated to {$this->status}.";
    }

    // delete profile
    public function deleteProfile(int $id): string {
        $stmt = $this->conn->prepare("DELETE FROM profiles WHERE profileID = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return "Profile deleted successfully.";
        } else {
            return "Error deleting profile: " . $this->conn->error;
        }
    }
}

class SearchUserProfileEnt {
    private mysqli $conn;

    private int $profileID;
    private string $role;
    private string $permissions;
    private string $description;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // search the database with input. allows partial and cross searches
    public function searchProfiles(string $role, string $permissions, string $description, string $status): array {
        $sql = "SELECT * FROM profiles WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($role)) {
            $words = preg_split('/\s+/', trim($role));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/') . '[[:>:]]', $words));
            $sql .= " AND role REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($permissions)) {
            $words = preg_split('/\s+/', trim($permissions));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/') . '[[:>:]]', $words));
            $sql .= " AND permissions REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($description)) {
            $words = preg_split('/\s+/', trim($description));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/') . '[[:>:]]', $words));
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