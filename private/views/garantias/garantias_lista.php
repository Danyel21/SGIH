<?php require_once '../../includes/auth.php'; ?>
<?php require_once '../../includes/db_data.php'; ?>

<?php
$mensagem = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
$search = trim($_GET['search'] ?? '');
$estado = $_GET['estado'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_garantia_id'])) {
    $idGarantiaDelete = intval($_POST['delete_garantia_id']);
    try {
        if ($idGarantiaDelete <= 0) {
            throw new Exception('ID de garantia inválido.');
        }
        if ($dbManager->deleteGarantiaContrato($idGarantiaDelete)) {
            header('Location: garantias_lista.php?message=' . urlencode('Garantia eliminada com sucesso.'));
            exit;
        }
        $error = 'Não foi possível eliminar a garantia.';
    } catch (Exception $ex) {
        $error = $ex->getMessage();
    }
}

$garantias = $dbManager->getGarantiasContratos();

if (!empty($search) || !empty($estado)) {
    $searchLower = mb_strtolower($search);
    $garantias = array_filter($garantias, function($garantia) use ($searchLower, $estado) {
        $equipamento = mb_strtolower($garantia['equipamento_associado'] ?? '');
        $fornecedor = mb_strtolower($garantia['fornecedor_associado'] ?? '');
        $tipo = mb_strtolower($garantia['tipo_contrato'] ?? '');

        $matchesSearch = $searchLower === '' || mb_stripos($equipamento, $searchLower) !== false || mb_stripos($fornecedor, $searchLower) !== false || mb_stripos($tipo, $searchLower) !== false;
        $dataFim = $garantia['data_fim_garantia'];
        $status = (date('Y-m-d') <= $dataFim) ? 'Ativo' : 'Expirado';
        $matchesEstado = $estado === '' || $estado === $status;

        return $matchesSearch && $matchesEstado;
    });
}
?><?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Lista de Garantias</h1>
                <p>Gerencie as garantias dos equipamentos do hospital</p>
            </div>
       
    <?php include '../../includes/user_menu.php'; ?>
        </header>

        <!-- Content -->
        <div class="content-wrapper">

  <?php if (!empty($message)): ?>
                <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; background: #e6f4ea; color: #1f6f3b; border-radius: 0.5rem; border-left: 4px solid #1f6f3b;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                    <?php echo ($message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; background: #fdecea; color: #b02a37; border-radius: 0.5rem; border-left: 4px solid #b02a37;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                    <?php echo ($error); ?>
                </div>
            <?php endif; ?>
            <form method="GET" class="filters-box">
                
                <div class="filter-group flex-grow">
                    <label>Pesquisar</label>
                    <input type="text" name="search" value="<?= ($search) ?>" placeholder="Equipamento, fornecedor, tipo...">
                </div>
                <div class="filter-group w-48">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="Ativo" <?= ($estado === 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                        <option value="Expirado" <?= ($estado === 'Expirado') ? 'selected' : '' ?>>Expirado</option>
                    </select>
                </div>
                <div class="filter-button">
                    <button type="submit">
                        <i class="fas fa-filter"></i>
                        Filtrar
                    </button>
                </div>
                
            </form>
              <!-- Botão para Novo equipamento -->
            <div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
                <a href="manage_garantia.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nova Garantia
                </a>
            </div>
            <?php if (!empty($search) || !empty($estado)): ?>
                <div class="alert alert-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #e7f3fe; color: #31708f; border-radius: 0.5rem; border-left: 4px solid #31708f;">
                    <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                    A ver resultados filtrados.
                    <?php if (!empty($search)): ?>
                        <strong>Pesquisa:</strong> <?= ($search) ?>
                    <?php endif; ?>
                    <?php if (!empty($estado)): ?>
                        | <strong>Estado:</strong> <?= ($estado) ?>
                    <?php endif; ?>
                    <!-- <a href="garantias_lista.php" style="margin-left: 1rem; text-decoration: underline;">Limpar Filtros</a> -->
                </div>
            <?php endif; ?>
            <div class="table-container">

              
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Equipamento</th>
                                <th>Fornecedor</th>
                                <th>Tipo</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Estado</th>
                                <th class="actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($garantias)): ?>
                                <?php foreach ($garantias as $garantia): 
                                    $dataFim = $garantia['data_fim_garantia'];
                                    $estado = (date('Y-m-d') <= $dataFim) ? 'Ativo' : 'Expirado';
                                    $badgeClass = ($estado === 'Ativo') ? 'badge badge-success' : 'badge badge-danger';
                                ?>
                                    <tr>
                                        <td><?php echo ($garantia['equipamento_associado']); ?></td>
                                        <td><?php echo ($garantia['fornecedor_associado']); ?></td>
                                        <td><?php echo ($garantia['tipo_contrato']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($garantia['data_inicio_garantia'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($garantia['data_fim_garantia'])); ?></td>
                                        <td>
                                            <span class="<?php echo $badgeClass; ?>"><?php echo $estado; ?></span>
                                        </td>
                                        <td class="actions">
                                                                                        <a href="#" class="view" data-bs-toggle="modal" data-bs-target="#detailsModal" data-garantia="<?php echo ($garantia['id_garantia']); ?>" data-equipa="<?php echo ($garantia['equipamento_associado']); ?>" data-fornecedor="<?php echo ($garantia['fornecedor_associado']); ?>" data-tipo="<?php echo ($garantia['tipo_contrato']); ?>" data-inicio="<?php echo ($garantia['data_inicio_garantia']); ?>" data-fim="<?php echo ($garantia['data_fim_garantia']); ?>" data-status="<?php echo $estado; ?>" data-periodicidade="<?php echo ($garantia['periodicidade_manutencao'] ?? ''); ?>" data-custo="<?php echo ($garantia['custo_contrato'] ?? ''); ?>" data-observacoes="<?php echo ($garantia['observacoes'] ?? ''); ?>" title="Ver detalhes"><i class="fas fa-info-circle"></i>
                                                                                    </a>

                                            <a href="manage_garantia.php?id=<?php echo $garantia['id_garantia']; ?>" class="edit" title="Editar garantia"><i class="fas fa-edit"></i>
                                        </a>
                                            <button class="delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-garantia="<?php echo ($garantia['id_garantia']); ?>" data-nome="<?php echo ($garantia['equipamento_associado']); ?>"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">Nenhuma garantia registada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </main>

    <!-- Modal de Eliminação -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"> <i class="fas fa-exclamation-triangle"
                            style="color: var(--danger); margin-right: 0.5rem;"></i>
                        Eliminar Garantia</h5>
                </div>
                <div class="modal-body">
                    <p>Tem a certeza que deseja eliminar esta garantia?</p>
                    <p style="font-weight: 600; color: var(--text-main);">
                        <i class="fas fa-laptop" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                        <span id="equipamentoName">EQP-2026-001</span>
                    </p>
                    <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 1rem;">
                        <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                        Esta ação não pode ser desfeita.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                            class="fas fa-times"></i>Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><i
                            class="fas fa-trash"></i>Eliminar</button>
                </div>
            </div>
        </div>
    </div>


    </div>

    <form id="deleteGarantiaForm" method="post" style="display:none;">
        <input type="hidden" name="delete_garantia_id" id="deleteGarantiaId" value="" />
    </form>

    <!-- Modal de Detalhes -->
    <div class="modal fade modal-details" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel"><i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>Detalhes da Garantia</h5>
                </div>
                <div class="modal-body">
                    <div class="detail-row"><strong>Equipamento:</strong> <span id="detailEquipamento"></span></div>
                    <div class="detail-row"><strong>Fornecedor:</strong> <span id="detailFornecedor"></span></div>
                    <div class="detail-row"><strong>Tipo de Contrato:</strong> <span id="detailTipo"></span></div>
                    <div class="detail-row"><strong>Início da Garantia:</strong> <span id="detailInicio"></span></div>
                    <div class="detail-row"><strong>Fim da Garantia:</strong> <span id="detailFim"></span></div>
                    <div class="detail-row"><strong>Estado:</strong> <span id="detailStatus"></span></div>
                    <div class="detail-row"><strong>Periodicidade:</strong> <span id="detailPeriodicidade"></span></div>
                    <div class="detail-row"><strong>Custo do Contrato:</strong> <span id="detailCusto"></span></div>
                    <div class="detail-row"><strong>Observações:</strong>
                        <p id="detailObservacoes" style="margin:0.5rem 0 0; color: var(--text-muted);"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var garantiaId = button.getAttribute('data-garantia');
                var equipamentoName = button.getAttribute('data-nome');
                document.getElementById('equipamentoName').textContent = equipamentoName || 'Garantia selecionada';
                document.getElementById('deleteGarantiaId').value = garantiaId;
            });
        }

        var detailsModal = document.getElementById('detailsModal');
        if (detailsModal) {
            detailsModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                document.getElementById('detailEquipamento').textContent = button.getAttribute('data-equipa') || '-';
                document.getElementById('detailFornecedor').textContent = button.getAttribute('data-fornecedor') || '-';
                document.getElementById('detailTipo').textContent = button.getAttribute('data-tipo') || '-';
                document.getElementById('detailInicio').textContent = button.getAttribute('data-inicio') || '-';
                document.getElementById('detailFim').textContent = button.getAttribute('data-fim') || '-';
                document.getElementById('detailStatus').textContent = button.getAttribute('data-status') || '-';
                document.getElementById('detailPeriodicidade').textContent = button.getAttribute('data-periodicidade') || '-';
                document.getElementById('detailCusto').textContent = button.getAttribute('data-custo') || '-';
                document.getElementById('detailObservacoes').textContent = button.getAttribute('data-observacoes') || 'Nenhuma observação registrada.';
            });
        }

        var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                document.getElementById('deleteGarantiaForm').submit();
            });
        }
    });
    </script>
<?php include '../../includes/footer.php'; ?>
