<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';
require_once '../../includes/file_upload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

if (!isset($_POST['id_equipamento']) || !isset($_POST['tipo_documento'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetros obrigatórios em falta']);
    exit;
}

$id_equipamento = intval($_POST['id_equipamento']);
$tipo_documento = intval($_POST['tipo_documento']);

// Validar se equipamento existe
$stmt = $pdo->prepare("SELECT id_equipamento FROM EQUIPAMENTO WHERE id_equipamento = :id");
$stmt->execute([':id' => $id_equipamento]);
if (!$stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['erro' => 'Equipamento não encontrado']);
    exit;
}

// Validar se tipo de documento existe
$stmt = $pdo->prepare("SELECT id_tipo_documento FROM TIPO_DOCUMENTO WHERE id_tipo_documento = :id");
$stmt->execute([':id' => $tipo_documento]);
if (!$stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['erro' => 'Tipo de documento não encontrado']);
    exit;
}

// obter nome do tipo para mapear pasta
$stmt = $pdo->prepare("SELECT nome_tipo FROM TIPO_DOCUMENTO WHERE id_tipo_documento = :id");
$stmt->execute([':id' => $tipo_documento]);
$tipoRow = $stmt->fetch(PDO::FETCH_ASSOC);
$tipo_nome = mb_strtolower($tipoRow['nome_tipo'] ?? '');

try {
    if (!isset($_FILES['ficheiro'])) {
        throw new Exception('Ficheiro não enviado');
    }

    // obrigar PDF conforme pedido
    $ext = strtolower(pathinfo($_FILES['ficheiro']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        throw new Exception('Apenas ficheiros PDF são permitidos para documentação.');
    }

    $fileManager = new FileUploadManager();

    // obter codigo_interno do equipamento
    $stmt = $pdo->prepare("SELECT codigo_interno FROM EQUIPAMENTO WHERE id_equipamento = :id");
    $stmt->execute([':id' => $id_equipamento]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $codigo_interno = $row['codigo_interno'] ?? null;
    if (empty($codigo_interno)) {
        throw new Exception('Código interno do equipamento não encontrado.');
    }

    // pasta alvo por tipo - melhorar correspondência normalizando nomes
    $mapping = [
        'certificado de calibracao' => 'certificado_calibracao',
        'certificado de garantia' => 'certificado_garantia',
        'manual tecnico' => 'manual_utilizador',
        'manual de utilizador' => 'manual_utilizador',
        'manual de servico' => 'manual_servico',
        'contrato de manutencao' => 'contrato_manutencao',
        'fatura' => 'fatura_aquisicao',
        'guia de remessa' => 'fatura_aquisicao',
        'guia de aquisicao' => 'fatura_aquisicao',
        'declaracao de conformidade' => 'declaracao_conformidade',
        'relatorio tecnico' => 'relatorio_tecnico',
    ];

    // normaliza strings: remove acentos, minúsculas, remove não-alnum
    $normalize = function($s) {
        $s = (string)$s;
        // translitera (tenta remover acentos)
        $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        $s = mb_strtolower($s);
        $s = preg_replace('/[^a-z0-9]+/u', ' ', $s);
        $s = trim(preg_replace('/\s+/', ' ', $s));
        return $s;
    };

    $tipo_norm = $normalize($tipo_nome);

    // prepara mapa normalizado
    $mapNorm = [];
    foreach ($mapping as $k => $v) {
        $mapNorm[$normalize($k)] = $v;
    }

    $destSub = 'outros';
    // 1) correspondência exata
    if (isset($mapNorm[$tipo_norm])) {
        $destSub = $mapNorm[$tipo_norm];
    } else {
        // 2) procurar por palavra-chave dentro do tipo normalizado
        foreach ($mapNorm as $k => $v) {
            if (mb_stripos($tipo_norm, $k) !== false || mb_stripos($k, $tipo_norm) !== false) {
                $destSub = $v;
                break;
            }
        }
    }

    // criar pastas padrão
    $subfolders = array_values(array_unique(array_merge(array_values($mapping), ['outros'])));
    $fileManager->ensureEquipmentFolders($codigo_interno, $subfolders);

    // upload para a pasta específica
    $baseDir = __DIR__ . '/../../uploads/documentos/' . $codigo_interno . '/' . $destSub;
    $uploadResult = $fileManager->uploadFileToDir($_FILES['ficheiro'], $baseDir);

    // Inserir no banco de dados
    $id_fornecedor = !empty($_POST['id_fornecedor']) ? intval($_POST['id_fornecedor']) : null;
    $nome_documento = !empty($_POST['nome_documento']) ? $_POST['nome_documento'] : $uploadResult['originalName'];
    $data_documento = !empty($_POST['data_documento']) ? $_POST['data_documento'] : date('Y-m-d');
    $data_validade = !empty($_POST['data_validade']) ? $_POST['data_validade'] : null;

    $resultado = $db_insert->insertDocumento(
        $id_equipamento,
        $tipo_documento,
        $id_fornecedor,
        $nome_documento,
        $data_documento,
        $data_validade,
        $uploadResult['extension'],
        $uploadResult['fileName'],
        $uploadResult['size'],
        $_POST['observacoes'] ?? null
    );

    if ($resultado) {
        http_response_code(200);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Documento enviado com sucesso',
            'ficheiro' => $uploadResult['fileName'],
            'url' => $fileManager->getUploadUrl($uploadResult['fileName'])
        ]);
    } else {
        throw new Exception('Erro ao salvar documento na base de dados');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'erro' => $e->getMessage()
    ]);
}
exit;
?>
