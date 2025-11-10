<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class ViewUserProfileCon {
    private UserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new UserProfileEnt($conn);
    }

    // fetch all profile data from entity
    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }
}
?>
