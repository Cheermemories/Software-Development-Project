<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class UpdateUserProfileCon {
    private UpdateUserProfileEnt $entity;

    public function __construct() {
        global $conn;
        $this->entity = new UpdateUserProfileEnt($conn);
    }

    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }

    public function updateProfile(int $id, string $role, string $permissions, string $description): string {
        return $this->entity->updateProfileByID($id, $role, $permissions, $description);
    }
}
?>
