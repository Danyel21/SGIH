<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fornecedores_lista.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: fornecedores_lista.php?error=' . urlencode('ID de fornecedor inválido.'));
    exit;
}

try {
    $deleted = $dbManager->deactivateFornecedor($id);
    if ($deleted) {
        header('Location: fornecedores_lista.php?message=' . urlencode('Fornecedor eliminado com sucesso.'));
        exit;
    }
    header('Location: fornecedores_lista.php?error=' . urlencode('Erro ao eliminar fornecedor.'));
} catch (Exception $e) {
    header('Location: fornecedores_lista.php?error=' . urlencode($e->getMessage()));
}
