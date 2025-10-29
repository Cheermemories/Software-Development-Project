<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class CreateRequestCon {
    private RequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestEnt($conn);
    }

    public function getCategories(): array {
        return $this->entity->fetchCategories();
    }

    public function saveRequest(int $pinID, string $title, string $description, int $categoryID): bool {
        return $this->entity->insertRequest($pinID, $title, $description, $categoryID);
    }
}
?>