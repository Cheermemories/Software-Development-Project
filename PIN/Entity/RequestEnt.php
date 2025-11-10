<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class RequestEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all requests by a PIN
    public function fetchRequests(int $pinID): array {
        $sql = "
            SELECT r.*, c.name AS categoryName
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE r.pinID = ?
            ORDER BY r.dateCreated DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $pinID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // fetch active categories for dropdown
    public function fetchCategories(): array {
        $sql = "
            SELECT categoryID, name 
            FROM categories 
            WHERE status = 'Active'
            ORDER BY name ASC
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // insert for Request creation
    public function insertRequest(int $pinID, string $title, string $description, int $categoryID): bool {
        $sql = "
            INSERT INTO requests (pinID, title, description, categoryID, dateCreated, status, views, shortlistedCount)
            VALUES (?, ?, ?, ?, NOW(), 'Active', 0, 0)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issi", $pinID, $title, $description, $categoryID);
        return $stmt->execute();
    }

    // fetch active requests from this PIN
    public function fetchActiveRequests(int $pinID): array {
        $sql = "
            SELECT r.*, c.name AS categoryName
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE r.pinID = ? AND r.status = 'Active'
            ORDER BY r.dateCreated DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $pinID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // update for updating requests
    public function updateRequest(int $id, string $title, string $description, int $categoryID): string {
        $stmt = $this->conn->prepare("
            UPDATE requests 
            SET title = ?, description = ?, categoryID = ?
            WHERE requestID = ?
        ");
        $stmt->bind_param("ssii", $title, $description, $categoryID, $id);
        return $stmt->execute()
            ? "Request updated successfully."
            : "Error updating request: " . $this->conn->error;
    }


    // cancel for "suspending" request
    public function cancelRequest(int $id): string {
        $stmt = $this->conn->prepare("UPDATE requests SET status = 'Cancelled' WHERE requestID = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute()
            ? "Request successfully cancelled."
            : "Error cancelling request: " . $this->conn->error;
    }


    // search for searching request from this PIN
    public function searchRequests(int $pinID, string $title, string $description, string $category, string $status): array {
        $sql = "
            SELECT r.*, c.name AS categoryName
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE r.pinID = ?
        ";

        $params = [$pinID];
        $types = "i";

        if (!empty($title)) {
            $regex = $this->buildRegex($title);
            $sql .= " AND r.title REGEXP ?";
            $params[] = $regex; $types .= "s";
        }

        if (!empty($description)) {
            $regex = $this->buildRegex($description);
            $sql .= " AND r.description REGEXP ?";
            $params[] = $regex; $types .= "s";
        }

        if (!empty($category)) {
            $regex = $this->buildRegex($category);
            $sql .= " AND c.name REGEXP ?";
            $params[] = $regex; $types .= "s";
        }

        if (!empty($status)) {
            $sql .= " AND r.status = ?";
            $params[] = $status; $types .= "s";
        }

        $sql .= " ORDER BY r.dateCreated DESC";
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    // fetch all completed requests from this PIN for history
    public function fetchCompletedRequests(int $pinID): array {
        $sql = "
            SELECT 
                r.requestID,
                r.title,
                r.description,
                c.categoryID,
                c.name AS categoryName,
                m.dateCompleted
            FROM matches m
            JOIN requests r ON m.requestID = r.requestID
            JOIN categories c ON r.categoryID = c.categoryID
            WHERE m.pinID = ? AND m.status = 'Completed'
            ORDER BY m.dateCompleted DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $pinID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // regxp for search flexibility
    private function buildRegex(string $input): string {
        $words = preg_split('/\s+/', trim($input));
        return implode('.*', array_map(fn($w) => preg_quote($w, '/'), $words));
    }
}
?>
