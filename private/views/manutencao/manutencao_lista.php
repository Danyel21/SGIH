<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php require_once '../../includes/auth.php'; ?>
<?php require_once '../../includes/db_data.php'; ?>

<?php
$search = $_GET['search'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_manutencao_id'])) {
    $manutencaoId = intval($_POST['delete_manutencao_id']);
    try {
        if ($manutencaoId <= 0) {
            throw new Exception('ID de manutenção inválido.');
        }

        if ($dbManager->deleteManutencao($manutencaoId)) {
            header('Location: manutencao_lista.php?message=' . urlencode('Registo de manutenção eliminado com sucesso.'));
            exit;
        }

        $error = 'Não foi possível eliminar a manutenção.';
    } catch (Exception $ex) {
        $error = $ex->getMessage();
    }
}

$manutencoes = $dbManager->getManutencoes();

// Aplicar filtros
if (!empty($search) || !empty($tipo)) {
    $searchLower = strtolower($search);
    $manutencoes = array_filter($manutencoes, function($m) use ($searchLower, $tipo) {
        $equipMatch = empty($searchLower) || strpos(strtolower($m['equipamento_manutencao']), $searchLower) !== false || 
                      strpos(strtolower($m['descricao_trabalho']), $searchLower) !== false;
        $tipoMatch = empty($tipo) || $m['tipo_manutencao'] === $tipo;
        return $equipMatch && $tipoMatch;
    });
}

$manutencoesDetalhes = [];
foreach ($manutencoes as $manutencao) {
    $manutencoesDetalhes[$manutencao['id_manutencao']] = [
        'id' => $manutencao['id_manutencao'],
        'equipamento' => $manutencao['equipamento_manutencao'],
        'fornecedor' => $manutencao['fornecedor_manutencao'] ?? 'N/D',
        'tipo' => $manutencao['tipo_manutencao'],
        'data' => $manutencao['data_manutencao'],
        'descricao' => $manutencao['descricao_trabalho'] ?? '',
        'custo' => $manutencao['custo_manutencao'],
        'proximo' => $manutencao['proximo_manutencao_prevista'] ?? null,
        'documentos' => $dbManager->getDocumentosManutencao_by_id($manutencao['id_manutencao'])
    ];
}

