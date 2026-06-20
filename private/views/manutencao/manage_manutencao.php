<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php require_once '../../includes/db_data.php'; ?>
<?php require_once '../../includes/insert_db.php'; ?>
<?php require_once '../../includes/file_upload.php'; ?>
<?php
// Process form submission: create or update manutenção
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_manutencao = !empty($_POST['id_manutencao']) ? intval($_POST['id_manutencao']) : 0;
    $id_equipamento = $_POST['id_equipamento'] ?? null;
    $id_fornecedor = !empty($_POST['id_fornecedor']) ? intval($_POST['id_fornecedor']) : null;
    $tipo = $_POST['tipo_manutencao'] ?? '';
    $data_manut = $_POST['data_manutencao'] ?? null;
    $descricao = $_POST['descricao'] ?? '';
    $custo = isset($_POST['custo']) && $_POST['custo'] !== '' ? floatval($_POST['custo']) : null;
    $proximo = $_POST['proximo_manutencao'] ?? null;

    try {
        if ($id_manutencao > 0) {
            // Update existing
            $sql = "UPDATE MANUTENCAO SET id_equipamento = :equip, id_fornecedor = :forn, tipo_manutencao = :tipo, data_manutencao = :data, descricao_trabalho = :descricao, custo_manutencao = :custo, proximo_manutencao_prevista = :proximo WHERE id_manutencao = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':equip' => $id_equipamento,
                ':forn' => $id_fornecedor,
                ':tipo' => $tipo,
                ':data' => $data_manut,
                ':descricao' => $descricao,
                ':custo' => $custo,
                ':proximo' => $proximo,
                ':id' => $id_manutencao
            ]);
            header('Location: manutencao_lista.php?message=' . urlencode('Manutenção atualizada com sucesso.'));
            exit;
        } else {
            // Insert new
            $res = $db_insert->insertManutencao($id_equipamento, $id_fornecedor, $tipo, $data_manut, $descricao, $custo, $proximo);
            if ($res) {
                $id_manutencao = intval($pdo->lastInsertId());
                $uploadError = '';

                if ($id_manutencao > 0 && !empty($_FILES['ficheiros']['name'][0])) {
                    try {
                        $stmt = $pdo->prepare('SELECT codigo_interno FROM EQUIPAMENTO WHERE id_equipamento = :id');
                        $stmt->execute([':id' => $id_equipamento]);
                        $equipInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                        $codigo_interno = $equipInfo['codigo_interno'] ?? null;

                        if ($codigo_interno) {
                            $fileManager = new FileUploadManager();
                            $fileManager->ensureEquipmentFolders($codigo_interno, ['contrato_manutencao']);
                            $destDir = __DIR__ . '/../../uploads/documentos/' . $codigo_interno . '/contrato_manutencao';

                            $files = $_FILES['ficheiros'];
                            $count = count($files['name']);
                            for ($i = 0; $i < $count; $i++) {
                                if (empty($files['name'][$i]) || $files['error'][$i] !== UPLOAD_ERR_OK) {
                                    continue;
                                }

                                $fileArray = [
                                    'name' => $files['name'][$i],
                                    'type' => $files['type'][$i],
                                    'tmp_name' => $files['tmp_name'][$i],
                                    'error' => $files['error'][$i],
                                    'size' => $files['size'][$i]
                                ];

                                $uploadResult = $fileManager->uploadFileToDir($fileArray, $destDir);
                                $db_insert->insertDocumentoManutencao(
                                    $id_manutencao,
                                    $uploadResult['originalName'],
                                    'Manutenção',
                                    $uploadResult['fileName'],
                                    $uploadResult['size'],
                                    date('Y-m-d'),
                                    null
                                );
                            }
                        } else {
                            $uploadError = 'Código interno do equipamento não encontrado para guardar os ficheiros.';
                        }
                    } catch (Exception $e) {
                        $uploadError = $e->getMessage();
                    }
                }

                if (empty($uploadError)) {
                    header('Location: manutencao_lista.php?message=' . urlencode('Manutenção registada com sucesso.'));
                    exit;
                }

                $message = 'Manutenção registada com sucesso. ' . ($uploadError ? 'Upload falhou: ' . $uploadError : '');
            } else {
                $error = 'Falha ao registar manutenção.';
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<?php
// Se vier um id na query string, carregar dados para edição
$editing = false;
$manutencaoData = [];
if (!empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare('SELECT * FROM MANUTENCAO WHERE id_manutencao = :id');
    $stmt->execute([':id' => $id]);
    $manutencaoData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $editing = !empty($manutencaoData);
}
?>
<?php
// Buscar lista de fornecedores e equipamentos para os dropdowns
$fornecedores = [];
$equipamentos = [];

try {
    $stmt = $pdo->query('SELECT id_fornecedor, nome_empresa FROM FORNECEDOR WHERE ativo = 1 ORDER BY nome_empresa');
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    // Se falhar, continue sem fornecedores
}

try {
    $stmt = $pdo->query('SELECT id_equipamento, codigo_interno, designacao FROM EQUIPAMENTO ORDER BY codigo_interno');
    $equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    // Se falhar, continue sem equipamentos
}
?>


    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1><?= $editing ? 'Editar Manutenção' : 'Registar Manutenção' ?></h1>
                <p>Registe uma nova intervenção em equipamento</p>
            </div>
            <?php include '../../includes/user_menu.php'; ?>
        </header>

        <div class="content-wrapper">
            <div class="form-card">
                <form id="novaManutencaoForm" method="post" enctype="multipart/form-data">
                    <div class="form-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="dataManutencao">Data da Intervenção *</label>
                                <input type="date" id="dataManutencao" name="data_manutencao" value="<?= ($manutencaoData['data_manutencao'] ?? '') ?>" required />
                            </div>
                            <div class="form-group">
                                <label for="equipamento">Equipamento *</label>
                                <select id="equipamento" name="id_equipamento" required>
                                    <option value="">-- Selecione um equipamento --</option>
                                    <?php foreach ($equipamentos as $eq): ?>
                                        <option value="<?= $eq['id_equipamento'] ?>" <?= (isset($manutencaoData['id_equipamento']) && $manutencaoData['id_equipamento'] == $eq['id_equipamento']) ? 'selected' : '' ?>>
                                            <?= ($eq['codigo_interno'] . ' - ' . $eq['designacao']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipoManutencao">Tipo de manutenção *</label>
                                <select id="tipoManutencao" name="tipo_manutencao" required>
                                    <option value="Preventiva" <?= (isset($manutencaoData['tipo_manutencao']) && $manutencaoData['tipo_manutencao'] === 'Preventiva') ? 'selected' : '' ?>>Preventiva</option>
                                    <option value="Correctiva" <?= (isset($manutencaoData['tipo_manutencao']) && $manutencaoData['tipo_manutencao'] === 'Correctiva') ? 'selected' : '' ?>>Correctiva</option>
                                    <option value="Urgente" <?= (isset($manutencaoData['tipo_manutencao']) && $manutencaoData['tipo_manutencao'] === 'Urgente') ? 'selected' : '' ?>>Urgente</option>
                                    <option value="Calibracao" <?= (isset($manutencaoData['tipo_manutencao']) && $manutencaoData['tipo_manutencao'] === 'Calibracao') ? 'selected' : '' ?>>Calibração</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fornecedor">Fornecedor (opcional)</label>
                                <select id="fornecedor" name="id_fornecedor">
                                    <option value="">-- Nenhum --</option>
                                    <?php foreach ($fornecedores as $forn): ?>
                                        <option value="<?= $forn['id_fornecedor'] ?>" <?= (isset($manutencaoData['id_fornecedor']) && $manutencaoData['id_fornecedor'] == $forn['id_fornecedor']) ? 'selected' : '' ?>>
                                            <?= ($forn['nome_empresa']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="custo">Custo (€)</label>
                                <input type="number" id="custo" name="custo" min="0" step="0.01" placeholder="120.00" value="<?= ($manutencaoData['custo_manutencao'] ?? '') ?>" />
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado">
                                    <option value="Concluida" <?= (isset($manutencaoData['estado']) && $manutencaoData['estado'] === 'Concluida') ? 'selected' : '' ?>>Concluída</option>
                                    <option value="Pendente" <?= (isset($manutencaoData['estado']) && $manutencaoData['estado'] === 'Pendente') ? 'selected' : '' ?>>Pendente</option>
                                    <option value="Agendada" <?= (isset($manutencaoData['estado']) && $manutencaoData['estado'] === 'Agendada') ? 'selected' : '' ?>>Agendada</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="descricao">Descrição *</label>
                                <textarea id="descricao" name="descricao" rows="5" placeholder="Descreva a intervenção realizada" required><?= ($manutencaoData['descricao_trabalho'] ?? '') ?></textarea>
                            </div>
                        </div>

                                         
      <div class="upload-section">
    <label for="ficheiros">Upload de Ficheiros (opcional)</label>

    <input 
        type="file" 
        id="ficheiros" 
        name="ficheiros[]" 
        multiple 
        style="display:none;"
        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx"
    />

    <button type="button" class="btn btn-primary" id="btnUpload">
        <i class="fas fa-upload"></i>
        Adicionar Documentação
    </button>

    <small style="display:block; margin-top:8px; color: var(--text-muted);">
        Aceita: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX (máx 10MB por ficheiro)
    </small>

    <!-- Lista de ficheiros selecionados -->
    <div id="fileList" style="margin-top: 15px; display: none;">
        <h5>Ficheiros selecionados:</h5>
        <ul id="uploadedFiles" style="list-style: none; padding: 0;">
        </ul>
    </div>
</div>


            
            <input type="hidden" name="id_manutencao" id="id_manutencao" value="<?= ($manutencaoData['id_manutencao'] ?? ($_GET['id'] ?? '')) ?>" />
                        <div class="form-actions">
                            
                        
                        <a href="manutencao_lista.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Registar Manutenção
                            </button>

       
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Upload de ficheiros
        const btnUpload = document.getElementById('btnUpload');
        const fileInput = document.getElementById('ficheiros');
        const fileList = document.getElementById('fileList');
        const uploadedFiles = document.getElementById('uploadedFiles');

        // Abrir file picker ao clicar no botão
        btnUpload.addEventListener('click', () => {
            fileInput.click();
        });

        // Quando ficheiros são selecionados
        fileInput.addEventListener('change', (e) => {
            updateFileList();
        });

        function updateFileList() {
            const files = fileInput.files;
            uploadedFiles.innerHTML = '';

            if (files.length === 0) {
                fileList.style.display = 'none';
                return;
            }

            fileList.style.display = 'block';

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const li = document.createElement('li');
                li.style.cssText = 'padding: 8px; margin: 5px 0; background: #f5f5f5; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;';
                
                const fileInfo = document.createElement('span');
                fileInfo.textContent = `${file.name} (${formatFileSize(file.size)})`;
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger';
                removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
                removeBtn.style.marginLeft = '10px';
                removeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    removeFile(i);
                });

                li.appendChild(fileInfo);
                li.appendChild(removeBtn);
                uploadedFiles.appendChild(li);
            }
        }

        function removeFile(index) {
            const dt = new DataTransfer();
            const files = fileInput.files;

            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }

            fileInput.files = dt.files;
            updateFileList();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>

  <?php include '../../includes/footer.php'; ?>
