<?php
require_once __DIR__ . '/../../Entity/UserAccountEnt.php';

class SuspendUserAccountCon {
    private SuspendUserAccountEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new SuspendUserAccountEnt($conn);
    }

    // get all users
    public function getAllUsers(): array {
        return $this->entity->fetchAllUsers();
    }

    // toggle user account status
    public function toggleStatus(int $id): string {
        $current = $this->entity->getUserByID($id);

        if (!$current) {
            return "User not found.";
        }

        $newStatus = ($current['status'] === 'Active') ? 'Inactive' : 'Active';
        $result = $this->entity->updateStatus($id, $newStatus);

        return $result ? "User status updated to {$newStatus}." : "Error updating user status.";
    }
}
?>
