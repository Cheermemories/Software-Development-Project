<?php
require_once __DIR__ . '/../Entity/PINEnt.php';

class ViewRequestCon {
    private ViewRequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewRequestEnt($conn);
    }

    // fetching all request from entity
    public function getAllRequests(int $pinID): array {
        return $this->entity->fetchRequestsByPin($pinID);
    }

    // fetch the user data of the id of a user
    public function getRequest(int $id): ?array {
        return $this->entity->getRequestByID($id);
    }
}
?>