function buildDocumentUrl($caminhoArquivo) {
    if (empty($caminhoArquivo)) {
        return '';
    }

    $fileName = basename($caminhoArquivo);
    $relativeUrl = '../../uploads/documentos/' . $fileName;

    if (strpos($caminhoArquivo, '/SGIH/') !== false) {
        return $caminhoArquivo;
    }

    return $relativeUrl;
}
?>

    <!-- Main -->
    <main class="main-content">

        <!-- Header -->
        <header>
            <div class="header-title">
                <h1>Lista de Manutenção</h1>
                <p>Gerencie as intervenções de manutenção dos equipamentos do hospital</p>
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

            <?php if (!empty($search) || !empty($tipo)): ?>
                <div class="alert alert-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #e7f3fe; color: #31708f; border-radius: 0.5rem; border-left: 4px solid #31708f;">
                    <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                    A ver resultados filtrados.
                    <?php if (!empty($search)): ?>
                        <strong>Pesquisa:</strong> <?= ($search) ?>
                    <?php endif; ?>
                    <?php if (!empty($tipo)): ?>
                        | <strong>Tipo:</strong> <?= ($tipo) ?>
                    <?php endif; ?>
                    <a href="manutencao_lista.php" style="margin-left: 1rem; text-decoration: underline;">Limpar Filtros</a>
                </div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total de Registos</h3>
                        <p><?= count($manutencoes) ?> Intervenções</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Documentos</h3>
                        <p><?= array_sum(array_map(fn($m) => !empty($m['documentos']) ? 1 : 0, $manutencoesDetalhes)) ?> registos com ficheiro</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Custo Médio</h3>
                        <p>
                            <?php
                                $totalCusto = array_sum(array_map(fn($m) => floatval($m['custo']), $manutencoesDetalhes));
                                $mediaCusto = count($manutencoesDetalhes) ? $totalCusto / count($manutencoesDetalhes) : 0;
                                echo number_format($mediaCusto, 2, ',', '.');
                            ?> €
                        </p>
                    </div>
                </div>
            </div>

            <form method="GET" class="filters-box">
                <div class="filter-group flex-grow">
                    <label>Pesquisar</label>
                    <input type="text" name="search" value="<?= ($search) ?>" placeholder="Equipamento, descrição...">
                </div>
                <div class="filter-group w-48">
                    <label>Tipo</label>
                    <select name="tipo">
                        <option value="">Todas</option>
                        <option value="Preventiva" <?= ($tipo === 'Preventiva') ? 'selected' : '' ?>>Preventiva</option>
                        <option value="Correctiva" <?= ($tipo === 'Correctiva') ? 'selected' : '' ?>>Correctiva</option>
                        <option value="Urgente" <?= ($tipo === 'Urgente') ? 'selected' : '' ?>>Urgente</option>
                        <option value="Calibracao" <?= ($tipo === 'Calibracao') ? 'selected' : '' ?>>Calibração</option>
                    </select>
                </div>
                <div class="filter-button">
                    <button type="submit">
                        <i class="fas fa-filter"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        <div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
                <a href="manage_manutencao.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nova Manutenção
                </a>
            </div>

            <div class="table-container">

                <div class="table-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-top: 2rem; margin-left: 1rem;">
                    <div>
                        <h3>Intervenções Recentes</h3>
                    </div>
                   
                </div>

              

                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Equipamento</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Custo</th>
                            <th class="actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($manutencoes)): ?>
                            <?php foreach ($manutencoes as $manutencao): ?>
                                <?php $docs = $manutencoesDetalhes[$manutencao['id_manutencao']]['documentos']; ?>
                                <tr>
                                    <td><?= (date('d/m/Y', strtotime($manutencao['data_manutencao']))) ?></td>
                                    <td><?= ($manutencao['equipamento_manutencao']) ?></td>
                                    <td><?= ($manutencao['tipo_manutencao']) ?></td>
                                    <td><?= (mb_strimwidth($manutencao['descricao_trabalho'] ?? '', 0, 70, '...')) ?></td>
                                    <td><?= number_format(floatval($manutencao['custo_manutencao']), 2, ',', '.') ?> €</td>
                                    <td class="actions">
                                        <a href="#" class="view" data-bs-toggle="modal" data-bs-target="#detailsModal" data-manutencao="<?= $manutencao['id_manutencao'] ?>" data-nome="<?= ($manutencao['equipamento_manutencao']) ?>" title="Ver detalhes da manutenção">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                        <a href="manage_manutencao.php?id=<?= $manutencao['id_manutencao'] ?>" class="edit" title="Editar manutenção">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (!empty($docs)): ?>
                                            <?php $downloadUrl = buildDocumentUrl($docs[0]['caminho_arquivo']); ?>
                                            <?php if ($downloadUrl): ?>
                                                <a href="<?= ($downloadUrl) ?>" class="pdf" title="Download documento" target="_blank" rel="noopener">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <a href="#" class="delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-manutencao="<?= $manutencao['id_manutencao'] ?>" data-descricao="<?= ($manutencao['equipamento_manutencao']) ?>">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:1rem;">Nenhuma intervenção encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade modal-details" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">
                        <i class="fas fa-info-exclamation" style="color: var(--danger); margin-right: 0.5rem;"></i>
                        Detalhes da Manutenção
                    </h5>
                </div>
                <div class="modal-body background-light" id="detailsModalBody">
                    <div class="modal-loading" style="padding: 2rem; text-align: center;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; margin-bottom: 1rem;"></i>
                        <p>Carregando detalhes...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times" style="margin-right: 0.5rem;"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle" style="color: var(--danger); margin-right: 0.5rem;"></i>
                        Confirmar Eliminação
                    </h5>
                </div>
                <div class="modal-body background-light">
                    <p>Tem a certeza que deseja eliminar este registo de manutenção?</p>
                    <p style="font-weight: 600; color: var(--text-main);">
                        <i class="fas fa-tools" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                        <span id="manutencaoDeleteName">Nenhum registo selecionado.</span>
                    </p>
                       <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 1rem;">
                        <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times" style="margin-right: 0.5rem;"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash" style="margin-right: 0.5rem;"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteManutencaoForm" method="post" style="display:none;">
        <input type="hidden" name="delete_manutencao_id" id="deleteManutencaoId" value="" />
    </form>

