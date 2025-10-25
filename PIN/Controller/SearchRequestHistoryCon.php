<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class SearchRequestHistoryCon {
    private SearchRequestHistoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SearchRequestHistoryEnt($conn);
    }

    public function getCompletedRequests(int $pinID): array {
        return $this->entity->fetchCompletedRequests($pinID);
    }
}
?>
