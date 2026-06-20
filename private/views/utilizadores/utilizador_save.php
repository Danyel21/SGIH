<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: utilizador_lista.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$funcao = trim($_POST['funcao'] ?? '');
$departamento = trim($_POST['departamento'] ?? '');
$password = trim($_POST['password'] ?? '');
$ativo = intval($_POST['ativo'] ?? 1);

if (empty($nome) || empty($email) || empty($funcao) || empty($departamento) || (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
    $error = 'Preencha correctamente todos os campos obrigatórios.';
    $redirect = $id > 0 ? 'utilizador_form.php?id=' . $id : 'utilizador_form.php';
    header('Location: ' . $redirect . '&error=' . urlencode($error));
    exit;
}

try {
    $existingStmt = $pdo->prepare('SELECT id_utilizador FROM UTILIZADOR WHERE email = :email' . ($id > 0 ? ' AND id_utilizador != :id' : ''));
    $existingStmt->bindValue(':email', $email, PDO::PARAM_STR);
    if ($id > 0) {
        $existingStmt->bindValue(':id', $id, PDO::PARAM_INT);
    }
    $existingStmt->execute();

    if ($existingStmt->fetch()) {
        $error = 'Já existe um utilizador com este email.';
        $redirect = $id > 0 ? 'utilizador_form.php?id=' . $id : 'utilizador_form.php';
        header('Location: ' . $redirect . '&error=' . urlencode($error));
        exit;
    }

    if ($id > 0) {
        $password_hash = null;
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
        }

        $saved = $db_insert->updateUtilizador($id, $nome, $email, $funcao, $departamento, $password_hash, $ativo);
        $message = 'Utilizador atualizado com sucesso!';
    } else {
        if (empty($password)) {
            $error = 'A palavra-passe é obrigatória para registar um novo utilizador.';
            header('Location: utilizador_form.php?error=' . urlencode($error));
            exit;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $saved = $db_insert->insertUtilizador($nome, $email, $funcao, $departamento, $password_hash, $ativo);
        $message = 'Utilizador registado com sucesso!';
    }

    if ($saved) {
        header('Location: utilizador_lista.php?message=' . urlencode($message));
        exit;
    }

    throw new Exception('Erro ao guardar utilizador.');
} catch (Exception $e) {
    $redirect = $id > 0 ? 'utilizador_form.php?id=' . $id : 'utilizador_form.php';
    header('Location: ' . $redirect . '&error=' . urlencode($e->getMessage()));
}
