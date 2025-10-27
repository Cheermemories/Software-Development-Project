<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class SearchRequestCon {
    private SearchRequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SearchRequestEnt($conn);
    }
    
    public function searchRequests(int $pinID, $title, $description, $category, $status): array {
        return $this->entity->searchRequests($pinID, $title, $description, $category, $status);
    }
}
?>
