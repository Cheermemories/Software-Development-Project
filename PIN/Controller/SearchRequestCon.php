<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class SearchRequestCon {
    private RequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestEnt($conn);
    }
    
    public function searchRequests(int $pinID, $title, $description, $category, $status): array {
        return $this->entity->searchRequests($pinID, $title, $description, $category, $status);
    }
}
?>
