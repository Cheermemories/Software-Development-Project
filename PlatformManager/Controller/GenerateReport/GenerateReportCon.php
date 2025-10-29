<?php
require_once __DIR__ . '/../../Entity/ReportEnt.php';

class GenerateReportCon {
    private ReportEnt $entity;

    public function __construct(mysqli $conn) {
        $this->entity = new ReportEnt($conn);
    }

    public function generateReport(string $type): array {
        return $this->entity->getReportData($type);
    }
}
?>
