<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class SearchShortlistRequestCsrCon {
    private SearchShortlistRequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SearchShortlistRequestCsrEnt($conn);
    }

    public function searchShortlistedRequests(
        int $csrID, string $title, string $description, string $category, string $pinName
    ): array {
        return $this->entity->searchShortlistedRequests($csrID, $title, $description, $category, $pinName);
    }
}
?>
