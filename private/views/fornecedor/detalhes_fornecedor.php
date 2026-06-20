<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_data.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: fornecedores_lista.php?error=' . urlencode('ID de fornecedor inválido.'));
    exit;
}

$fornecedor = $dbManager->getFornecedorById($id);
if (!$fornecedor) {
    header('Location: fornecedores_lista.php?error=' . urlencode('Fornecedor não encontrado.'));
    exit;
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Detalhes do Fornecedor</h1>
                <p>Visualize informações completas do fornecedor selecionado</p>
            </div>
            <div class="header-actions" style="display:flex; gap:0.75rem; align-items:center;">
                <a href="editar_fornecedor.php?id=<?= $fornecedor['id_fornecedor'] ?>" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-edit"></i>
                    Editar
                </a>
                <a href="fornecedores_lista.php" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </header>

        <div class="form-card" style="padding: 2rem;">
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Empresa</label>
                    <div class="detail-value"><?= ($fornecedor['nome_empresa']) ?></div>
                </div>
                <div class="form-group">
                    <label>NIF</label>
                    <div class="detail-value"><?= ($fornecedor['nif']) ?></div>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <div class="detail-value"><?= ($fornecedor['tipo_fornecedor']) ?></div>
                </div>
                <div class="form-group">
                    <label>Contacto</label>
                    <div class="detail-value"><?= ($fornecedor['contacto_telefonico']) ?></div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <div class="detail-value"><?= ($fornecedor['email']) ?></div>
                </div>
                <div class="form-group">
                    <label>Endereço</label>
                    <div class="detail-value"><?= ($fornecedor['morada']) ?></div>
                </div>
                <div class="form-group">
                    <label>Código Postal</label>
                    <div class="detail-value"><?= ($fornecedor['codigo_postal']) ?></div>
                </div>
                <div class="form-group">
                    <label>Localidade</label>
                    <div class="detail-value"><?= ($fornecedor['localidade']) ?></div>
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <div class="detail-value"> <?= ($fornecedor['website']) ?: '<span style="color: #6c757d;">Não especificado</span>' ?></div>
                </div>
                <div class="form-group">
                    <label>Pessoa de Contacto</label>
                    <div class="detail-value"> <?= ($fornecedor['pessoa_contacto']) ?: '<span style="color: #6c757d;">Não especificado</span>' ?></div>
                </div>
                <div class="form-group">
                    <label>Telefone Pessoa de Contacto</label>
                    <div class="detail-value"> <?= ($fornecedor['telefone_pessoa_contacto']) ?: '<span style="color: #6c757d;">Não especificado</span>' ?></div>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Observações</label>
                    <div class="detail-value" style="white-space: pre-wrap; min-height: 4rem; background: #fff; padding: 0.75rem; border: 1px solid #dcdcdc; border-radius: 0.5rem;">
                        <?php if ($fornecedor['observacoes']): ?>
                            <?= nl2br(($fornecedor['observacoes'])) ?>
                        <?php else: ?>
                            <span style="color: #6c757d;">Sem observações</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include '../../includes/footer.php'; ?>