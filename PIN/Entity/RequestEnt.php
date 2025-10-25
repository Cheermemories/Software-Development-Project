<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class SearchRequestEnt {
    private mysqli $conn;

    private int $requestID;
    private int $pinID;
    private string $title;
    private string $description;
    private int $categoryID;
    private string $dateCreated;
    private string $status;
    private int $views;
    private int $shortlistedCount;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // pulls data of requests from PIN to make category dropdown
    public function getAllRequests(int $pinID): array {
        $sql = "
            SELECT r.requestID, r.title, r.description, r.dateCreated, r.status,
                r.categoryID, c.name AS categoryName
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

    // search the database with input. allows partial and cross searches
    public function searchRequests(int $pinID, $title, $description, $category, $status): array {
        $sql = "
            SELECT r.*, c.name AS categoryName
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE r.pinID = ?
        ";

        $params = [$pinID];
        $types = "i";

        if (!empty($title)) {
            $sql .= " AND r.title LIKE ?";
            $params[] = "%$title%";
            $types .= "s";
        }
        if (!empty($description)) {
            $sql .= " AND r.description LIKE ?";
            $params[] = "%$description%";
            $types .= "s";
        }
        if (!empty($category)) {
            $sql .= " AND c.name LIKE ?";
            $params[] = "%$category%";
            $types .= "s";
        }
        if (!empty($status)) {
            $sql .= " AND r.status = ?";
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

class ViewRequestEnt {
    private mysqli $conn;

    private int $requestID;
    private int $pinID;
    private string $title;
    private string $description;
    private int $categoryID;
    private string $dateCreated;
    private string $status;
    private int $views;
    private int $shortlistedCount;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetching user account data
    public function fetchRequestsByPin(int $pinID): array {
    $sql = "
        SELECT r.*, c.name AS categoryName
        FROM requests r
        LEFT JOIN categories c ON r.categoryID = c.categoryID
        WHERE r.pinID = ?
    ";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $pinID);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

class CancelRequestEnt {
    private mysqli $conn;

    private int $requestID;
    private int $pinID;
    private string $title;
    private string $description;
    private int $categoryID;
    private string $dateCreated;
    private string $status;
    private int $views;
    private int $shortlistedCount;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get active requests for this PIN
    public function fetchActiveRequests(int $pinID): array {
        $stmt = $this->conn->prepare("SELECT * FROM requests WHERE pinID = ? AND status = 'Active'");
        $stmt->bind_param("i", $pinID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // changes the status to Cancelled
    public function cancelRequest(int $id): string {
        $stmt = $this->conn->prepare("UPDATE requests SET status = 'Cancelled' WHERE requestID = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return "Request successfully cancelled.";
        } else {
            return "Error cancelling request: " . $this->conn->error;
        }
    }
}

class CreateRequestEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get all categories for dropdown box
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

    // insert new request into database
    public function createRequest(int $pinID, string $title, string $description, int $categoryID): bool {
        $sql = "INSERT INTO requests (pinID, title, description, categoryID, dateCreated, status, views, shortlistedCount)
                VALUES (?, ?, ?, ?, NOW(), 'Active', 0, 0)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issi", $pinID, $title, $description, $categoryID);
        return $stmt->execute();
    }
}

class UpdateRequestEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get all requests from this PIN
    public function fetchAllRequests(int $pinID): array {
        $sql = "
            SELECT r.*, c.name AS categoryName
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE r.pinID = ? AND r.status = 'Active'
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $pinID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // get all active categories
    public function fetchAllCategories(): array {
        $result = $this->conn->query("SELECT * FROM categories WHERE status = 'Active'");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // update request details in the database
    public function updateRequest(int $id, string $title, string $description, int $categoryID): string {
        $stmt = $this->conn->prepare("UPDATE requests SET title = ?, description = ?, categoryID = ? WHERE requestID = ?");
        $stmt->bind_param("ssii", $title, $description, $categoryID, $id);

        if ($stmt->execute()) {
            return "Request updated successfully.";
        } else {
            return "Error updating request: " . $this->conn->error;
        }
    }
}

class ViewRequestHistoryEnt {
    private mysqli $conn;

    private int $requestID;
    private int $pinID;
    private string $title;
    private string $description;
    private int $categoryID;
    private string $dateCreated;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get all completed requests from this PIN
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
}

class SearchRequestHistoryEnt {
    private mysqli $conn;

    private int $requestID;
    private int $pinID;
    private string $title;
    private string $description;
    private int $categoryID;
    private string $dateCreated;
    private string $status;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get all completed requests from this PIN
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
}
?>
