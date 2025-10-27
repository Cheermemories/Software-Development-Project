<?php
require_once __DIR__ . '/../../../PlatformManager/Entity/CategoryEnt.php';

class CreateCategoryCon {
    private CreateCategoryEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CreateCategoryEnt($conn);
    }

    public function createCategory(string $name, string $description): string {
        return $this->entity->insertCategory($name, $description);
    }
}
?>
