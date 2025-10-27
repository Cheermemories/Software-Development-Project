<?php
require_once __DIR__ . '/../../../PlatformManager/Entity/CategoryEnt.php';

class SuspendCategoryCon {
    private SuspendCategoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SuspendCategoryEnt($conn);
    }

    public function getAllCategories(): array {
        return $this->entity->fetchAllCategories();
    }

    public function toggleStatus(int $categoryID): string {
        return $this->entity->toggleCategoryStatus($categoryID);
    }
}
?>
