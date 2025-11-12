<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class ViewHistoryRequestCon {
    private RequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestEnt($conn);
    }

    public function getCompletedRequests(int $pinID): array {
        return $this->entity->fetchCompletedRequests($pinID);
    }
}
?>
