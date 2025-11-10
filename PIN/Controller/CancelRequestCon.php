<?php
require_once __DIR__ . '/../Entity/RequestEnt.php';

class CancelRequestCon {
    private RequestEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new RequestEnt($conn);
    }
    
    // fetch active requests for this PIN
    public function getActiveRequests(int $pinID): array {
        return $this->entity->fetchActiveRequests($pinID);
    }

    // cancel a request
    public function cancelRequest(int $id): string {
        return $this->entity->cancelRequest($id);
    }
}
?>

