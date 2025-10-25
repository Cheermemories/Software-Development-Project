<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class CancelRequestCon {
    private CancelRequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new CancelRequestEnt($conn);
    }
    
    // Only fetch active requests for this PIN
    public function getActiveRequests(int $pinID): array {
        return $this->entity->fetchActiveRequests($pinID);
    }

    // Cancel a request (set to Cancelled)
    public function cancelRequest(int $id): string {
        return $this->entity->cancelRequest($id);
    }
}
?>

