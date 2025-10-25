<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class CreateUserAccountCon {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function createUser(string $name, string $email, string $password, string $role): string {
        $entity = new CreateUserAccountEnt($this->conn);
        return $entity->insertUser($name, $email, $password, $role);
    }
}
?>