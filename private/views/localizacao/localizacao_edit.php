<?php
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';

$id = intval($_GET['id'] ?? 0);
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

$localizacao = [
    'edificio' => '',
    'piso' => '',
    'servico_departamento' => '',
    'sala_gabinete' => ''
];

if ($id > 0) {
    $loaded = $dbManager->getLocalizacaoById($id);
    if ($loaded) {
        $localizacao = $loaded;
    } else {
        header('Location: localizacoes_lista.php?error=' . urlencode('Localização não encontrada.'));
        exit;
    }
}
?>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1><?= $id > 0 ? 'Editar Localização' : 'Adicionar Localização' ?></h1>
                <p><?= $id > 0 ? 'Atualize os dados desta localização' : 'Adicione uma nova localização ao sistema' ?></p>
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

            <form id="formNovaLocalizacao" method="post" action="localizacao_save.php">
                <?php if ($id > 0): ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                <?php endif; ?>
                <div class="form-body">
                    
                    <!-- Informações da Localização -->
                    <div>
                        <h3 class="form-section-title">
                            <i class="fas fa-map-marker-alt" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                            Informações da Localização
                        </h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="edificio">Edifício *</label>
                                <select id="edificio" name="edificio" required>
                                    <option value="">Selecione um edifício</option>
                                    <option value="Edifício A" <?= $localizacao['edificio'] === 'Edifício A' ? 'selected' : '' ?>>Edifício A</option>
                                    <option value="Edifício B" <?= $localizacao['edificio'] === 'Edifício B' ? 'selected' : '' ?>>Edifício B</option>
                                    <option value="Edifício C" <?= $localizacao['edificio'] === 'Edifício C' ? 'selected' : '' ?>>Edifício C</option>
                                    <option value="Edifício D" <?= $localizacao['edificio'] === 'Edifício D' ? 'selected' : '' ?>>Edifício D</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="piso">Piso *</label>
                                <select id="piso" name="piso" required>
                                    <option value="">Selecione um piso</option>
                                    <?php for ($i = 0; $i <= 10; $i++) {
                                        $label = $i === 0 ? 'Térreo' : "Piso $i";
                                        echo "<option value=\"$i\" " . ($localizacao['piso'] === (string)$i ? 'selected' : '') . ">$label</option>";
                                    } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="departamento">Serviço / Departamento *</label>
                                <select id="departamento" name="departamento" required>
                                    <option value="">Selecione um departamento</option>
                                    <option value="UCI" <?= $localizacao['servico_departamento'] === 'UCI' ? 'selected' : '' ?>>UCI</option>
                                    <option value="Bloco Operatório" <?= $localizacao['servico_departamento'] === 'Bloco Operatório' ? 'selected' : '' ?>>Bloco Operatório</option>
                                    <option value="Pediatria" <?= $localizacao['servico_departamento'] === 'Pediatria' ? 'selected' : '' ?>>Pediatria</option>
                                    <option value="Cardiologia" <?= $localizacao['servico_departamento'] === 'Cardiologia' ? 'selected' : '' ?>>Cardiologia</option>
                                    <option value="Radiologia" <?= $localizacao['servico_departamento'] === 'Radiologia' ? 'selected' : '' ?>>Radiologia</option>
                                    <option value="Laboratório" <?= $localizacao['servico_departamento'] === 'Laboratório' ? 'selected' : '' ?>>Laboratório</option>
                                    <option value="Farmácia" <?= $localizacao['servico_departamento'] === 'Farmácia' ? 'selected' : '' ?>>Farmácia</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sala">Sala / Gabinete *</label>
                                <input type="text" id="sala" name="sala" placeholder="Ex: Sala 04, Gabinete 02" value="<?= ($localizacao['sala_gabinete'] ?? '') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="descricao">Descrição (Opcional)</label>
                                <textarea id="descricao" name="descricao" placeholder="Detalhes adicionais sobre esta localização" rows="4"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="coordenadas">Coordenadas / Referência *</label>
                                <input type="text" id="coordenadas" name="coordenadas" placeholder="Ex: A-2-04, B-1-Cirurgia" >
                            </div>
                        </div>
                    </div>

                    <!-- Informações Adicionais -->
                    <div>
                        <h3 class="form-section-title">
                            <i class="fas fa-cog" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                            Informações Adicionais
                        </h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tipo">Tipo de Localização</label>
                                <select id="tipo" name="tipo">
                                    <option value="sala">Sala</option>
                                    <option value="gabinete">Gabinete</option>
                                    <option value="armazem">Armazém</option>
                                    <option value="corredor">Corredor</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="capacidade">Capacidade (Número de Equipamentos)</label>
                                <input type="number" id="capacidade" name="capacidade" min="0" placeholder="Ex: 10">
                            </div>

                            <div class="form-group">
                                <label for="responsavel">Responsável</label>
                                <input type="text" id="responsavel" name="responsavel" placeholder="Nome do responsável da área">
                            </div>

                            <div class="form-group">
                                <label for="telefone">Telefone Direto</label>
                                <input type="tel" id="telefone" name="telefone" placeholder="+351 2XX XXX XXX">
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="form-actions">
                        <a href="localizacoes_lista.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar Localização
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </main>

<?php include '../../includes/footer.php'; ?>
