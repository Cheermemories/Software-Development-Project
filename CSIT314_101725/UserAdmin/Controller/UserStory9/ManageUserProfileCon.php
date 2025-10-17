<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class ManageUserProfileCon {
    private ManageUserProfileEnt $entity;

    public function __construct() {
        global $conn;
        $this->entity = new ManageUserProfileEnt($conn);
    }

    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }

    public function toggleStatus(int $id): string {
        return $this->entity->toggleStatus($id);
    }

    public function deleteProfile(int $id): string {
        return $this->entity->deleteProfile($id);
    }
}
?>
