<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php require_once '../../includes/auth.php'; ?>
<?php require_once '../../includes/db_data.php'; ?>
<?php require_once '../../includes/insert_db.php'; ?>
<?php require_once '../../includes/file_upload.php'; ?>

<?php
$id_equipamento = $_GET['id_equipamento'] ?? null;
$equipamento = [];
$componentes_associados = [];
$fornecedores_associados = [];

if ($id_equipamento) {
    $equipamento = $dbManager->getEquipamentoByCodigo($id_equipamento);
    $componentes_associados = $dbManager->getComponentesEquipamento_by_id($id_equipamento);
    $fornecedores_associados = $dbManager->getEquipamentoFornecedores_by_id($id_equipamento);
}

$categorias = $dbManager->getCategoriasEquipamento();
$criticidades = $dbManager->getCriticidades();
$localizacoes = $dbManager->getLocalizacoes();
$fornecedores = $dbManager->getFornecedores();
$tipo = ['Compra', 'Doação', 'Aluguel', 'Empréstimo'];

$estados = [];
$estadosHtml = '';
ob_start();
$possibleEstados = $dbManager->getEstadosEquipamento();
$estadosHtml = ob_get_clean();
if (is_array($possibleEstados)) {
    $estados = $possibleEstados;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'codigo_interno' => $_POST['codigo_interno'] ?? '',
        'designacao' => $_POST['designacao'] ?? '',
        'categoria' => $_POST['categoria'] ?? '',
        'marca' => $_POST['marca'] ?? '',
        'modelo' => $_POST['modelo'] ?? '',
        'numero_serie' => $_POST['numero_serie'] ?? '',
        'criticidade' => $_POST['criticidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'localizacao' => $_POST['localizacao'] ?? '',
        'data_aquisicao' => $_POST['data_aquisicao'] ?? '',
        'ano_fabrico' => $_POST['ano_fabrico'] ?? null,
        'custo_aquisicao' => $_POST['custo'] ?? null,
        'tipo_entrada' => $_POST['tipo_entrada'] ?? '',
        'observacoes' => $_POST['observacoes'] ?? ''
    ];

    if ($id_equipamento) {
        $db_insert->updateEquipamento($id_equipamento, $data);
        $message = "Equipamento atualizado com sucesso!";
    } else {
        $db_insert->insertEquipamento($data);
        $id_equipamento = $pdo->lastInsertId();
        /*$garantia_data = [
            'id_equipamento' => $id_equipamento,
            'id_fornecedor' => $_POST['id_fornecedor'] ?? null,
            'data_inicio_garantia' => null,
            'data_fim_garantia' => null,
            'tipo_contrato' => null,
            'periodicidade_manutencao' => null,
            'custo_contrato' => null
        ];
        $db_insert->insertGarantiaContrato($id_equipamento, $data['id_fornecedor'], $garantia_data['data_inicio_garantia'], $garantia_data['data_fim_garantia'], $garantia_data['tipo_contrato'], $garantia_data['periodicidade_manutencao'], $garantia_data['custo_contrato']);
*/
        $message = "Equipamento registado com sucesso!";
    }

    // Lógica para Componentes
    if ($id_equipamento) {
        $db_insert->deleteComponentesByEquipamento($id_equipamento);
        if (isset($_POST['codigo_componente']) && is_array($_POST['codigo_componente'])) {
            foreach ($_POST['codigo_componente'] as $index => $codigo_componente) {
                if (trim($codigo_componente) === '') {
                    continue;
                }
                $db_insert->insertComponente(
                    $id_equipamento,
                    $codigo_componente,
                    $_POST['designacao_componente'][$index] ?? '',
                    $_POST['marca_componente'][$index] ?? '',
                    $_POST['modelo_componente'][$index] ?? '',
                    $_POST['numero_serie_componente'][$index] ?? '',
                    $_POST['data_aquisicao_componente'][$index] ?? null,
                    $_POST['obs_componente'][$index] ?? ''
                );
            }
        }
    }

    // Lógica para Fornecedores
    if ($id_equipamento) {
        $db_insert->deleteEquipamentoFornecedorByEquipamento($id_equipamento);
        if (isset($_POST['id_fornecedor']) && is_array($_POST['id_fornecedor'])) {
            foreach ($_POST['id_fornecedor'] as $index => $idFornecedor) {
                if (trim($idFornecedor) === '') {
                    continue;
                }
                $db_insert->insertEquipamentoFornecedor(
                    $id_equipamento,
                    $idFornecedor,
                    $_POST['tipo_relacao'][$index] ?? '',
                    $_POST['data_associacao'][$index] ?? null,
                    $_POST['obs_fornecedor'][$index] ?? ''
                );
            }
        }
    }

    header("Location: equipamentos_lista.php?message=" . urlencode($message));
    exit();
}

