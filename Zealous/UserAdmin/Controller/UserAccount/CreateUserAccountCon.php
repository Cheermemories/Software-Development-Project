<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class CreateUserAccountCon {
    private CreateUserAccountEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CreateUserAccountEnt($conn);
    }

    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }

    public function createUser(string $name, string $email, string $password, string $role): string {
        return $this->entity->insertUser($name, $email, $password, $role);
    }
}
?>