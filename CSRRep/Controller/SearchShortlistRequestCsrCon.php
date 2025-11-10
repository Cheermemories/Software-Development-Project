<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class SearchShortlistRequestCsrCon {
    private RequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestCsrEnt($conn);
    }

    public function searchShortlistedRequests(
        int $csrID, string $title, string $description, string $category, string $pinName
    ): array {
        return $this->entity->searchShortlistedRequests($csrID, $title, $description, $category, $pinName);
    }
}
?>
