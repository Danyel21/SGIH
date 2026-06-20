<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';

requireAdmin();

$id = intval($_GET['id'] ?? 0);
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

$utilizador = [
    'nome' => '',
    'email' => '',
    'funcao' => '',
    'ativo' => 1
];

if ($id > 0) {
    $loaded = $dbManager->getUtilizadorById($id);
    if ($loaded) {
        $utilizador = $loaded;
    } else {
        header('Location: utilizador_lista.php?error=' . urlencode('Utilizador não encontrado.'));
        exit;
    }
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1><?= $id > 0 ? 'Editar Utilizador' : 'Registar Utilizador' ?></h1>
                <p><?= $id > 0 ? 'Atualize os dados do utilizador' : 'Adicione um novo utilizador ao sistema' ?></p>
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

            <form method="post" action="utilizador_save.php">
                <?php if ($id > 0): ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                <?php endif; ?>
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
                            <label for="funcao">Função *</label>
                            <select id="funcao" name="funcao" required>
                                <option value="">Selecione uma função</option>
                                <option value="Administrador" <?= $utilizador['funcao'] === 'Administrador' || $utilizador['funcao'] === 'Admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="Técnico" <?= $utilizador['funcao'] === 'Técnico' ? 'selected' : '' ?>>Técnico</option>
                                <option value="Enfermagem" <?= $utilizador['funcao'] === 'Enfermagem' ? 'selected' : '' ?>>Enfermagem</option>
                                <option value="Administrativo" <?= $utilizador['funcao'] === 'Administrativo' ? 'selected' : '' ?>>Administrativo</option>
                                <option value="Outro" <?= $utilizador['funcao'] === 'Outro' ? 'selected' : '' ?>>Outra função</option>
                                <?php if (!in_array($utilizador['funcao'] ?? '', ['Administrador', 'Admin', 'Técnico', 'Enfermagem', 'Administrativo', 'Outro'])): ?>
                                    <option value="<?= ($utilizador['funcao'] ?? '') ?>" selected><?= ($utilizador['funcao'] ?? 'Função personalizada') ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    
                                            <div class="form-group">
                            <label for="departamento">Departamento</label>
                            <input type="text" id="departamento" name="departamento" placeholder="Departamento do utilizador" value="<?= ($utilizador['departamento'] ?? '') ?>">
                        </div>


                        <div class="form-group">
    <label for="password"><?= $id > 0 ? 'Nova Palavra-passe (opcional)' : 'Palavra-passe *' ?></label>

    <div style="display:flex; gap:10px;">
        <input type="text" id="password" name="password"
               <?= $id > 0 ? '' : 'required' ?>
               placeholder="Defina uma palavra-passe segura">

        <button type="button" class="btn btn-secondary" onclick="gerarPassword()">
            Gerar
        </button>
    </div>

    <?php if ($id > 0): ?>
        <small class="form-text text-muted">Preencha apenas se quiser alterar a palavra-passe.</small>
    <?php endif; ?>
</div>
                        <div class="form-group">
                            <label for="ativo">Estado *</label>
                            <select id="ativo" name="ativo" required>
                                <option value="1" <?= $utilizador['ativo'] == 1 ? 'selected' : '' ?>>Ativo</option>
                                <option value="0" <?= $utilizador['ativo'] == 0 ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-actions">
                        <a href="utilizador_lista.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <?= $id > 0 ? 'Guardar Alterações' : 'Registar Utilizador' ?>
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

    const primeiraParte = nome
        .replace(/\s+/g, '')
        .substring(0, 4);

    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
    let aleatorio = '';

    for (let i = 0; i < 8; i++) {
        aleatorio += caracteres.charAt(
            Math.floor(Math.random() * caracteres.length)
        );
    }

    document.getElementById('password').value =
        primeiraParte + aleatorio;
}
</script>
<?php include '../../includes/footer.php'; ?>