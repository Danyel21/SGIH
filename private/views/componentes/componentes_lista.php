<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../includes/auth.php'; ?>
<?php require_once __DIR__ . '/../../includes/db_data.php'; ?>

<?php
$search = $_GET['search'] ?? '';
$id_equipamento = $_GET['id_equipamento'] ?? '';
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_componente_id'])) {
    $idDel = intval($_POST['delete_componente_id']);
    try {
        if ($idDel <= 0) throw new Exception('ID inválido');
        if ($dbManager->deleteComponente($idDel)) {
            header('Location: componentes_lista.php?message=' . urlencode('Componente eliminado com sucesso.'));
            exit;
        }
        $error = 'Não foi possível eliminar o componente.';
    } catch (Exception $ex) {
        $error = $ex->getMessage();
    }
}

$componentes = $dbManager->getComponentesEquipamento() ?? [];
// aplicar filtros simples
if (!empty($search) || !empty($id_equipamento)) {
    $componentes = array_filter($componentes, function($c) use ($search, $id_equipamento) {
        $matchSearch = $search === '' || stripos(($c['designacao_componente'] ?? ''), $search) !== false || stripos(($c['codigo_interno_componente'] ?? ''), $search) !== false;
        $matchEquip = $id_equipamento === '' || (isset($c['id_equipamento_principal']) && $c['id_equipamento_principal'] == $id_equipamento);
        return $matchSearch && $matchEquip;
    });
}

$equipamentos = $dbManager->getEquipamentos() ?? [];
?>

<main class="main-content">
    <header>
        <div class="header-title">
            <h1>Lista de Componentes</h1>
            <p>Gerencie os componentes associados aos equipamentos</p>
        </div>
        <?php include __DIR__ . '/../../includes/user_menu.php'; ?>
    </header>
    <div class="content-wrapper">

        <form method="GET" class="filters-box">
            <div class="filter-group flex-grow">
                <label>Pesquisar</label>
                <input type="text" name="search" value="<?= ($search) ?>" placeholder="Código, Designação...">
            </div>

            <div class="filter-group w-48">
                <label>Equipamento</label>
                <select name="id_equipamento">
                    <option value="">Todos</option>
                    <?php foreach ($equipamentos as $e): ?>
                        <option value="<?= $e['id_equipamento'] ?>" <?= ($id_equipamento == $e['id_equipamento']) ? 'selected' : '' ?>><?= ($e['designacao']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-button">
                <button type="submit"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem; padding:1rem; background:#e6f4ea; color:#1f6f3b; border-radius:0.5rem;">
                <i class="fas fa-check-circle" style="margin-right:0.5rem;"></i>
                <?= $message ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding:1rem; background:#fdecea; color:#b02a37; border-radius:0.5rem;">
                <i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($search) || !empty($id_equipamento)): ?>
            <div class="alert alert-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #e7f3fe; color: #31708f; border-radius: 0.5rem; border-left: 4px solid #31708f;">
                <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                A ver resultados filtrados.
                <?php if (!empty($search)): ?><strong> Pesquisa:</strong> <?= ($search) ?><?php endif; ?>
                <?php if (!empty($id_equipamento)): ?> | <strong>Equipamento:</strong> <?php
                    foreach ($equipamentos as $e) { if ($e['id_equipamento']==$id_equipamento) { echo ($e['designacao']); break; } }
                ?><?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 2rem; display:flex; justify-content:flex-end;">
            <a href="componente_form.php" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Componente</a>
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Cód. Interno</th>
                        <th>Designação</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Equipamento</th>
                        <th class="actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($componentes)): ?>
                        <?php foreach ($componentes as $comp): ?>
                            <tr>
                                <td class="code"><?= ($comp['codigo_interno_componente'] ?? '-') ?></td>
                                <td><?= ($comp['designacao_componente'] ?? '-') ?></td>
                                <td><?= ($comp['marca_componente'] ?? '-') ?></td>
                                <td><?= ($comp['modelo_componente'] ?? '-') ?></td>
                                <td><?= ($comp['equipamento_principal'] ?? '-') ?></td>
                                <td class="actions">
                                    <a href="#" class="view-equipment" data-id="<?= $comp['id_componente'] ?>" title="Ver equipamento"><i class="fas fa-info-circle"></i>
                                </a>
                                    <a href="componente_form.php?id_componente=<?= $comp['id_componente'] ?>" class="edit" title="Editar"><i class="fas fa-edit"></i>
                                </a>
                                    <a href="#" class="delete" data-id="<?= $comp['id_componente'] ?>" data-nome="<?= ($comp['designacao_componente'] ?? '') ?>" title="Eliminar"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Nenhum componente registado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination placeholder -->
        <div class="pagination-wrapper">
            <div class="pagination-info">Mostrando 1 a 10 de <?= count($componentes) ?> resultados</div>
            <div class="pagination"></div>
        </div>
    </div>
</main>

<!-- Modal equipamento (detalhe) -->
<div class="modal fade modal-details" id="equipModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-info-exclamation"
                            style="color: var(--danger); margin-right: 0.5rem;"></i>Detalhe do Equipamento</h5>
      </div>
      <div class="modal-body" id="equipModalBody">
        <div>Carregando...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal delete -->
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
                    <p>Tem a certeza que deseja eliminar este registo de componente?</p>
                    <p style="font-weight: 600; color: var(--text-main);">
                        <i class="fas fa-tools" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                        <span id="componenteDeleteName">Nenhum registo selecionado.</span>
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

<form id="deleteCompForm" method="post" style="display:none;">
    <input type="hidden" name="delete_componente_id" id="deleteComponenteId" value="" />
</form>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // View equipment details for a component
    document.querySelectorAll('.view-equipment').forEach(btn => {
        btn.addEventListener('click', function(){
            var compId = this.getAttribute('data-id');
            // fetch equipamento JSON by componente id via endpoint
            fetch('equipamento_json.php?id_comp=' + encodeURIComponent(compId))
                .then(r => r.json())
                .then(data => {
                    var html = '<dl class="row">';
                    html += '<dt class="col-sm-4">Designação</dt><dd class="col-sm-8">' + (data.designacao||'-') + '</dd>';
                    html += '<dt class="col-sm-4">Código Interno</dt><dd class="col-sm-8">' + (data.codigo_interno||'-') + '</dd>';
                    html += '<dt class="col-sm-4">Marca / Modelo</dt><dd class="col-sm-8">' + (data.marca||'-') + ' / ' + (data.modelo||'-') + '</dd>';
                    html += '<dt class="col-sm-4">Localização</dt><dd class="col-sm-8">' + ((data.localizacao_servico||'') + (data.localizacao_sala? ' - '+data.localizacao_sala : '')) + '</dd>';
                    html += '</dl>';
                    document.getElementById('equipModalBody').innerHTML = html;
                    var modal = new bootstrap.Modal(document.getElementById('equipModal'));
                    modal.show();
                }).catch(err => {
                    document.getElementById('equipModalBody').innerHTML = '<div class="alert alert-danger">Não foi possível carregar os detalhes.</div>';
                    var modal = new bootstrap.Modal(document.getElementById('equipModal'));
                    modal.show();
                });
        });
    });

    // Delete
    var deleteId = null;
    document.querySelectorAll('.delete').forEach(btn => {
        btn.addEventListener('click', function(){
            deleteId = this.getAttribute('data-id');
            document.getElementById('componenteDeleteName').textContent = this.getAttribute('data-nome') || '';
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function(){
        if (deleteId) {
            document.getElementById('deleteComponenteId').value = deleteId;
            document.getElementById('deleteCompForm').submit();
        }
    });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
