<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class ShortlistRequestCsrCon {
    private ShortlistRequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ShortlistRequestCsrEnt($conn);
    }

    public function getActiveRequests(int $csrID): array {
        return $this->entity->getActiveRequests($csrID);
    }

    public function addToShortlist(int $csrID, int $requestID): string {
        return $this->entity->addToShortlist($csrID, $requestID);
    }

    public function removeFromShortlist(int $csrID, int $requestID): string {
        return $this->entity->removeFromShortlist($csrID, $requestID);
    }
}
?>
