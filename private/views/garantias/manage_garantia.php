<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';
require_once '../../includes/file_upload.php';

$mensagem = '';
$error = '';
$equipamentos = $pdo->query("SELECT id_equipamento, codigo_interno, designacao FROM EQUIPAMENTO ORDER BY codigo_interno")->fetchAll(PDO::FETCH_ASSOC);
$fornecedores = $dbManager->getFornecedores(null);

$id_garantia = !empty($_GET['id']) ? intval($_GET['id']) : 0;
$editing = $id_garantia > 0;
$garantiaData = [];

function selectedOption($current, $option)
{
    if ($current === null || $option === null) {
        return '';
    }
    return mb_strtolower(trim($current)) === mb_strtolower(trim($option)) ? 'selected' : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_garantia = !empty($_POST['id_garantia']) ? intval($_POST['id_garantia']) : 0;
    $id_equipamento = $_POST['id_equipamento'] ?? '';
    $id_fornecedor = $_POST['id_fornecedor'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $tipo_contrato = $_POST['tipo_contrato'] ?? '';
    $periodicidade = $_POST['periodicidade'] ?? '';
    $custo_contrato = $_POST['custo_contrato'] ?? null;

    if (empty($id_equipamento) || empty($id_fornecedor) || empty($data_inicio) || empty($data_fim) || empty($tipo_contrato)) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
        $garantiaData = [
            'id_equipamento' => $id_equipamento,
            'id_fornecedor' => $id_fornecedor,
            'data_inicio_garantia' => $data_inicio,
            'data_fim_garantia' => $data_fim,
            'tipo_contrato' => $tipo_contrato,
            'periodicidade_manutencao' => $periodicidade,
            'custo_contrato' => $custo_contrato
        ];
    } else {
        if ($id_garantia > 0) {
            $sql = "UPDATE GARANTIA_CONTRATO SET
                        id_equipamento = :equipamento,
                        id_fornecedor = :fornecedor,
                        data_inicio_garantia = :inicio,
                        data_fim_garantia = :fim,
                        tipo_contrato = :tipo,
                        periodicidade_manutencao = :periodicidade,
                        custo_contrato = :custo
                    WHERE id_garantia = :id";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':equipamento' => $id_equipamento,
                ':fornecedor' => $id_fornecedor,
                ':inicio' => $data_inicio,
                ':fim' => $data_fim,
                ':tipo' => $tipo_contrato,
                ':periodicidade' => $periodicidade,
                ':custo' => $custo_contrato,
                ':id' => $id_garantia
            ]);

            if ($resultado) {
                $uploadError = '';
                if (!empty($_FILES['certificado_garantia']['name'])) {
                    try {
                        $stmt = $pdo->prepare('SELECT codigo_interno FROM EQUIPAMENTO WHERE id_equipamento = :id');
                        $stmt->execute([':id' => $id_equipamento]);
                        $equipInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                        $codigo_interno = $equipInfo['codigo_interno'] ?? null;

                        if (empty($codigo_interno)) {
                            throw new Exception('Código interno do equipamento não encontrado.');
                        }

                        $fileManager = new FileUploadManager();
                        $fileManager->ensureEquipmentFolders($codigo_interno, ['certificado_garantia']);
                        $destDir = __DIR__ . '/../../uploads/documentos/' . $codigo_interno . '/certificado_garantia';

                        $uploadResult = $fileManager->uploadFileToDir($_FILES['certificado_garantia'], $destDir);

                        $stmt = $pdo->prepare('SELECT id_tipo_documento FROM TIPO_DOCUMENTO WHERE LOWER(nome_tipo) = :tipo LIMIT 1');
                        $stmt->execute([':tipo' => 'certificado de garantia']);
                        $tipoRow = $stmt->fetch(PDO::FETCH_ASSOC);
                        $id_tipo_documento = $tipoRow['id_tipo_documento'] ?? null;

                        if ($id_tipo_documento) {
                            $db_insert->insertDocumento(
                                $id_equipamento,
                                $id_tipo_documento,
                                $id_fornecedor,
                                'Certificado de Garantia',
                                date('Y-m-d'),
                                null,
                                $uploadResult['extension'],
                                $uploadResult['fileName'],
                                $uploadResult['size'],
                                null
                            );
                        }
                    } catch (Exception $e) {
                        $uploadError = $e->getMessage();
                    }
                }

                if (empty($uploadError)) {
                    header('Location: garantias_lista.php?message=' . urlencode('Garantia actualizada com sucesso!'));
                    exit();
                }

                $error = 'Garantia actualizada com sucesso, mas o upload falhou: ' . $uploadError;
            } else {
                $error = 'Ocorreu um erro ao actualizar a garantia. Verifique os dados e tente novamente.';
                $garantiaData = [
                    'id_equipamento' => $id_equipamento,
                    'id_fornecedor' => $id_fornecedor,
                    'data_inicio_garantia' => $data_inicio,
                    'data_fim_garantia' => $data_fim,
                    'tipo_contrato' => $tipo_contrato,
                    'periodicidade_manutencao' => $periodicidade,
                    'custo_contrato' => $custo_contrato
                ];
            }
        } else {
            $resultado = $db_insert->insertGarantiaContrato(
                $id_equipamento,
                $id_fornecedor,
                $data_inicio,
                $data_fim,
                $tipo_contrato,
                $periodicidade,
                $custo_contrato
            );

            if ($resultado) {
                $uploadError = '';
                if (!empty($_FILES['certificado_garantia']['name'])) {
                    try {
                        $stmt = $pdo->prepare('SELECT codigo_interno FROM EQUIPAMENTO WHERE id_equipamento = :id');
                        $stmt->execute([':id' => $id_equipamento]);
                        $equipInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                        $codigo_interno = $equipInfo['codigo_interno'] ?? null;

                        if (empty($codigo_interno)) {
                            throw new Exception('Código interno do equipamento não encontrado.');
                        }

                        $fileManager = new FileUploadManager();
                        $fileManager->ensureEquipmentFolders($codigo_interno, ['certificado_garantia']);
                        $destDir = __DIR__ . '/../../uploads/documentos/' . $codigo_interno . '/certificado_garantia';

                        $uploadResult = $fileManager->uploadFileToDir($_FILES['certificado_garantia'], $destDir);

                        $stmt = $pdo->prepare('SELECT id_tipo_documento FROM TIPO_DOCUMENTO WHERE LOWER(nome_tipo) = :tipo LIMIT 1');
                        $stmt->execute([':tipo' => 'certificado de garantia']);
                        $tipoRow = $stmt->fetch(PDO::FETCH_ASSOC);
                        $id_tipo_documento = $tipoRow['id_tipo_documento'] ?? null;

                        if ($id_tipo_documento) {
                            $db_insert->insertDocumento(
                                $id_equipamento,
                                $id_tipo_documento,
                                $id_fornecedor,
                                'Certificado de Garantia',
                                date('Y-m-d'),
                                null,
                                $uploadResult['extension'],
                                $uploadResult['fileName'],
                                $uploadResult['size'],
                                null
                            );
                        }
                    } catch (Exception $e) {
                        $uploadError = $e->getMessage();
                    }
                }

                if (empty($uploadError)) {
                    header('Location: garantias_lista.php?message=' . urlencode('Garantia registada com sucesso!'));
                    exit();
                }

                $error = 'Garantia registada com sucesso, mas o upload falhou: ' . $uploadError;
            } else {
                $error = 'Ocorreu um erro ao registar a garantia. Verifique os dados e tente novamente.';
                $garantiaData = [
                    'id_equipamento' => $id_equipamento,
                    'id_fornecedor' => $id_fornecedor,
                    'data_inicio_garantia' => $data_inicio,
                    'data_fim_garantia' => $data_fim,
                    'tipo_contrato' => $tipo_contrato,
                    'periodicidade_manutencao' => $periodicidade,
                    'custo_contrato' => $custo_contrato
                ];
            }
        }
    }
}

