<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class ViewRequestCsrCon {
    private ViewRequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewRequestCsrEnt($conn);
    }

    public function getAllRequests(): array {
        return $this->entity->getAllRequests();
    }

    public function incrementViewCount(int $requestID): void {
        $this->entity->incrementViewCount($requestID);
    }
}
?>