<script>
window.manutencoesDetalhes = <?= json_encode($manutencoesDetalhes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Modal details
    var deleteModalEl = document.getElementById('deleteModal');
    if (deleteModalEl) {
        deleteModalEl.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-manutencao');
            var descricao = button.getAttribute('data-descricao');
            document.getElementById('manutencaoDeleteName').textContent = descricao || 'Registo de manutenção';
            document.getElementById('deleteManutencaoId').value = id;
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            document.getElementById('deleteManutencaoForm').submit();
        });
    }

    var detailsModalEl = document.getElementById('detailsModal');
    if (detailsModalEl) {
        detailsModalEl.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-manutencao');
            var modalTitle = document.getElementById('detailsModalLabel');
            var modalBody = document.getElementById('detailsModalBody');
            var displayName = button.getAttribute('data-nome');

            if (modalTitle) {
                modalTitle.textContent = 'Detalhes: ' + (displayName || 'Manutenção');
            }

            var data = window.manutencoesDetalhes[id];
            if (!data) {
                modalBody.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: var(--danger);">Não foi possível carregar os detalhes.</div>';
                return;
            }

            var documentosHtml = '';
            if (data.documentos && data.documentos.length > 0) {
                documentosHtml += '<div style="margin-top: 1rem;"><strong>Documentos</strong><ul style="padding-left: 1.2rem; margin: 0.5rem 0;">';
                data.documentos.forEach(function(doc) {
                    var url = '../../uploads/documentos/' + encodeURIComponent(doc.caminho_arquivo.split('/').pop());
                    documentosHtml += '<li style="margin-bottom: 0.5rem;">' +
                        '<span>' + (doc.nome_documento || doc.tipo_documento || 'Documento') + '</span>' +
                        ' <a href="' + url + '" target="_blank" rel="noopener" class="btn btn-sm btn-primary" style="margin-left:0.75rem;">' +
                        '<i class="fas fa-download" style="margin-right: 0.35rem;"></i>Baixar</a>' +
                        '</li>';
                });
                documentosHtml += '</ul></div>';
            } else {
                documentosHtml += '<div style="padding: 1rem; color: var(--text-muted);">Nenhum documento associado.</div>';
            }

            var proxima = data.proximo ? data.proximo : 'Não definido';
            var cost = data.custo ? parseFloat(data.custo).toFixed(2).replace('.', ',') + ' €' : '0,00 €';

            modalBody.innerHTML = '<div style="display:grid; gap:1rem;">' +
                '<div style="padding:1rem; background:#fff; border-radius:0.75rem; box-shadow:0 1px 4px rgba(0,0,0,.05);">' +
                    '<p><strong>Equipamento:</strong> ' + (data.equipamento || 'N/D') + '</p>' +
                    '<p><strong>Fornecedor:</strong> ' + (data.fornecedor || 'N/D') + '</p>' +
                    '<p><strong>Tipo:</strong> ' + (data.tipo || 'N/D') + '</p>' +
                    '<p><strong>Data da intervenção:</strong> ' + (data.data ? data.data.split('-').reverse().join('/') : 'N/D') + '</p>' +
                    '<p><strong>Custo:</strong> ' + cost + '</p>' +
                    '<p><strong>Próxima manutenção:</strong> ' + proxima + '</p>' +
                '</div>' +
                '<div style="padding:1rem; background:#fff; border-radius:0.75rem; box-shadow:0 1px 4px rgba(0,0,0,.05);">' +
                    '<p><strong>Descrição do trabalho:</strong></p>' +
                    '<p style="margin-top:0.5rem; color: var(--text-main);">' + (data.descricao || 'Sem descrição adicional.') + '</p>' +
                '</div>' +
                documentosHtml +
            '</div>';
        });
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
