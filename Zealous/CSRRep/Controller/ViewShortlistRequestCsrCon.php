<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class ViewShortlistRequestCsrCon {
    private ViewShortlistRequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewShortlistRequestCsrEnt($conn);
    }

    public function getShortlistedRequests(int $csrID): array {
        return $this->entity->getShortlistedRequests($csrID);
    }

    public function addViewCount(int $requestID): void {
        $this->entity->addViewCount($requestID);
    }
}
?>
