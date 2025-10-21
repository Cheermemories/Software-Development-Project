<?php
require_once __DIR__ . '/../Entity/PINEnt.php';

class CreateRequestCon {
    private CreateRequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CreateRequestEnt($conn);
    }

    public function getCategories(): array {
        return $this->entity->fetchCategories();
    }

    public function saveRequest(int $pinID, string $title, string $description, int $categoryID): bool {
        return $this->entity->createRequest($pinID, $title, $description, $categoryID);
    }
}
?>