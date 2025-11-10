<?php
require_once __DIR__ . '/../../../PlatformManager/Entity/CategoryEnt.php';

class ViewCategoryCon {
    private CategoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CategoryEnt($conn);
    }

    public function getAllCategories(): array {
        return $this->entity->fetchAllCategories();
    }
}
?>
