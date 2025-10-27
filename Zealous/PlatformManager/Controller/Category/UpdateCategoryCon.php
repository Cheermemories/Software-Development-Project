<?php
require_once __DIR__ . '/../../../PlatformManager/Entity/CategoryEnt.php';

class UpdateCategoryCon {
    private UpdateCategoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UpdateCategoryEnt($conn);
    }

    public function getAllCategories(): array {
        return $this->entity->fetchAllCategories();
    }

    public function updateCategory(int $categoryID, string $name, string $description): string {
        return $this->entity->updateCategory($categoryID, $name, $description);
    }
}
?>


