<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class CreateCategoryEnt {
    private mysqli $conn;

    private int $categoryID;
    private string $name;
    private string $description;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
        $this->status = "Active";
    }

    private function categoryExists(string $name): bool {
        $stmt = $this->conn->prepare("SELECT categoryID FROM categories WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function insertCategory(string $name, string $description): string {
        if ($this->categoryExists($name)) {
            return "A category with this name already exists.";
        }

        $sql = "INSERT INTO categories (name, description, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $name, $description, $this->status);

        if ($stmt->execute()) {
            return "Category created successfully!";
        } else {
            return "Error creating category. Please try again.";
        }
    }
}

class ViewCategoryEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function fetchAllCategories(): array {
        $sql = "SELECT categoryID, name, description, status FROM categories ORDER BY categoryID ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

class UpdateCategoryEnt {
    private mysqli $conn;

    private int $categoryID;
    private string $name;
    private string $description;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function fetchAllCategories(): array {
        $sql = "SELECT categoryID, name, description, status FROM categories ORDER BY name ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function updateCategory(int $categoryID, string $name, string $description): string {
        $stmt = $this->conn->prepare("
            UPDATE categories 
            SET name = ?, description = ? 
            WHERE categoryID = ?
        ");
        $stmt->bind_param("ssi", $name, $description, $categoryID);
        return $stmt->execute() ? "Category updated successfully!" : "Error updating category.";
    }
}

class SuspendCategoryEnt {
    private mysqli $conn;

    private int $categoryID;
    private string $name;
    private string $description;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function fetchAllCategories(): array {
        $sql = "SELECT categoryID, name, description, status FROM categories ORDER BY name ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function toggleCategoryStatus(int $categoryID): string {
        $sql = "UPDATE categories
                SET status = CASE WHEN status = 'Active' THEN 'Inactive' ELSE 'Active' END
                WHERE categoryID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $categoryID);

        if ($stmt->execute()) {
            return "Category status updated successfully.";
        } else {
            return "Error updating category status.";
        }
    }
}

class SearchCategoryEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function searchCategories(string $name, string $description, string $status): array {
        $sql = "SELECT categoryID, name, description, status FROM categories WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($name)) {
            $words = preg_split('/\s+/', trim($name));
            $regex = implode('.*', array_map(fn($w) => '[[:<:]]' . preg_quote($w, '/'), $words));
            $sql .= " AND name REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($description)) {
            $words = preg_split('/\s+/', trim($description));
            $regex = implode('.*', array_map(fn($w) => preg_quote($w, '/'), $words));
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