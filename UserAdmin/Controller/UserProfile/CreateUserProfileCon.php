<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class CreateUserProfileCon {
    private UserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserProfileEnt($conn);
    }

    public function createProfile(string $role, string $permissions, string $description): string {
        return $this->entity->insertProfile($role, $permissions, $description);
    }
}
?>