if ($id_garantia > 0 && empty($garantiaData)) {
    $stmt = $pdo->prepare('SELECT * FROM GARANTIA_CONTRATO WHERE id_garantia = :id');
    $stmt->execute([':id' => $id_garantia]);
    $garantiaData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $editing = !empty($garantiaData);
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<main class="main-content">
    <header>
        <div class="header-title">
            <h1><?= $editing ? 'Editar Garantia' : 'Registar Garantia' ?></h1>
            <p><?= $editing ? 'Atualize o contrato de garantia existente.' : 'Adicione um novo contrato de garantia para um equipamento.' ?>
            </p>
        </div>
        <a href="garantias_lista.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Voltar à Lista
        </a>
    </header>

    <div class="content-wrapper">
        <div class="form-card">
            <div class="card-header">
                <h2>Detalhes do Contrato de Garantia</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"
                    style="margin: 1rem; padding: 1rem; background: #fdecea; color: #b02a37; border-radius: 0.5rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="id_equipamento">Equipamento *</label>
                        <select id="id_equipamento" name="id_equipamento" required>
                            <option value="">Selecionar equipamento</option>
                            <?php foreach ($equipamentos as $equipamento): ?>
                                <option value="<?php echo $equipamento['id_equipamento']; ?>"
                                    <?= (isset($garantiaData['id_equipamento']) && $garantiaData['id_equipamento'] == $equipamento['id_equipamento']) ? 'selected' : '' ?>>
                                    <?php echo ($equipamento['codigo_interno'] . ' - ' . $equipamento['designacao']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_fornecedor">Fornecedor *</label>
                        <select id="id_fornecedor" name="id_fornecedor" required>
                            <option value="">Selecionar fornecedor</option>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <option value="<?php echo $fornecedor['id_fornecedor']; ?>"
                                    <?= (isset($garantiaData['id_fornecedor']) && $garantiaData['id_fornecedor'] == $fornecedor['id_fornecedor']) ? 'selected' : '' ?>>
                                    <?php echo ($fornecedor['nome_empresa']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tipo_contrato">Tipo de Garantia *</label>
                        <select id="tipo_contrato" name="tipo_contrato" required>
                            <option value="">Selecionar tipo</option>
                            <option value="Garantia de Fábrica" <?= selectedOption($garantiaData['tipo_contrato'] ?? null, 'Garantia de Fábrica') ?>>Garantia de Fábrica</option>
                            <option value="Garantia Extendida" <?= selectedOption($garantiaData['tipo_contrato'] ?? null, 'Garantia Extendida') ?>>Garantia Extendida</option>
                            <option value="Garantia de Terceiro" <?= selectedOption($garantiaData['tipo_contrato'] ?? null, 'Garantia de Terceiro') ?>>Garantia de Terceiro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="data_inicio">Data de Início *</label>
                        <input type="date" id="data_inicio" name="data_inicio" required
                            value="<?= ($garantiaData['data_inicio_garantia'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="data_fim">Data de Fim *</label>
                        <input type="date" id="data_fim" name="data_fim" required
                            value="<?= ($garantiaData['data_fim_garantia'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="periodicidade">Periodicidade de Manutenção</label>
                        <select id="periodicidade" name="periodicidade">
                            <option value="">Sem periodicidade definida</option>
                            <option value="Mensal" <?= selectedOption($garantiaData['periodicidade_manutencao'] ?? null, 'Mensal') ?>>Mensal</option>
                            <option value="Trimestral" <?= selectedOption($garantiaData['periodicidade_manutencao'] ?? null, 'Trimestral') ?>>Trimestral</option>
                            <option value="Semestral" <?= selectedOption($garantiaData['periodicidade_manutencao'] ?? null, 'Semestral') ?>>Semestral</option>
                            <option value="Anual" <?= selectedOption($garantiaData['periodicidade_manutencao'] ?? null, 'Anual') ?>>Anual</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="custo_contrato">Custo do Contrato</label>
                        <input type="number" id="custo_contrato" name="custo_contrato" step="0.01" min="0"
                            placeholder="0.00" value="<?= ($garantiaData['custo_contrato'] ?? '') ?>">
                    </div>

                    <input type="hidden" name="id_garantia" value="<?= $id_garantia ?>">
                </div>

                <div class="form-actions" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                    <div class="upload-section required" style="margin:0;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; color:var(--text-dark);">Certificado de Garantia (PDF)</label>
                        <button type="button" class="btn btn-primary upload-btn"
                            onclick="document.getElementById('certificado_garantia').click();"
                            style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <i class="fas fa-upload"></i>
                            Adicionar Documentação
                        </button>
                        <input type="file" id="certificado_garantia" name="certificado_garantia" accept="application/pdf" style="display:none;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
                        <i class="fas fa-save"></i>
                        <?= $editing ? 'Atualizar Garantia' : 'Registar Garantia' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>