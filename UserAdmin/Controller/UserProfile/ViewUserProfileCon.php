<?php
require_once __DIR__ . '/../../Entity/UserProfileEnt.php';

class ViewUserProfileCon {
    private ViewUserProfileEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ViewUserProfileEnt($conn);
    }

    // fetch all profile data from entity
    public function getAllProfiles(): array {
        return $this->entity->fetchAllProfiles();
    }
}
?>
