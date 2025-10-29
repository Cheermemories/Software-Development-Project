<?php
require_once __DIR__ . '/../../../PlatformManager/Entity/CategoryEnt.php';

class SearchCategoryCon {
    private CategoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CategoryEnt($conn);
    }

    public function searchCategories(string $name, string $description, string $status): array {
        return $this->entity->searchCategories($name, $description, $status);
    }
}
?>
