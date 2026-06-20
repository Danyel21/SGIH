<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';

$id = intval($_GET['id'] ?? 0);
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

$fornecedor = [
    'nome_empresa' => '',
    'nif' => '',
    'tipo_fornecedor' => '',
    'contacto_telefonico' => '',
    'email' => '',
    'morada' => '',
    'codigo_postal' => '',
    'localidade' => '',
    'website' => '',
    'pessoa_contacto' => '',
    'telefone_pessoa_contacto' => '',
    'observacoes' => ''
];

if ($id > 0) {
    $loaded = $dbManager->getFornecedorById($id);
    if ($loaded) {
        $fornecedor = $loaded;
    } else {
        header('Location: fornecedores_lista.php?error=' . urlencode('Fornecedor não encontrado.'));
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
                <h1><?= $id > 0 ? 'Editar Fornecedor' : 'Adicionar Fornecedor' ?></h1>
                <p><?= $id > 0 ? 'Atualize os detalhes do fornecedor' : 'Registe um novo fornecedor' ?></p>
            </div>

        </header>

        <div class="form-card">
            <?php if (!empty($message)): ?>
                <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; background: #e6f4ea; color: #1f6f3b; border-radius: 0.5rem; border-left: 4px solid #1f6f3b;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; background: #fdecea; color: #b02a37; border-radius: 0.5rem; border-left: 4px solid #b02a37;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="formEditarFornecedor" method="post" action="fornecedor_save.php">
                <?php if ($id > 0): ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                <?php endif; ?>

                <div class="form-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="empresa">Empresa *</label>
                            <input type="text" id="empresa" name="empresa" placeholder="Nome da empresa" required value="<?= htmlspecialchars($fornecedor['nome_empresa'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="nif">NIF *</label>
                            <input type="text" id="nif" name="nif" placeholder="NIF do fornecedor" required value="<?= htmlspecialchars($fornecedor['nif'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo *</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Selecione um tipo</option>
                                <option value="Fabricante" <?= ($fornecedor['tipo_fornecedor'] === 'Fabricante') ? 'selected' : '' ?>>Fabricante</option>
                                <option value="Distribuidor" <?= ($fornecedor['tipo_fornecedor'] === 'Distribuidor') ? 'selected' : '' ?>>Distribuidor</option>
                                <option value="Assistência Técnica" <?= ($fornecedor['tipo_fornecedor'] === 'Assistência Técnica') ? 'selected' : '' ?>>Assistência Técnica</option>
                                <option value="Consumíveis" <?= ($fornecedor['tipo_fornecedor'] === 'Consumíveis') ? 'selected' : '' ?>>Consumíveis</option>
                                <option value="Outro" <?= ($fornecedor['tipo_fornecedor'] === 'Outro') ? 'selected' : '' ?>>Outro</option>
                                <?php if (!in_array($fornecedor['tipo_fornecedor'] ?? '', ['Fabricante', 'Distribuidor', 'Assistência Técnica', 'Consumíveis', 'Outro'])): ?>
                                    <option value="<?= ($fornecedor['tipo_fornecedor'] ?? '') ?>" selected><?= htmlspecialchars($fornecedor['tipo_fornecedor'] ?? 'Tipo personalizado') ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contacto">Contacto *</label>
                            <input type="tel" id="contacto" name="contacto" placeholder="Telefone" required value="<?= htmlspecialchars($fornecedor['contacto_telefonico'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" placeholder="email@fornecedor.com" required value="<?= htmlspecialchars($fornecedor['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco" placeholder="Rua, número, cidade" value="<?= htmlspecialchars($fornecedor['morada'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="codigo_postal">Código Postal</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" placeholder="1234-567" value="<?= htmlspecialchars($fornecedor['codigo_postal'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="localidade">Localidade</label>
                            <input type="text" id="localidade" name="localidade" placeholder="Lisboa" value="<?= htmlspecialchars($fornecedor['localidade'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" id="website" name="website" placeholder="https://www.exemplo.com" value="<?= htmlspecialchars($fornecedor['website'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="pessoa_contacto">Pessoa de Contacto</label>
                            <input type="text" id="pessoa_contacto" name="pessoa_contacto" placeholder="Nome do contacto" value="<?= htmlspecialchars($fornecedor['pessoa_contacto'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="telefone_pessoa_contacto">Telefone Pessoa de Contacto</label>
                            <input type="tel" id="telefone_pessoa_contacto" name="telefone_pessoa_contacto" placeholder="+351 9XX XXX XXX" value="<?= htmlspecialchars($fornecedor['telefone_pessoa_contacto'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="observacoes">Observações</label>
                            <textarea id="observacoes" name="observacoes" rows="4" placeholder="Notas adicionais sobre o fornecedor"><?= htmlspecialchars($fornecedor['observacoes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="fornecedores_lista.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar Fornecedor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

<?php include '../../includes/footer.php'; ?>
