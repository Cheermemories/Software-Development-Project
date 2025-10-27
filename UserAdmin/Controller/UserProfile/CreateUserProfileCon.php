<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class CreateUserProfileCon {
    private CreateUserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CreateUserProfileEnt($conn);
    }

    public function createProfile(string $role, string $permissions, string $description): string {
        return $this->entity->insertProfile($role, $permissions, $description);
    }
}
?>