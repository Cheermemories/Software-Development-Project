<?php
require_once __DIR__ . '/../../../PlatformManager/Entity/CategoryEnt.php';

class ViewCategoryCon {
    private ViewCategoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewCategoryEnt($conn);
    }

    public function getAllCategories(): array {
        return $this->entity->fetchAllCategories();
    }
}
?>
