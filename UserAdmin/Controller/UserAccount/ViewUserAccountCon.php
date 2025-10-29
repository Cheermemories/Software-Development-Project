<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class ViewUserAccountCon {
    private UserAccountEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserAccountEnt($conn);
    }

    // fetching all user data from entity
    public function getAllUsers(): array {
        return $this->entity->fetchAllUsers();
    }
}
?>