?>

<main class="main-content">
    <header>
        <div>
            <h1> <?= $id_equipamento ? 'Editar Equipamento' : 'Registar Equipamento' ?>
        </h1>
            <p style="color: var(--text-muted); margin-top: 0.35rem;">Preencha os dados do equipamento para criar ou
                atualizar o registo.</p>
        </div>
        <a href="equipamentos_lista.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i>
            Voltar à Lista
        </a>
    </header>

    <form method="POST" enctype="multipart/form-data">
        <div class="content-wrapper">
            <div class="form-card">
                <div class="card-header">
                    <h2>Dados do Equipamento</h2>
                </div>
                <div class="form-body">
                    <div class="form-section-title">Identificação Geral</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="codigo_interno">Código Interno *</label>
                            <input id="codigo_interno" name="codigo_interno" type="text" placeholder="Ex: EQP-2026-001"
                                value="<?php echo ($equipamento['codigo_interno'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="designacao">Designação *</label>
                            <input id="designacao" name="designacao" type="text" placeholder="Ex: Ventilador Pulmonar"
                                value="<?php echo ($equipamento['designacao'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoria *</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Selecione...</option>
                                <?php
                                foreach ($categorias as $categoria) {
                                    echo '<option value="' . $categoria['id_categoria'] . '" ' . (($equipamento['id_categoria'] ?? '') == $categoria['id_categoria'] ? 'selected' : '') . '>' . ($categoria['nome_categoria']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="marca">Marca *</label>
                            <input id="marca" name="marca" type="text"
                                value="<?php echo ($equipamento['marca'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="modelo">Modelo *</label>
                            <input id="modelo" name="modelo" type="text"
                                value="<?php echo ($equipamento['modelo'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="numero_serie">Número de Série *</label>
                            <input id="numero_serie" name="numero_serie" type="text"
                                value="<?php echo ($equipamento['numero_serie'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-section-title">Classificação e Localização</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="criticidade">Criticidade *</label>
                            <select id="criticidade" name="criticidade" required>
                                <?php
                                foreach ($criticidades as $criticidade) {
                                    echo '<option value="' . $criticidade['id_criticidade'] . '" ' . (($equipamento['id_criticidade'] ?? '') == $criticidade['id_criticidade'] ? 'selected' : '') . '>' . ($criticidade['nivel_criticidade']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado Operacional *</label>
                            <select id="estado" name="estado" required>
                                <?php
                                foreach ($estados as $estado) {
                                    echo '<option value="' . $estado['id_estado'] . '" ' . (($equipamento['id_estado'] ?? '') == $estado['id_estado'] ? 'selected' : '') . '>' . ($estado['nome_estado']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="localizacao">Localização *</label>
                            <select id="localizacao" name="localizacao" required>
                                <option value="">Selecione a Sala...</option>
                                <?php foreach ($localizacoes as $localizacao):
                                    $selected = ($equipamento['id_localizacao'] ?? null) == $localizacao['id_localizacao'] ? 'selected' : '';
                                    $label = $localizacao['edificio'] . ' - ' .
                                        $localizacao['sala_gabinete'] . ' - ' .
                                        $localizacao['piso'] . ' - ' .
                                        $localizacao['servico_departamento'];
                                    ?>
                                    <option value="<?= $localizacao['id_localizacao'] ?>" <?= $selected ?>>
                                        <?= ($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-section-title">Dados de Aquisição</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="data_aquisicao">Data de Aquisição *</label>
                            <input id="data_aquisicao" name="data_aquisicao" type="date"
                                value="<?php echo ($equipamento['data_aquisicao'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ano_fabrico">Ano de Fabrico</label>
                            <input id="ano_fabrico" name="ano_fabrico" type="number" min="1900" max="2099"
                                value="<?php echo ($equipamento['ano_fabrico'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="custo">Custo de Aquisição (€)</label>
                            <input id="custo" name="custo" type="number" step="0.01"
                                value="<?php echo ($equipamento['custo_aquisicao'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="tipo_entrada">Tipo de Entrada *</label>
                            <select id="tipo_entrada" name="tipo_entrada" required>
                                <?php foreach ($tipo as $t) {
                                    echo '<option value="' . $t . '" ' . (($equipamento['tipo_entrada'] ?? '') == $t ? 'selected' : '') . '>' . $t . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="observacoes">Observações</label>
                            <textarea id="observacoes" name="observacoes"
                                rows="4"><?php echo ($equipamento['observacoes'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="form-section-title">Componentes do Equipamento</div>

                    <div id="componentes-container">

                        <?php if (!empty($componentes_associados)): ?>
                            <?php foreach ($componentes_associados as $componente): ?>
                                <div class="componente-item form-grid mb-3">
                                    <div class="form-group">
                                        <label>Código Interno *</label>
                                        <input type="text" name="codigo_componente[]" class="form-control"
                                            value="<?php echo ($componente['codigo_interno_componente'] ?? ''); ?>"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>Designação *</label>
                                        <input type="text" name="designacao_componente[]" class="form-control"
                                            value="<?php echo ($componente['designacao_componente'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Marca</label>
                                        <input type="text" name="marca_componente[]" class="form-control"
                                            value="<?php echo ($componente['marca_componente'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Modelo</label>
                                        <input type="text" name="modelo_componente[]" class="form-control"
                                            value="<?php echo ($componente['modelo_componente'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Número de Série</label>
                                        <input type="text" name="numero_serie_componente[]" class="form-control"
                                            value="<?php echo ($componente['numero_serie_componente'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Data de Aquisição</label>
                                        <input type="date" name="data_aquisicao_componente[]" class="form-control"
                                            value="<?php echo ($componente['data_aquisicao'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group" style="grid-column: 1 / -1;">
                                        <label>Observações</label>
                                        <input type="text" name="obs_componente[]" class="form-control"
                                            value="<?php echo ($componente['observacoes'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-danger removerComponente">
                                            <i class="fas fa-trash"></i>
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="componente-item form-grid mb-3">
                                <div class="form-group">
                                    <label>Código Interno *</label>
                                    <input type="text" name="codigo_componente[]" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Designação *</label>
                                    <input type="text" name="designacao_componente[]" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Marca</label>
                                    <input type="text" name="marca_componente[]" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" name="modelo_componente[]" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Número de Série</label>
                                    <input type="text" name="numero_serie_componente[]" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Data de Aquisição</label>
                                    <input type="date" name="data_aquisicao_componente[]" class="form-control">
                                </div>
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label>Observações</label>
                                    <input type="text" name="obs_componente[]" class="form-control">
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-success" id="adicionarComponente">
                            <i class="fas fa-plus"></i>
                            Adicionar Componente
                        </button>
                    </div>
                    <div class="form-section-title">Fornecedores Associados</div>

                    <div id="fornecedores-container">

                        <?php if (!empty($fornecedores_associados)): ?>
                            <?php foreach ($fornecedores_associados as $fornecedor): ?>
                                <div class="fornecedor-item form-grid mb-3">
                                    <div class="form-group">
                                        <label>Fornecedor *</label>
                                        <select name="id_fornecedor[]" class="form-control" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($fornecedores as $f): ?>
                                                <option value="<?= ($f['id_fornecedor']); ?>"
                                                    <?= ($fornecedor['id_fornecedor'] == $f['id_fornecedor']) ? 'selected' : ''; ?>>
                                                    <?= ($f['nome_empresa']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo de Relação *</label>
                                        <select name="tipo_relacao[]" class="form-control" required>
                                            <option value="Fabricante" <?= (($fornecedor['tipo_relacao'] ?? '') == 'Fabricante') ? 'selected' : ''; ?>>Fabricante</option>
                                            <option value="Distribuidor" <?= (($fornecedor['tipo_relacao'] ?? '') == 'Distribuidor') ? 'selected' : ''; ?>>Distribuidor</option>
                                            <option value="Assistência Técnica" <?= (($fornecedor['tipo_relacao'] ?? '') == 'Assistência Técnica') ? 'selected' : ''; ?>>Assistência Técnica</option>
                                            <option value="Consumíveis" <?= (($fornecedor['tipo_relacao'] ?? '') == 'Consumíveis') ? 'selected' : ''; ?>>Consumíveis</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Data de Associação *</label>
                                        <input type="date" name="data_associacao[]" class="form-control"
                                            value="<?= ($fornecedor['data_associacao'] ?? ''); ?>"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <input type="text" name="obs_fornecedor[]" class="form-control"
                                            value="<?= ($fornecedor['observacoes'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-danger removerFornecedor">
                                            <i class="fas fa-trash"></i>
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="fornecedor-item form-grid mb-3">
                                <div class="form-group">
                                    <label>Fornecedor *</label>
                                    <select name="id_fornecedor[]" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($fornecedores as $f): ?>
                                            <option value="<?= ($f['id_fornecedor']); ?>"><?= ($f['nome_empresa']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Relação *</label>
                                    <select name="tipo_relacao[]" class="form-control" required>
                                        <option value="Fabricante">Fabricante</option>
                                        <option value="Distribuidor">Distribuidor</option>
                                        <option value="Assistência Técnica">Assistência Técnica</option>
                                        <option value="Consumíveis">Consumíveis</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Data de Associação *</label>
                                    <input type="date" name="data_associacao[]" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Observações</label>
                                    
                                    <input type="text" name="obs_fornecedor[]" class="form-control">
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-success" id="adicionarFornecedor">
                            <i class="fas fa-plus"></i>
                            Adicionar Fornecedor
                        </button>
                    </div>
                    <div class="form-actions">
                        <a type="button" class="btn btn-secondary" href="equipamentos_lista.php"><i
                                class="fas fa-times"></i>Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                            <?php echo $id_equipamento ? 'Guardar Alterações' : 'Registar Equipamento'; ?></button>
                    </div>



                </div>
            </div>
            <!-- Botão / Área Upload -->
            <div class="upload-section required mt-4">
                <label>Upload de Ficheiros</label>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDocumentos">
                    <i class="fas fa-upload"></i>
                    Adicionar Documentação
                </button>

                <small style="display:block; margin-top:8px; color: var(--text-muted);">
                    É obrigatório anexar toda a documentação técnica do equipamento.
                </small>
            </div>
        </div>

</main>


<!-- Modal -->
<div class="modal fade" id="modalDocumentos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Gestão Documental do Equipamento
                </h5>
            </div>

            <div class="modal-body">
                <div id="alertDocumento"></div>

                <div class="row g-4">
                    <!-- Manual Utilizador -->
                    <div class="col-md-6">
                        <div class="document-card required">
                            <label class="form-label">Manual de Utilizador *</label>
                            <input type="file" class="form-control ficheiro-upload" data-tipo="1" data-nome="Manual de Utilizador" id="manual_utilizador" name="manual_utilizador">
                            <small class="text-muted">Tipo: 1 (Manual Utilizador)</small>
                        </div>
                    </div>

                    <!-- Manual Serviço -->
                    <div class="col-md-6">
                        <div class="document-card required">
                            <label class="form-label">Manual de Serviço *</label>
                            <input type="file" class="form-control ficheiro-upload" data-tipo="2" data-nome="Manual de Serviço" id="manual_servico" name="manual_servico">
                            <small class="text-muted">Tipo: 2 (Manual Serviço)</small>
                        </div>
                    </div>

                    <!-- Certificado -->
                    <div class="col-md-6">
                        <div class="document-card required">
                            <label class="form-label">Certificado de Calibração *</label>
                            <input type="file" class="form-control ficheiro-upload" data-tipo="3" data-nome="Certificado de Calibração" id="certificado_calibracao" name="certificado_calibracao">
                            <small class="text-muted">Tipo: 3 (Certificado)</small>
                        </div>
                    </div>

                    <!-- Contrato -->
                    <div class="col-md-6">
                        <div class="document-card required">
                            <label class="form-label">Contrato de Manutenção *</label>
                            <input type="file" class="form-control ficheiro-upload" data-tipo="4" data-nome="Contrato de Manutenção" id="contrato_manutencao" name="contrato_manutencao">
                            <small class="text-muted">Tipo: 4 (Contrato)</small>
                        </div>
                    </div>

                    <!-- Fatura -->
                    <div class="col-md-6">
                        <div class="document-card required">
                            <label class="form-label">Fatura / Guia de Aquisição *</label>
                            <input type="file" class="form-control ficheiro-upload" data-tipo="5" data-nome="Fatura de Aquisição" id="fatura_aquisicao" name="fatura_aquisicao">
                            <small class="text-muted">Tipo: 5 (Fatura)</small>
                        </div>
                    </div>

                    <!-- Conformidade -->
                    <div class="col-md-6">
                        <div class="document-card required">
                            <label class="form-label">Declaração de Conformidade *</label>
                            <input type="file" class="form-control ficheiro-upload" data-tipo="6" data-nome="Declaração de Conformidade" id="declaracao_conformidade" name="declaracao_conformidade">
                            <small class="text-muted">Tipo: 6 (Conformidade)</small>
                        </div>
                    </div>

                    <!-- Relatório -->
                    <div class="col-12">
                        <div class="document-card required">
                            <label class="form-label">Relatório Técnico *</label>
                            <input type="file" class="form-control ficheiro-upload" data-tipo="7" data-nome="Relatório Técnico" id="relatorio_tecnico" name="relatorio_tecnico">
                            <small class="text-muted">Tipo: 7 (Relatório)</small>
                        </div>
                    </div>
                </div>

                <div id="uploadStatus" style="margin-top: 1.5rem;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="guardarDocumentos">
                    <i class="fas fa-save"></i>
                    Guardar Documentos
                </button>
            </div>
        </div>
    </div>
</div>
</form>



<script>
    document.getElementById("guardarDocumentos").addEventListener("click", function () {
        const camposObrigatorios = [
            "manual_utilizador",
            "manual_servico",
            "certificado_calibracao",
            "contrato_manutencao",
            "fatura_aquisicao",
            "declaracao_conformidade",
            "relatorio_tecnico"
        ];

        // usar o id numérico do equipamento quando disponível (deve registar antes de enviar documentos)
        const id_equipamento = <?php echo json_encode($id_equipamento ?? ''); ?>;
        if (!id_equipamento) {
            alert("Deve registar o equipamento antes de adicionar documentos!");
            return;
        }

        let ficheirosValidos = [];

        camposObrigatorios.forEach(id => {
            const input = document.getElementById(id);
            if (input.files.length > 0) {
                ficheirosValidos.push({
                    input: input,
                    tipo: input.dataset.tipo,
                    nome: input.dataset.nome
                });
                input.classList.remove("is-invalid");
            } else {
                input.classList.add("is-invalid");
            }
        });

        if (ficheirosValidos.length === 0) {
            alert("Nenhum ficheiro selecionado. Selecione pelo menos um documento.");
            return;
        }

        // Desabilitar botão durante upload
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        const alertDiv = document.getElementById('alertDocumento');
        alertDiv.innerHTML = '';

        let uploadsCompletos = 0;
        let uploadsFalhados = 0;

        // Fazer upload de cada ficheiro
        ficheirosValidos.forEach(ficheiro => {
            const formData = new FormData();
            formData.append('id_equipamento', id_equipamento);
            formData.append('tipo_documento', ficheiro.tipo);
            formData.append('nome_documento', ficheiro.nome);
            formData.append('data_documento', new Date().toISOString().split('T')[0]);
            formData.append('ficheiro', ficheiro.input.files[0]);

            fetch('documento_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    uploadsCompletos++;
                    ficheiro.input.classList.add("is-valid");
                    ficheiro.input.disabled = true;
                } else {
                    uploadsFalhados++;
                    alertDiv.innerHTML += `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${ficheiro.nome}: ${data.erro}</div>`;
                }

                // Se todos os uploads terminarem
                if (uploadsCompletos + uploadsFalhados === ficheirosValidos.length) {
                    btn.disabled = false;
                    if (uploadsFalhados === 0) {
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Documentos salvos!';
                        btn.classList.add('btn-success');
                        alertDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> Todos os documentos foram enviados com sucesso!</div>`;
                        
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalDocumentos'));
                            modal.hide();
                        }, 1500);
                    } else {
                        btn.innerHTML = '<i class="fas fa-save"></i> Guardar Documentos';
                        btn.classList.remove('btn-success');
                    }
                }
            })
            .catch(error => {
                uploadsFalhados++;
                alertDiv.innerHTML += `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Erro: ${error.message}</div>`;
                
                if (uploadsCompletos + uploadsFalhados === ficheirosValidos.length) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save"></i> Guardar Documentos';
                }
            });
        });
    });


    document.getElementById("adicionarFornecedor").addEventListener("click", () => {

        const container = document.getElementById("fornecedores-container");

        const novoFornecedor = document.createElement("div");

        novoFornecedor.className = "fornecedor-item form-grid mb-3";

        novoFornecedor.innerHTML = `
        <div class="form-group">
            <label>Fornecedor *</label>
            <select name="id_fornecedor[]" class="form-control" required>
                <option value="">Selecione...</option>
                <option value="1">Siemens Healthineers</option>
                <option value="2">Philips Medical</option>
                <option value="3">GE Healthcare</option>
            </select>
        </div>

        <div class="form-group">
            <label>Tipo de Relação *</label>
            <select name="tipo_relacao[]" class="form-control" required>
                <option value="Fabricante">Fabricante</option>
                <option value="Distribuidor">Distribuidor</option>
                <option value="Assistência Técnica">Assistência Técnica</option>
                <option value="Consumíveis">Consumíveis</option>
            </select>
        </div>

        <div class="form-group">
            <label>Data de Associação *</label>
            <input type="date"
                   name="data_associacao[]"
                   class="form-control"
                   required>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <input type="text"
                   name="obs_fornecedor[]"
                   class="form-control">
        </div>

        <div class="form-group">
            <button type="button"
                    class="btn btn-danger removerFornecedor">
                <i class="fas fa-trash"></i>
                Remover
            </button>
        </div>
    `;

        container.appendChild(novoFornecedor);
    });

    document.addEventListener("click", function (e) {

        if (e.target.closest(".removerFornecedor")) {
            e.target.closest(".fornecedor-item").remove();
        }

    });



    document.getElementById("adicionarComponente").addEventListener("click", () => {

        const container = document.getElementById("componentes-container");

        const div = document.createElement("div");
        div.className = "componente-item form-grid mb-3";

        div.innerHTML = `
        <div class="form-group">
            <label>Código Interno *</label>
            <input type="text" name="codigo_componente[]" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Designação *</label>
            <input type="text" name="designacao_componente[]" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Marca</label>
            <input type="text" name="marca_componente[]" class="form-control">
        </div>

        <div class="form-group">
            <label>Modelo</label>
            <input type="text" name="modelo_componente[]" class="form-control">
        </div>

        <div class="form-group">
            <label>Número de Série</label>
            <input type="text" name="numero_serie_componente[]" class="form-control">
        </div>

        <div class="form-group">
            <label>Data de Aquisição</label>
            <input type="date" name="data_aquisicao_componente[]" class="form-control">
        </div>

        <div class="form-group">
            <label>Observações</label>
            <input type="text" name="obs_componente[]" class="form-control">
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-danger removerComponente">
                <i class="fas fa-trash"></i>
                Remover
            </button>
        </div>
    `;

        container.appendChild(div);
    });

    document.addEventListener("click", function (e) {
        if (e.target.closest(".removerComponente")) {
            e.target.closest(".componente-item").remove();
        }
    });

</script>
<?php include '../../includes/footer.php'; ?>