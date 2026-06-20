<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

// Receber filtros
$search = $_POST['search'] ?? $_GET['search'] ?? '';
$tipo = $_POST['tipo'] ?? $_GET['tipo'] ?? '';

// Aplicar filtros se existirem
if (!empty($search) || !empty($tipo)) {
    $filtros = [
        'search' => $search,
        'tipo' => $tipo
    ];
    $fornecedores = $dbManager->getFornecedoresComFiltro($filtros);
} else {
    $fornecedores = $dbManager->getFornecedores();
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Lista de Fornecedores</h1>
                <p>Gerencie os fornecedores do hospital</p>
            </div>
         
                    <?php include '../../includes/user_menu.php'; ?>

        </header>

      

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
                <div class="alert alert-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #e7f3ff; color: #0051ba; border-radius: 0.5rem; border-left: 4px solid #0051ba;">
                    <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>
                    A ver resultados filtrados.
                    <?php if (!empty($search)): ?>
                        <strong>Pesquisa:</strong> <?= ($search) ?>
                    <?php endif; ?>
                    <?php if (!empty($tipo)): ?>
                        | <strong>Tipo:</strong> <?= ($tipo) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
           
                <form method="post" class="filters-box" action="fornecedores_lista.php" >
                     <div class="filter-group flex-grow">
                        <label>Pesquisar</label>
                        <input type="text" name="search"  value="<?= ($search) ?>" placeholder="Pesquisar por empresa, NIF, email ou contacto...">
                    </div>
                    <div class="filter-group w-48">
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="">Todos</option>
                            <option value="Fabricante" <?= $tipo === 'Fabricante' ? 'selected' : '' ?>>Fabricante</option>
                            <option value="Distribuidor" <?= $tipo === 'Distribuidor' ? 'selected' : '' ?>>Distribuidor</option>
                            <option value="Assistência Técnica" <?= $tipo === 'Assistência Técnica' ? 'selected' : '' ?>>Assistência Técnica</option>
                            <option value="Consumíveis" <?= $tipo === 'Consumíveis' ? 'selected' : '' ?>>Consumíveis</option>
                            <option value="Outro" <?= $tipo === 'Outro' ? 'selected' : '' ?>>Outro</option>
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
                    <a href="editar_fornecedor.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Novo Fornecedor
                    </a>
                </div>
         <div class="table-container">
            <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>NIF</th>
                                <th>Tipo</th>
                                <th>Contacto</th>
                                <th>Email</th>
                                <th class="actions">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($fornecedores)): ?>
                                <?php foreach ($fornecedores as $fornecedor): ?>
                                    <tr>
                                        <td><?= ($fornecedor['nome_empresa']) ?></td>
                                        <td><?= ($fornecedor['nif']) ?></td>
                                        <td><?= ($fornecedor['tipo_fornecedor']) ?></td>
                                        <td><?= ($fornecedor['contacto_telefonico']) ?></td>
                                        <td><?= ($fornecedor['email']) ?></td>
                                        <td class="actions">
                                            <a href="detalhes_fornecedor.php?id=<?= $fornecedor['id_fornecedor'] ?>" class="view" title="Detalhes fornecedor">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            <a href="editar_fornecedor.php?id=<?= $fornecedor['id_fornecedor'] ?>" class="edit" title="Editar fornecedor">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="delete delete-fornecedor-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $fornecedor['id_fornecedor'] ?>" data-nome="<?= htmlspecialchars($fornecedor['nome_empresa']) ?>" title="Eliminar fornecedor">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align:center;">Nenhum fornecedor encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Modal de eliminação -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="deleteFornecedorForm" method="post" action="delete_fornecedor.php">
                        <input type="hidden" name="id" id="deleteFornecedorId">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">
                                <i class="fas fa-exclamation-triangle" style="color: var(--danger); margin-right: 0.5rem;"></i>
                                Confirmar Eliminação
                            </h5>
                        </div>
                        <div class="modal-body background-light">
                            <p>Tem a certeza que deseja eliminar este fornecedor?</p>
                            <p style="font-weight: 600; color: var(--text-main);">
                                <i class="fas fa-truck" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                                <span id="fornecedorName">Fornecedor</span>
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
                            <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                                <i class="fas fa-trash" style="margin-right: 0.5rem;"></i>
                                Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.querySelectorAll('.delete-fornecedor-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    var fornecedorName = this.dataset.nome;
                    var fornecedorId = this.dataset.id;
                    document.getElementById('fornecedorName').textContent = fornecedorName;
                    document.getElementById('deleteFornecedorId').value = fornecedorId;
                });
            });
        </script>

<?php include '../../includes/footer.php'; ?>
