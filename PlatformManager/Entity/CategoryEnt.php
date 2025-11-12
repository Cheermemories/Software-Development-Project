<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class CategoryEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all categories
    public function fetchAllCategories(): array {
        $sql = "SELECT categoryID, name, description, status FROM categories ORDER BY categoryID ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    // function called by insertCategory to check for existing categories
    private function categoryExists(string $name): bool {
        $stmt = $this->conn->prepare("SELECT categoryID FROM categories WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // insert for Category creation
    public function insertCategory(string $name, string $description): string {
        if (empty($name) || empty($description)) {
            return "All fields are required.";
        }

        if ($this->categoryExists($name)) {
            return "A category with this name already exists.";
        }

        $status = "Active";
        $sql = "INSERT INTO categories (name, description, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $name, $description, $status);

        return $stmt->execute()
            ? "Category created successfully!"
            : "Error creating category: " . $this->conn->error;
    }


    // update for updates to category
    public function updateCategory(int $categoryID, string $name, string $description): string {
        if (empty($name) || empty($description)) {
            return "All fields are required.";
        }

        $stmt = $this->conn->prepare("
            UPDATE categories 
            SET name = ?, description = ?
            WHERE categoryID = ?
        ");
        $stmt->bind_param("ssi", $name, $description, $categoryID);

        return $stmt->execute()
            ? "Category updated successfully!"
            : "Error updating category: " . $this->conn->error;
    }


    // toggle for active and inactive for suspend
    public function toggleStatus(int $categoryID): string {
        $stmt = $this->conn->prepare("
            UPDATE categories
            SET status = CASE WHEN status = 'Active' THEN 'Inactive' ELSE 'Active' END
            WHERE categoryID = ?
        ");
        $stmt->bind_param("i", $categoryID);

        return $stmt->execute()
            ? "Category status updated successfully."
            : "Error updating category status.";
    }


    // search for searching Categories
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
