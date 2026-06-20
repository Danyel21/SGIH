<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/insert_db.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: utilizador_lista.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: utilizador_lista.php?error=' . urlencode('ID de utilizador inválido.'));
    exit;
}

if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $id) {
    header('Location: utilizador_lista.php?error=' . urlencode('Não pode desativar o seu próprio utilizador.'));
    exit;
}

try {
    $deactivated = $db_insert->deactivateUtilizador($id);
    if ($deactivated) {
        header('Location: utilizador_lista.php?message=' . urlencode('Utilizador desativado com sucesso.'));
        exit;
    }
    header('Location: utilizador_lista.php?error=' . urlencode('Falha ao desativar o utilizador.'));
} catch (Exception $e) {
    header('Location: utilizador_lista.php?error=' . urlencode($e->getMessage()));
}
