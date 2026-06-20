<?php
require_once 'auth.php';
require_once 'db_data.php';

header('Content-Type: application/json');

try {
    $dbManager = new DatabaseManager($pdo);
    $utilizadores = $dbManager->getUtilizadores();
    echo json_encode($utilizadores);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['erro' => $e->getMessage()]);
}
exit;
?>
