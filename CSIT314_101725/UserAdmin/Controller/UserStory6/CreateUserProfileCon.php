<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class CreateUserProfileCon {
    private $entity;

    public function __construct($conn) {
        $this->entity = new CreateUserProfileEnt($conn);
    }

    public function createProfile($role, $permissions, $description) {
        $this->entity->setRole($role);
        $this->entity->setPermissions($permissions);
        $this->entity->setDescription($description);
        return $this->entity->insertProfile();
    }
}
?>