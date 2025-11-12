<?php
require_once __DIR__ . '/../../Database/db_connect.php';

class ReportEnt {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function getReportData(string $type): array {
        $today = date('Y-m-d');
        $start = $today;
        $end = $today;

        // date range for report type to generate
        if ($type === 'weekly') {
            $start = date('Y-m-d', strtotime('monday this week'));
            $end = date('Y-m-d', strtotime('sunday this week'));
        } 
        elseif ($type === 'monthly') {
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }

        // for request created
        $sqlCreated = "
            SELECT 
                r.title, 
                r.description, 
                r.dateCreated, 
                r.status, 
                u.name AS pinName
            FROM requests r
            JOIN users u ON r.pinID = u.userID
            WHERE r.dateCreated BETWEEN ? AND ?
            ORDER BY r.dateCreated DESC, r.title ASC, u.name ASC
        ";
        $stmt1 = $this->conn->prepare($sqlCreated);
        $stmt1->bind_param("ss", $start, $end);
        $stmt1->execute();
        $created = $stmt1->get_result()->fetch_all(MYSQLI_ASSOC);

        // for request that are active
        $sqlActive = "
            SELECT 
                r.title, 
                r.description, 
                r.dateCreated, 
                u.name AS pinName
            FROM requests r
            JOIN users u ON r.pinID = u.userID
            WHERE r.status = 'Active' AND r.dateCreated BETWEEN ? AND ?
            ORDER BY r.dateCreated DESC, r.title ASC, u.name ASC
        ";
        $stmt2 = $this->conn->prepare($sqlActive);
        $stmt2->bind_param("ss", $start, $end);
        $stmt2->execute();
        $active = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        // for request matched
        $sqlMatched = "
            SELECT 
                r.title,
                r.description,
                r.dateCreated,
                m.dateMatched,
                u.name AS pinName,
                csr.name AS csrName
            FROM matches m
            JOIN requests r ON m.requestID = r.requestID
            JOIN users u ON r.pinID = u.userID
            JOIN users csr ON m.csrID = csr.userID
            WHERE m.dateMatched BETWEEN ? AND ?
            ORDER BY m.dateMatched DESC, r.title ASC, u.name ASC, csr.name ASC
        ";
        $stmt3 = $this->conn->prepare($sqlMatched);
        $stmt3->bind_param("ss", $start, $end);
        $stmt3->execute();
        $matched = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);

        // for request completed
        $sqlCompleted = "
            SELECT 
                r.title,
                r.description,
                r.dateCreated,
                m.dateMatched,
                m.dateCompleted,
                u.name AS pinName,
                csr.name AS csrName
            FROM matches m
            JOIN requests r ON m.requestID = r.requestID
            JOIN users u ON r.pinID = u.userID
            JOIN users csr ON m.csrID = csr.userID
            WHERE m.dateCompleted BETWEEN ? AND ?
            ORDER BY m.dateCompleted DESC, r.title ASC, u.name ASC, csr.name ASC
        ";
        $stmt4 = $this->conn->prepare($sqlCompleted);
        $stmt4->bind_param("ss", $start, $end);
        $stmt4->execute();
        $completed = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);

        // for request cancelled
        $sqlCancelled = "
            SELECT 
                r.title, 
                r.description, 
                r.dateCreated, 
                u.name AS pinName
            FROM requests r
            JOIN users u ON r.pinID = u.userID
            WHERE r.status = 'Cancelled' AND r.dateCreated BETWEEN ? AND ?
            ORDER BY r.title ASC, u.name ASC
        ";
        $stmt5 = $this->conn->prepare($sqlCancelled);
        $stmt5->bind_param("ss", $start, $end);
        $stmt5->execute();
        $cancelled = $stmt5->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'created' => $created,
            'active' => $active,
            'matched' => $matched,
            'completed' => $completed,
            'cancelled' => $cancelled
        ];
    }
}
?>