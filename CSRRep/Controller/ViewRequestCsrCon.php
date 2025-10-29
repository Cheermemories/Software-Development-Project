<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class ViewRequestCsrCon {
    private RequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestCsrEnt($conn);
    }

    public function getAllRequests(): array {
        return $this->entity->getAllRequests();
    }

    public function incrementViewCount(int $requestID): void {
        $this->entity->incrementViewCount($requestID);
    }
}
?>
