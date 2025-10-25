<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class SearchRequestCon {
    private $entity;

    public function __construct() {
        $this->entity = new SearchRequestEnt();
    }

    public function getAllRequests(int $pinID): array {
        return $this->entity->getAllRequests($pinID);
    }

    public function searchRequests(int $pinID, $title, $description, $category, $status): array {
        return $this->entity->searchRequests($pinID, $title, $description, $category, $status);
    }
}
?>
