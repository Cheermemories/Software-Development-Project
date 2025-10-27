<?php
require_once __DIR__ . '/../../CSRRep/Entity/RequestCsrEnt.php';

class SearchRequestCsrCon {
    private SearchRequestCsrEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SearchRequestCsrEnt($conn);
    }

    public function searchRequests(string $title, string $description, string $category, string $status, string $pinName): array {
        return $this->entity->searchRequests($title, $description, $category, $status, $pinName);
    }
}
?>
