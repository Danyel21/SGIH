<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';

requireAuthentication();

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
$userId = getAuthenticatedUserId();

if (empty($userId)) {
    header('Location: ../../public/logout.php');
    exit;
}

$utilizador = $dbManager->getUtilizadorById($userId);
if (!$utilizador) {
    header('Location: ../../public/logout.php');
    exit;
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Perfil</h1>
                <p>Atualize o seu nome, email ou palavra-passe.</p>
            </div>
            <?php include '../../includes/user_menu.php'; ?>
        </header>

        <div class="form-card">
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

            <form method="post" action="perfil_save.php">
                <input type="hidden" name="id" value="<?= intval($utilizador['id_utilizador']) ?>">
                <div class="form-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nome">Nome *</label>
                            <input type="text" id="nome" name="nome" placeholder="Nome completo" required value="<?= ($utilizador['nome']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" placeholder="usuario@hospital.pt" required value="<?= ($utilizador['email']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Nova Palavra-passe</label>
                            <div style="display:flex; gap:10px;">
                                <input type="text" id="password" name="password" placeholder="Deixe vazio para manter a atual">
                                <button type="button" class="btn btn-secondary" onclick="gerarPassword()">
                                    Gerar
                                </button>
                            </div>
                            <small class="form-text text-muted">Preencha apenas se quiser alterar a palavra-passe.</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="../../dashboard/dashboard_admin.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar Perfil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

<script>
    function gerarPassword() {
        const nome = document.getElementById('nome').value.trim();

        if (!nome) {
            alert('Preencha primeiro o nome.');
            return;
        }

        const primeiraParte = nome.replace(/\s+/g, '').substring(0, 4);
        const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
        let aleatorio = '';

        for (let i = 0; i < 8; i++) {
            aleatorio += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
        }

        document.getElementById('password').value = primeiraParte + aleatorio;
    }
</script>
<?php include '../../includes/footer.php'; ?>