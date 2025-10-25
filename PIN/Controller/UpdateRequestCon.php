<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class UpdateRequestCon {
    private UpdateRequestEnt $entity;

    public function __construct() {
        global $conn;
        $this->entity = new UpdateRequestEnt($conn);
    }

    public function getAllRequests(int $pinID): array {
        return $this->entity->fetchAllRequests($pinID);
    }

    public function getAllCategories(): array {
        return $this->entity->fetchAllCategories();
    }

    public function updateRequest(int $id, string $title, string $description, int $categoryID): string {
        return $this->entity->updateRequest($id, $title, $description, $categoryID);
    }
}
?>
