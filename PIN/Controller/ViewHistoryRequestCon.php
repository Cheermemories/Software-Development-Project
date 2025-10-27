<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class ViewHistoryRequestCon {
    private ViewHistoryRequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewHistoryRequestEnt($conn);
    }

    public function getCompletedRequests(int $pinID): array {
        return $this->entity->fetchCompletedRequests($pinID);
    }
}
?>
