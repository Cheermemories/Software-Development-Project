<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class ViewHistoryRequestCsrCon {
    private ViewHistoryRequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewHistoryRequestCsrEnt($conn);
    }

    public function getAllCompletedRequests(): array {
        return $this->entity->fetchAllCompletedRequests();
    }
}
?>
