<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/insert_db.php';
require_once '../../includes/db_data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fornecedores_lista.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$nome_empresa = trim($_POST['empresa'] ?? '');
$nif = trim($_POST['nif'] ?? '');
$tipo_fornecedor = trim($_POST['tipo'] ?? '');
$contacto = trim($_POST['contacto'] ?? '');
$email = trim($_POST['email'] ?? '');
$morada = trim($_POST['endereco'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$localidade = trim($_POST['localidade'] ?? '');
$website = trim($_POST['website'] ?? '');
$pessoa_contacto = trim($_POST['pessoa_contacto'] ?? '');
$telefone_pessoa_contacto = trim($_POST['telefone_pessoa_contacto'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');

if (empty($nome_empresa) || empty($nif) || empty($tipo_fornecedor) || empty($contacto) || empty($email)) {
    $error = 'Preencha os campos obrigatórios: Empresa, NIF, Tipo, Contacto e Email.';
    header('Location: editar_fornecedor.php?id=' . ($id > 0 ? $id : '') . '&error=' . urlencode($error));
    exit;
}

try {
    if ($id > 0) {
        $fornecedor = $dbManager->getFornecedorById($id);
        if (!$fornecedor) {
            header('Location: fornecedores_lista.php?error=' . urlencode('Fornecedor não encontrado.'));
            exit;
        }

        $saved = $db_insert->updateFornecedor(
            $id,
            $nome_empresa,
            $nif,
            $tipo_fornecedor,
            $contacto,
            $email,
            $morada,
            $codigo_postal,
            $localidade,
            $website,
            $pessoa_contacto,
            $telefone_pessoa_contacto,
            $observacoes,
            1
        );
        $message = 'Fornecedor atualizado com sucesso.';
    } else {
        $saved = $db_insert->insertFornecedor(
            $nome_empresa,
            $nif,
            $tipo_fornecedor,
            $contacto,
            $email,
            $morada,
            $codigo_postal,
            $localidade,
            $website,
            $pessoa_contacto,
            $telefone_pessoa_contacto,
            $observacoes,
            1
        );
        $message = 'Fornecedor criado com sucesso.';
    }

    if ($saved) {
        header('Location: fornecedores_lista.php?message=' . urlencode($message));
        exit;
    }

    header('Location: editar_fornecedor.php?id=' . ($id > 0 ? $id : '') . '&error=' . urlencode('Erro ao guardar fornecedor.'));
} catch (Exception $e) {
    header('Location: editar_fornecedor.php?id=' . ($id > 0 ? $id : '') . '&error=' . urlencode($e->getMessage()));
}
