<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class ViewRequestHistoryCon {
    private ViewRequestHistoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewRequestHistoryEnt($conn);
    }

    public function getCompletedRequests(int $pinID): array {
        return $this->entity->fetchCompletedRequests($pinID);
    }
}
?>
