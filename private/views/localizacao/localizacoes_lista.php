<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php require_once '../../includes/auth.php'; ?>
<?php require_once '../../includes/db_data.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Lista de Localizações</h1>
                <p>Gerencie as localizações dos equipamentos do hospital</p>
            </div>
  <?php include '../../includes/user_menu.php'; ?>  
        </header>
<div class="content-wrapper">
            <?php
            $message = $_GET['message'] ?? '';
            $error = $_GET['error'] ?? '';
            $filters = [];
            if (!empty($_GET['search'])) {
                $filters['search'] = trim($_GET['search']);
            }
            if (!empty($_GET['servico_departamento']) && $_GET['servico_departamento'] !== 'Todos') {
                $filters['servico_departamento'] = $_GET['servico_departamento'];
            }

            // lista filtrada e lista completa para popular selects
            $localizacoes = $dbManager->getLocalizacoes($filters);
            $equipamentosPorLocalizacao = [];
            $localizacoesDetalhes = [];
            foreach ($localizacoes as $loc) {
                $equipamentos = $dbManager->getLocalizacoesEquipamentos($loc['id_localizacao']);
                $equipamentosPorLocalizacao[$loc['id_localizacao']] = $equipamentos;
                $localizacoesDetalhes[$loc['id_localizacao']] = [
                    'localizacao' => [
                        'piso' => $loc['piso'] ?? '',
                        'servico_departamento' => $loc['servico_departamento'] ?? '',
                        'sala_gabinete' => $loc['sala_gabinete'] ?? ''
                    ],
                    'equipamentos' => $equipamentos
                ];
            }
            $allLocalizacoes = $dbManager->getLocalizacoes();
            $servicos = array_unique(array_column($allLocalizacoes, 'servico_departamento'));
            sort($servicos);
            $totalResults = count($localizacoes);
            ?>
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

                <?php if (!empty($_GET['search']) || !empty($_GET['servico_departamento'])): ?>
                    <div class="alert alert-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #e7f3fe; color: #31708f; border-radius: 0.5rem; border-left: 4px solid #31708f;">
                        <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                    A ver resultados filtrados.
                    <?php if (!empty($_GET['search'])): ?>
                        <strong>Pesquisa:</strong> <?= ($_GET['search']) ?>
                    <?php endif; ?>
                    <?php if (!empty($_GET['servico_departamento'])): ?>
                        | <strong>Serviço / Departamento:</strong> <?= ($_GET['servico_departamento']) ?>
                    <?php endif; ?>

                    </div>
                <?php endif; ?>
            <form method="get" class="filters-box">
                <div class="filter-group flex-grow">
                    <label>Pesquisar</label>
                    <input type="text" name="search" placeholder="Pesquisar localização..." value="<?= ($_GET['search'] ?? '') ?>">
                </div>
                <div class="filter-group w-48">
                    <label>Serviço / Departamento</label>
                    <select name="servico_departamento">
                        <option>Todos</option>
                        <?php foreach ($servicos as $s): ?>
                            <option value="<?= ($s) ?>" <?= (isset($_GET['servico_departamento']) && $_GET['servico_departamento']==$s) ? 'selected' : '' ?>><?= ($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-button">
                    <button type="submit">
                        <i class="fas fa-filter"></i>
                        Filtrar
                    </button>
                </div>
            </form>

            <!-- Botão para Nova Localização -->
            <div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
                <a href="localizacao_edit.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nova Localização
                </a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Edifício</th>
                            <th>Piso</th>
                            <th>Serviço / Departamento</th>
                            <th>Sala / Gabinete</th>
                            <th>Equipamentos</th>
                            <th class="actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($localizacoes)): ?>
                            <?php foreach ($localizacoes as $loc): ?>
                                <tr>
                                    <td><?= ($loc['edificio'] ?? 'N/D') ?></td>
                                    <td><?= ($loc['piso'] ?? '') ?></td>
                                    <td><?= ($loc['servico_departamento'] ?? '') ?></td>
                                    <td><?= ($loc['sala_gabinete'] ?? '') ?></td>
                                    <td><?= ($loc['equipamentos_count'] ?? 0) ?></td>
                                    <?php $displayName = (($loc['edificio'] ?? '') . ' - Piso ' . ($loc['piso'] ?? '') . ' - ' . ($loc['servico_departamento'] ?? '') . ' - ' . ($loc['sala_gabinete'] ?? '')); ?>
                                    <td class="actions">
                                        <a href="#" class="view details" data-bs-toggle="modal" data-bs-target="#detailsModal" data-localizacao="<?= $loc['id_localizacao'] ?>" data-nome="<?= $displayName ?>" title="Ver detalhes">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                        <a href="localizacao_edit.php?id=<?= $loc['id_localizacao'] ?>" class="edit" title="Editar localização">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-localizacao="<?= $loc['id_localizacao'] ?>" data-nome="<?= $displayName ?>" title="Eliminar localização">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:1rem;">Nenhuma localização encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <script>
                window.localizacoesDetalhes = <?= json_encode($localizacoesDetalhes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            </script>
          <!-- Pagination -->
            <div class="pagination-wrapper">

                <div class="pagination-info">
                    Mostrando <?= $totalResults ?> resultados
                </div>

                <div class="pagination">

                    <button>Anterior</button>

                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>

                    <button>Próximo</button>

                </div>

            </div>

        </div>
    </main>

    <!-- Modal de Detalhes da Localização -->
    <div class="modal fade modal-details" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">
                        <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                        Detalhes da Localização
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

    <!-- Modal de Confirmação de Eliminação -->
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
                    <p>Tem a certeza que deseja eliminar esta localização?</p>
                    <p style="font-weight: 600; color: var(--text-main);">
                        <i class="fas fa-map-marker-alt" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                        <span id="localizacaoName">Edifício A - Piso 1 - UCI - Sala 04</span>
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    var deleteModalEl = document.getElementById('deleteModal');
    if (deleteModalEl) {
        deleteModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-localizacao');
            var nome = button.getAttribute('data-nome');
            document.getElementById('localizacaoName').textContent = nome;
            document.getElementById('confirmDeleteBtn').setAttribute('data-id', id);
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            if (!id) return;
            var btn = this;
            btn.disabled = true;
            fetch('delete_localizacao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btn.disabled = false;
                if (data.success) {
                    var modal = bootstrap.Modal.getInstance(deleteModalEl);
                    if (modal) modal.hide();
                    var rowLink = document.querySelector('a.delete[data-localizacao="' + id + '"]');
                    if (rowLink) {
                        var tr = rowLink.closest('tr');
                        if (tr) tr.remove();
                    }
                } else {
                    alert(data.message || 'Erro ao eliminar');
                }
            })
            .catch(function() {
                btn.disabled = false;
                alert('Erro de rede');
            });
        });
    }

    var detailsModalEl = document.getElementById('detailsModal');
    if (detailsModalEl) {
        detailsModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-localizacao');
            var modalBody = document.getElementById('detailsModalBody');
            var modalTitle = document.getElementById('detailsModalLabel');
            var displayName = button.getAttribute('data-nome');
            if (modalTitle) {
                modalTitle.textContent = displayName || 'Detalhes da localização';
            }

            if (!window.localizacoesDetalhes || !window.localizacoesDetalhes[id]) {
                modalBody.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: var(--danger);">Não foi possível carregar os detalhes.</div>';
                return;
            }

            var data = window.localizacoesDetalhes[id];
            var equipamentos = data.equipamentos || [];
            var estados = {};
            var categorias = {};

            equipamentos.forEach(function(e) {
                var estado = e.nome_estado || 'Desconhecido';
                var categoria = e.nome_categoria || 'Desconhecida';
                estados[estado] = (estados[estado] || 0) + 1;
                categorias[categoria] = (categorias[categoria] || 0) + 1;
            });

            var locationHtml = '<div style="margin-bottom: 1rem;">' +
                '<p style="margin:0.25rem 0;"><strong>Localização:</strong> ' + (data.localizacao.edificio || 'N/D') + ' - Piso ' + (data.localizacao.piso || 'N/D') + '</p>' +
                '<p style="margin:0.25rem 0;"><strong>Serviço / Departamento:</strong> ' + (data.localizacao.servico_departamento || 'N/D') + '</p>' +
                '<p style="margin:0.25rem 0;"><strong>Sala / Gabinete:</strong> ' + (data.localizacao.sala_gabinete || 'N/D') + '</p>' +
                '<p style="margin:0.25rem 0;"><strong>Total de equipamentos:</strong> ' + equipamentos.length + '</p>' +
            '</div>';

            var summaryHtml = '<div style="display:grid; gap:0.75rem; margin-bottom:1rem; padding:1rem; background:#f8fbff; border-radius:0.75rem;">' +
                '<div><strong>Resumo de Equipamentos</strong></div>';

            if (Object.keys(estados).length > 0) {
                summaryHtml += '<div>';
                for (var estado in estados) {
                    summaryHtml += '<span style="display:inline-block; margin-right:1rem;"><strong>' + estado + ':</strong> ' + estados[estado] + '</span>';
                }
                summaryHtml += '</div>';
            }

            if (Object.keys(categorias).length > 0) {
                summaryHtml += '<div>';
                for (var categoria in categorias) {
                    summaryHtml += '<span style="display:inline-block; margin-right:1rem;"><strong>' + categoria + ':</strong> ' + categorias[categoria] + '</span>';
                }
                summaryHtml += '</div>';
            }

            summaryHtml += '</div>';

            var equipamentosHtml = '';
            if (equipamentos.length > 0) {
                equipamentosHtml += '<div style="overflow-x:auto;"><table class="custom-table" style="width:100%; margin-top:0.5rem;">' +
                    '<thead>' +
                        '<tr>' +
                            '<th>Cód. Interno</th>' +
                            '<th>Designação</th>' +
                            '<th>Categoria</th>' +
                            '<th>Estado</th>' +
                            '<th>Criticidade</th>' +
                        '</tr>' +
                    '</thead>' +
                    '<tbody>';

                equipamentos.forEach(function(e) {
                    equipamentosHtml += '<tr>' +
                        '<td>' + (e.codigo_interno || '-') + '</td>' +
                        '<td>' + (e.designacao || '-') + '</td>' +
                        '<td>' + (e.nome_categoria || '-') + '</td>' +
                        '<td>' + (e.nome_estado || '-') + '</td>' +
                        '<td>' + (e.nivel_criticidade || '-') + '</td>' +
                        '</tr>';
                });

                equipamentosHtml += '</tbody></table></div>';
            } else {
                equipamentosHtml = '<div style="padding:1rem; text-align:center; color: var(--text-muted);">Nenhum equipamento encontrado nesta localização.</div>';
            }

            modalBody.innerHTML = locationHtml + summaryHtml + equipamentosHtml;
        });
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
