<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class SuspendUserProfileCon {
    private SuspendUserProfileEnt $entity;

    public function __construct() {
        global $conn;
        $this->entity = new SuspendUserProfileEnt($conn);
    }

    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }

    public function toggleStatus(int $id): string {
        return $this->entity->toggleStatus($id);
    }
}
?>
