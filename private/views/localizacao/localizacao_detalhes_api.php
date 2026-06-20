<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/db_data.php';
require_once __DIR__ . '/../../includes/auth.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode([ 'success' => false, 'message' => 'ID de localização inválido.' ]);
    exit;
}

$localizacao = $dbManager->getLocalizacaoById($id);
if (!$localizacao) {
    echo json_encode([ 'success' => false, 'message' => 'Localização não encontrada.' ]);
    exit;
}

$equipamentos = $dbManager->getEquipamentos([ 'localizacao' => $id ]);
$totalEquipamentos = count($equipamentos);

$estados = [];
$categorias = [];
foreach ($equipamentos as $equipamento) {
    $estado = $equipamento['nome_estado'] ?? 'Desconhecido';
    $categoria = $equipamento['nome_categoria'] ?? 'Desconhecida';
    $estados[$estado] = ($estados[$estado] ?? 0) + 1;
    $categorias[$categoria] = ($categorias[$categoria] ?? 0) + 1;
}

$response = [
    'success' => true,
    'localizacao' => $localizacao,
    'total_equipamentos' => $totalEquipamentos,
    'equipamentos' => $equipamentos,
    'estados' => $estados,
    'categorias' => $categorias,
];

echo json_encode($response);
