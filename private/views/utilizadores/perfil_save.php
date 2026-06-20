<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';

requireAuthentication();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($nome) || empty($email) || (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
    $error = 'Preencha correctamente o nome e email.';
    header('Location: perfil.php?error=' . urlencode($error));
    exit;
}

$userId = getAuthenticatedUserId();
if ($id !== $userId) {
    header('Location: perfil.php?error=' . urlencode('Acesso não autorizado.'));
    exit;
}

try {
    $existingStmt = $pdo->prepare('SELECT id_utilizador FROM UTILIZADOR WHERE email = :email AND id_utilizador != :id');
    $existingStmt->execute([
        ':email' => $email,
        ':id' => $id
    ]);

    if ($existingStmt->fetch()) {
        header('Location: perfil.php?error=' . urlencode('Já existe um utilizador com este email.'));
        exit;
    }

    $currentUser = $dbManager->getUtilizadorById($id);
    if (!$currentUser) {
        header('Location: perfil.php?error=' . urlencode('Utilizador não encontrado.'));
        exit;
    }

    $password_hash = null;
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    $saved = $db_insert->updateUtilizador(
        $id,
        $nome,
        $email,
        $currentUser['funcao'],
        $password_hash,
        $currentUser['ativo']
    );

    if ($saved) {
        $_SESSION['username'] = $nome;
        $_SESSION['email'] = $email;
        header('Location: perfil.php?message=' . urlencode('Perfil atualizado com sucesso!'));
        exit;
    }

    throw new Exception('Erro ao atualizar perfil.');
} catch (Exception $e) {
    header('Location: perfil.php?error=' . urlencode($e->getMessage()));
    exit;
}
