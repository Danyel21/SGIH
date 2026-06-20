<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/insert_db.php';
require_once '../../includes/db_data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: localizacoes_lista.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$edificio = trim($_POST['edificio'] ?? '');
$piso = trim($_POST['piso'] ?? '');
$servico_departamento = trim($_POST['departamento'] ?? '');
$sala_gabinete = trim($_POST['sala'] ?? '');

if (empty($edificio) || empty($piso) || empty($servico_departamento) || empty($sala_gabinete)) {
    $error = 'Preencha todos os campos obrigatórios: edifício, piso, departamento e sala.';
    header('Location: localizacao_edit.php?id=' . ($id > 0 ? $id : '') . '&error=' . urlencode($error));
    exit;
}

try {
    if ($id > 0) {
        $localizacao = $dbManager->getLocalizacaoById($id);
        if (!$localizacao) {
            header('Location: localizacoes_lista.php?error=' . urlencode('Localização não encontrada.'));
            exit;
        }

        $saved = $db_insert->updateLocalizacao($id, $edificio, $piso, $servico_departamento, $sala_gabinete);
        $message = 'Localização atualizada com sucesso!';
    } else {
        $saved = $db_insert->insertLocalizacao($edificio, $piso, $servico_departamento, $sala_gabinete);
        $message = 'Localização criada com sucesso!';
    }

    if ($saved) {
        header('Location: localizacoes_lista.php?message=' . urlencode($message));
        exit;
    }

    header('Location: localizacoes_lista.php?error=' . urlencode('Erro ao salvar localização.'));
} catch (Exception $e) {
    header('Location: localizacao_edit.php?id=' . ($id > 0 ? $id : '') . '&error=' . urlencode($e->getMessage()));
}
