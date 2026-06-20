<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';

requireAdmin();

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? 'todos';

$ativoFilter = null;
if ($status === 'ativos') {
    $ativoFilter = 1;
} elseif ($status === 'inativos') {
    $ativoFilter = 0;
}

$utilizadores = $dbManager->getUtilizadores($ativoFilter, $search);
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Lista de Utilizadores</h1>
                <p>Gerencie os utilizadores do sistema</p>
            </div>
            <?php include '../../includes/user_menu.php'; ?>
        </header>

        <div class="content-wrapper">
            <form method="get" action="utilizador_lista.php" class="filters-box" style="margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <div style="flex:1; min-width:220px;">
                    <label for="search" style="display:block; margin-bottom:0.5rem; font-weight:600;">Pesquisar</label>
                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nome, email ou função" class="form-control">
                </div>
                <div style="width:220px;">
                    <label for="status" style="display:block; margin-bottom:0.5rem; font-weight:600;">Estado</label>
                    <select id="status" name="status" class="form-select">
                        <option value="todos" <?= $status === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="ativos" <?= $status === 'ativos' ? 'selected' : '' ?>>Ativos</option>
                        <option value="inativos" <?= $status === 'inativos' ? 'selected' : '' ?>>Inativos</option>
                    </select>
                </div>
                <div style="display:flex; gap:0.75rem; align-items:center;">
                    <button type="submit" class="btn btn-primary" style="margin-top: 1.8rem;">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="utilizador_lista.php" class="btn btn-secondary" style="margin-top: 1.8rem;">Limpar</a>
                </div>
            </form>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; background: #e6f4ea; color: #1f6f3b; border-radius: 0.5rem; border-left: 4px solid #1f6f3b;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                    <?= ($message) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; background: #fdecea; color: #b02a37; border-radius: 0.5rem; border-left: 4px solid #b02a37;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                    <?= ($error) ?>
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
                <a href="utilizador_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Novo Utilizador
                </a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Função</th>
                            <th>Estado</th>
                            <th>Departamento</th>
                            <th class="actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($utilizadores)): ?>
                            <?php foreach ($utilizadores as $utilizador): ?>
                                <tr>
                                    <td><?= ($utilizador['nome']) ?></td>
                                    <td><?= ($utilizador['email']) ?></td>
                                    <td><?= ($utilizador['funcao']) ?></td>
                                    <td>
                                        <?php if ($utilizador['ativo'] == 1): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= ($utilizador['departamento']) ?></td>
                                    <td class="actions">
                                        <a href="utilizador_form.php?id=<?= $utilizador['id_utilizador'] ?>" class="edit" title="Editar utilizador">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="delete delete-user-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $utilizador['id_utilizador'] ?>" data-nome="<?= htmlspecialchars($utilizador['nome']) ?>" title="Desativar utilizador">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Nenhum utilizador encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteUserForm" method="post" action="utilizador_delete.php">
                    <input type="hidden" name="id" id="deleteUserId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">
                            <i class="fas fa-exclamation-triangle" style="color: var(--danger); margin-right: 0.5rem;"></i>
                            Confirmar Desativação
                        </h5>
                    </div>
                    <div class="modal-body background-light">
                        <p>Tem a certeza que deseja desativar este utilizador?</p>
                        <p style="font-weight: 600; color: var(--text-main);">
                            <i class="fas fa-user" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                            <span id="deleteUserName">Utilizador</span>
                        </p>
                        <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 1rem;">
                            Esta ação não elimina o utilizador definitivamente; apenas o torna inativo.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-user-slash"></i>
                            Desativar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-user-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                document.getElementById('deleteUserName').textContent = this.dataset.nome || 'Utilizador';
                document.getElementById('deleteUserId').value = this.dataset.id || '';
            });
        });
    </script>

<?php include '../../includes/footer.php'; ?>