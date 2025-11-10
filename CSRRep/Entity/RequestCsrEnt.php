<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class RequestCsrEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all requests
    public function getAllRequests(): array {
        $sql = "
            SELECT 
                r.requestID, r.title, r.description, r.dateCreated, r.status,
                c.name AS categoryName,
                u.name AS pinName
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            LEFT JOIN users u ON r.pinID = u.userID
            ORDER BY r.dateCreated DESC
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // increase view count when request is viewed
    public function incrementViewCount(int $requestID): void {
        $stmt = $this->conn->prepare("
            UPDATE requests 
            SET views = views + 1 
            WHERE requestID = ?
        ");
        $stmt->bind_param("i", $requestID);
        $stmt->execute();
    }

    // search for searching requests
    public function searchRequests(string $title, string $description, string $category, string $status, string $pinName): array {
        $sql = "
            SELECT 
                r.requestID, r.title, r.description, r.dateCreated, r.status,
                c.name AS categoryName,
                u.name AS pinName
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            LEFT JOIN users u ON r.pinID = u.userID
            WHERE 1=1
        ";

        $params = [];
        $types = "";

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
        if (!empty($pinName)) {
            $sql .= " AND u.name LIKE ?";
            $params[] = "%$pinName%";
            $types .= "s";
        }

        $sql .= " ORDER BY r.dateCreated DESC";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    // fetch active requests for list of request to shortlist
    public function getActiveRequests(int $csrID): array {
        $sql = "
            SELECT 
                r.requestID, r.title, r.description, r.dateCreated, r.shortlistedCount,
                c.name AS categoryName,
                EXISTS (
                    SELECT 1 FROM shortlist s 
                    WHERE s.requestID = r.requestID AND s.csrID = ?
                ) AS isShortlisted
            FROM requests r
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE r.status = 'Active'
            ORDER BY r.dateCreated DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $csrID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // add to the shortlist
    public function addToShortlist(int $csrID, int $requestID): string {
        $check = $this->conn->prepare("SELECT * FROM shortlist WHERE csrID = ? AND requestID = ?");
        $check->bind_param("ii", $csrID, $requestID);
        $check->execute();
        $res = $check->get_result();
        if ($res->num_rows > 0) return "This request is already in your shortlist.";

        $stmt = $this->conn->prepare("
            INSERT INTO shortlist (csrID, requestID, dateSaved)
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("ii", $csrID, $requestID);
        $stmt->execute();

        $update = $this->conn->prepare("
            UPDATE requests 
            SET shortlistedCount = shortlistedCount + 1 
            WHERE requestID = ?
        ");
        $update->bind_param("i", $requestID);
        $update->execute();

        return "Request added to shortlist successfully.";
    }

    // remove from the shortlist
    public function removeFromShortlist(int $csrID, int $requestID): string {
        $delete = $this->conn->prepare("
            DELETE FROM shortlist WHERE csrID = ? AND requestID = ?
        ");
        $delete->bind_param("ii", $csrID, $requestID);
        $delete->execute();

        $update = $this->conn->prepare("
            UPDATE requests 
            SET shortlistedCount = GREATEST(shortlistedCount - 1, 0)
            WHERE requestID = ?
        ");
        $update->bind_param("i", $requestID);
        $update->execute();

        return "Request removed from shortlist.";
    }

    // fetch requests that are shortlisted by the csr rep
    public function getShortlistedRequests(int $csrID): array {
        $sql = "
            SELECT 
                r.requestID, r.title, r.description, r.dateCreated, r.status,
                c.name AS categoryName, u.name AS pinName, s.dateSaved
            FROM shortlist s
            JOIN requests r ON s.requestID = r.requestID
            JOIN users u ON r.pinID = u.userID
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE s.csrID = ?
            ORDER BY s.dateSaved DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $csrID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // search for searching request within csr rep's shortlist
    public function searchShortlistedRequests(int $csrID, string $title, string $description, string $category, string $pinName): array {
        $sql = "
            SELECT 
                r.requestID, r.title, r.description, r.dateCreated,
                c.name AS categoryName, u.name AS pinName, s.dateSaved
            FROM shortlist s
            JOIN requests r ON s.requestID = r.requestID
            JOIN users u ON r.pinID = u.userID
            LEFT JOIN categories c ON r.categoryID = c.categoryID
            WHERE s.csrID = ?
        ";

        $params = [$csrID];
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
        if (!empty($pinName)) {
            $sql .= " AND u.name LIKE ?";
            $params[] = "%$pinName%";
            $types .= "s";
        }

        $sql .= " ORDER BY s.dateSaved DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    // fetch all completed requests
    public function fetchAllCompletedRequests(): array {
        $sql = "
            SELECT 
                r.requestID, r.title, r.description, r.categoryID,
                c.name AS categoryName, u.name AS pinName, m.dateCompleted
            FROM matches m
            JOIN requests r ON m.requestID = r.requestID
            JOIN users u ON r.pinID = u.userID
            JOIN categories c ON r.categoryID = c.categoryID
            WHERE m.status = 'Completed'
            ORDER BY m.dateCompleted DESC
        ";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
