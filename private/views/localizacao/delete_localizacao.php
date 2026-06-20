<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_data.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    
    $result = $dbManager->deleteLocalizacao($id);
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falha ao eliminar.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
