<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class ViewRequestCsrEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function getAllRequests(): array {
        $sql = "
            SELECT r.requestID, r.title, r.description, r.dateCreated, r.status,
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

    // adds 1 to views
    public function incrementViewCount(int $requestID): void {
        $stmt = $this->conn->prepare("
            UPDATE requests 
            SET views = views + 1 
            WHERE requestID = ?
        ");
        $stmt->bind_param("i", $requestID);
        $stmt->execute();
        $stmt->close();
    }
}

class SearchRequestCsrEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function searchRequests(string $title, string $description, string $category, string $status, string $pinName): array {
        $sql = "
            SELECT r.requestID, r.title, r.description, r.dateCreated, r.status,
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
            $words = preg_split('/\s+/', trim($title));
            $regex = implode('.*', array_map(fn($w) => preg_quote($w, '/'), $words));
            $sql .= " AND r.title REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($description)) {
            $words = preg_split('/\s+/', trim($description));
            $regex = implode('.*', array_map(fn($w) => preg_quote($w, '/'), $words));
            $sql .= " AND r.description REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($category)) {
            $words = preg_split('/\s+/', trim($category));
            $regex = implode('.*', array_map(fn($w) => preg_quote($w, '/'), $words));
            $sql .= " AND c.name REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        if (!empty($status)) {
            $sql .= " AND r.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        if (!empty($pinName)) {
            $words = preg_split('/\s+/', trim($pinName));
            $regex = implode('.*', array_map(fn($w) => preg_quote($w, '/'), $words));
            $sql .= " AND u.name REGEXP ?";
            $params[] = $regex;
            $types .= "s";
        }

        $sql .= " ORDER BY r.dateCreated DESC";

        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

class ShortlistRequestCsrEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // geth active requests from request table
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

    // add a request to the shortlist table
    public function addToShortlist(int $csrID, int $requestID): string {
        // check if already shortlisted
        $check = $this->conn->prepare("SELECT * FROM shortlist WHERE csrID = ? AND requestID = ?");
        $check->bind_param("ii", $csrID, $requestID);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            return "This request is already in your shortlist.";
        }

        // insert into shortlist table
        $stmt = $this->conn->prepare("
            INSERT INTO shortlist (csrID, requestID, dateSaved)
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("ii", $csrID, $requestID);
        $stmt->execute();

        // add 1 to shortlistedCount
        $update = $this->conn->prepare("
            UPDATE requests 
            SET shortlistedCount = shortlistedCount + 1 
            WHERE requestID = ?
        ");
        $update->bind_param("i", $requestID);
        $update->execute();

        return "Request added to shortlist successfully.";
    }

    // remove a request from the shortlist table
    public function removeFromShortlist(int $csrID, int $requestID): string {
        $delete = $this->conn->prepare("
            DELETE FROM shortlist
            WHERE csrID = ? AND requestID = ?
        ");
        $delete->bind_param("ii", $csrID, $requestID);
        $delete->execute();

        // minus 1 to shortlistedCount
        $update = $this->conn->prepare("
            UPDATE requests 
            SET shortlistedCount = GREATEST(shortlistedCount - 1, 0)
            WHERE requestID = ?
        ");
        $update->bind_param("i", $requestID);
        $update->execute();

        return "Request removed from shortlist.";
    }
}

class ViewShortlistRequestCsrEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // get all shortlisted requests from this CSR Rep
    public function getShortlistedRequests(int $csrID): array {
        $sql = "
            SELECT 
                r.requestID,
                r.title,
                r.description,
                r.dateCreated,
                r.status,
                c.name AS categoryName,
                u.name AS pinName,
                s.dateSaved
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

    // add to the value of view
    public function addViewCount(int $requestID): void {
        $sql = "UPDATE requests SET views = views + 1 WHERE requestID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $requestID);
        $stmt->execute();
    }
}

class SearchShortlistRequestCsrEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function searchShortlistedRequests(int $csrID, string $title, string $description, string $category, string $pinName): array {
        $sql = "
            SELECT 
                r.requestID,
                r.title,
                r.description,
                r.dateCreated,
                c.name AS categoryName,
                u.name AS pinName,
                s.dateSaved
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
}

class ViewHistoryRequestCsrEnt {
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

    public function fetchAllCompletedRequests(): array {
        $sql = "
            SELECT 
                r.requestID,
                r.title,
                r.description,
                c.categoryID,
                c.name AS categoryName,
                u.name AS pinName,
                m.dateCompleted
            FROM matches m
            JOIN requests r ON m.requestID = r.requestID
            JOIN categories c ON r.categoryID = c.categoryID
            JOIN users u ON r.pinID = u.userID
            WHERE m.status = 'Completed'
            ORDER BY m.dateCompleted DESC
        ";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

class SearchHistoryRequestCsrEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // fetch all completed matches across all users
    public function fetchAllCompletedRequests(): array {
        $sql = "
            SELECT 
                r.requestID,
                r.title,
                r.description,
                r.categoryID,
                c.name AS categoryName,
                u.name AS pinName,
                m.dateCompleted
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