<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class UpdateUserProfileCon {
    private UserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserProfileEnt($conn);
    }

    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }

    public function updateProfile(int $id, string $role, string $permissions, string $description): string {
        return $this->entity->updateProfileByID($id, $role, $permissions, $description);
    }
}
?>
