<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class UserAccountCon {
    private UserAccountEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserAccountEnt($conn);
    }

    // fetching all user data from entity
    public function getAllUsers(): array {
        return $this->entity->fetchAllUsers();
    }

    // fetch the user data of the id of a user
    public function getUser(int $id): ?array {
        return $this->entity->getUserByID($id);
    }
}
?>