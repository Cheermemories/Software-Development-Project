<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class UpdateUserAccountCon {
    private UpdateUserAccountEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UpdateUserAccountEnt($conn);
    }

    // fetch all user accounts
    public function getAllUsers(): array {
        return $this->entity->fetchAllUsers();
    }

    // fetch all profiles for dropdown roles
    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }

    // update a specific user
    public function updateUser(int $id, string $name, string $email, string $password, string $role): string {
        return $this->entity->updateUser($id, $name, $email, $password, $role);
    }
}
?>
