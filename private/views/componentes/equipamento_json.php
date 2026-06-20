<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_data.php';

header('Content-Type: application/json; charset=utf-8');

$id_comp = isset($_GET['id_comp']) ? intval($_GET['id_comp']) : 0;
if ($id_comp <= 0) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

// Precisamos do equipamento associado ao componente
// obter componente
$componente = $dbManager->getComponenteById($id_comp);
if (empty($componente)) {
    echo json_encode(['error' => 'Componente não encontrado']);
    exit;
}

$id_equip = $componente['id_equipamento_principal'] ?? 0;
if ($id_equip <= 0) {
    echo json_encode(['error' => 'Equipamento não associado']);
    exit;
}

$equip = $dbManager->getEquipamentoByCodigo($id_equip);
if (empty($equip)) {
    echo json_encode(['error' => 'Equipamento não encontrado']);
    exit;
}

// Retorna campos úteis
$out = [
    'id_equipamento' => $equip['id_equipamento'] ?? null,
    'designacao' => $equip['designacao'] ?? null,
    'codigo_interno' => $equip['codigo_interno'] ?? null,
    'marca' => $equip['marca'] ?? null,
    'modelo' => $equip['modelo'] ?? null,
    'localizacao_servico' => $equip['localizacao_servico'] ?? null,
    'localizacao_sala' => $equip['localizacao_sala'] ?? null
];

echo json_encode($out);
