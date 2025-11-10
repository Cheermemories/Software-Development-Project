<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class ViewHistoryRequestCsrCon {
    private RequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestCsrEnt($conn);
    }

    public function getAllCompletedRequests(): array {
        return $this->entity->fetchAllCompletedRequests();
    }
}
?>
