<?php
require_once __DIR__ . '/../../../PlatformManager/Entity/CategoryEnt.php';

class CreateCategoryCon {
    private CategoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CategoryEnt($conn);
    }

    public function createCategory(string $name, string $description): string {
        return $this->entity->insertCategory($name, $description);
    }
}
?>
