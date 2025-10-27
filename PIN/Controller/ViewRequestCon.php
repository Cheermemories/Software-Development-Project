<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class ViewRequestCon {
    private ViewRequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewRequestEnt($conn);
    }

    // fetching all request from entity
    public function getAllRequests(int $pinID): array {
        return $this->entity->fetchRequestsByPin($pinID);
    }
}
?>