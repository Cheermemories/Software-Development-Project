<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class SearchUserAccountCon {
    private UserAccountEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserAccountEnt($conn);
    }

    // call entity to search users
    public function searchUsers(string $name, string $email, string $role, string $status): array {
        return $this->entity->searchUsers($name, $email, $role, $status);
    }

    // call entity to get roles from profile table
    public function getAllRoles(): array {
        return $this->entity->fetchAllProfiles();
    }
}
?>
