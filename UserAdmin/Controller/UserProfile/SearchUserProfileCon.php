<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class SearchUserProfileCon {
    private SearchUserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SearchUserProfileEnt($conn);
    }

    public function searchProfiles(string $role, string $permissions, string $description, string $status): array {
        return $this->entity->searchProfiles($role, $permissions, $description, $status);
    }
}
?>
