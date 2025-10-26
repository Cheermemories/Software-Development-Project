<?php
require_once __DIR__ . '/../Entity/LoginEnt.php';

class LoginCon {
    private LoginEnt $entity;

    public function __construct() {
        $this->entity = new LoginEnt();
    }

    public function authenticateUser(string $email, string $password): ?array {
        return $this->entity->verifyCredentials($email, $password);
    }
}
?>
