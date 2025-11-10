<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class SuspendUserProfileCon {
    private UserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserProfileEnt($conn);
    }

    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }

    public function toggleStatus(int $id): string {
        return $this->entity->toggleStatus($id);
    }
}
?>
