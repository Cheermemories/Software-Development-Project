<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class SuspendUserAccountCon {
    private UserAccountEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserAccountEnt($conn);
    }

    // get all users
    public function getAllUsers(): array {
        return $this->entity->fetchAllUsers();
    }

    // toggle user account status
    public function toggleStatus(int $id): string {
        return $this->entity->toggleStatus($id);
    }
}
?>
