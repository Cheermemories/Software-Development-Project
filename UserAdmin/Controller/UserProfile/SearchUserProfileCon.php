<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class SearchUserProfileCon {
    private UserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserProfileEnt($conn);
    }

    public function searchProfiles(string $role, string $permissions, string $description, string $status): array {
        return $this->entity->searchProfiles($role, $permissions, $description, $status);
    }
}
?>
