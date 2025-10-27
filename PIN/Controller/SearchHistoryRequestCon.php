<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class SearchHistoryRequestCon {
    private SearchHistoryRequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SearchHistoryRequestEnt($conn);
    }

    public function getCompletedRequests(int $pinID): array {
        return $this->entity->fetchCompletedRequests($pinID);
    }
}
?>
