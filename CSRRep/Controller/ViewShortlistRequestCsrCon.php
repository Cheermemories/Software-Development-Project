<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class ViewShortlistRequestCsrCon {
    private RequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestCsrEnt($conn);
    }

    public function getShortlistedRequests(int $csrID): array {
        return $this->entity->getShortlistedRequests($csrID);
    }

    public function incrementViewCount(int $requestID): void {
        $this->entity->incrementViewCount($requestID);
    }
}
?>
