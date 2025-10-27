<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class SearchHistoryRequestCsrCon {
    private SearchHistoryRequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SearchHistoryRequestCsrEnt($conn);
    }

    public function getAllCompletedRequests(): array {
        return $this->entity->fetchAllCompletedRequests();
    }
}
?>
