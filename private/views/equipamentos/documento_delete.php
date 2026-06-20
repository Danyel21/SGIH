<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/file_upload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

$id_documento = intval($_POST['id_documento'] ?? 0);
$id_equipamento = intval($_POST['id_equipamento'] ?? 0);

if ($id_documento <= 0 || $id_equipamento <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetros obrigatórios em falta']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT caminho_ficheiro FROM DOCUMENTO WHERE id_documento = :id_documento AND id_equipamento = :id_equipamento');
    $stmt->execute([':id_documento' => $id_documento, ':id_equipamento' => $id_equipamento]);
    $documento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$documento) {
        http_response_code(404);
        echo json_encode(['erro' => 'Documento não encontrado']);
        exit;
    }

    $caminho = $documento['caminho_ficheiro'];
    if (!empty($caminho)) {
        $fileManager = new FileUploadManager();
        $fileManager->deleteFile($caminho);
    }

    $stmt = $pdo->prepare('DELETE FROM DOCUMENTO WHERE id_documento = :id_documento');
    $stmt->execute([':id_documento' => $id_documento]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Documento apagado com sucesso']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
    exit;
}
?>