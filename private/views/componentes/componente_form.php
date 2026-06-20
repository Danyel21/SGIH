<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../includes/auth.php'; ?>
<?php require_once __DIR__ . '/../../includes/db_data.php'; ?>
<?php require_once __DIR__ . '/../../includes/insert_db.php'; ?>

<?php
$message = '';
$error = '';
$id_componente = $_GET['id_componente'] ?? null;
$editing = !empty($id_componente);

$componente = [];
if ($editing) {
    $componente = $dbManager->getComponenteById(intval($id_componente));
}

// Lista de equipamentos para associar
$equipamentos = $dbManager->getEquipamentos() ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_equip = intval($_POST['id_equipamento'] ?? 0);
    $codigo = trim($_POST['codigo_interno'] ?? '');
    $design = trim($_POST['designacao'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $numserie = trim($_POST['numero_serie'] ?? '');
    $dataaq = $_POST['data_aquisicao'] ?? null;
    $obs = trim($_POST['observacoes'] ?? '');

    try {
        if ($editing) {
            $db_insert = new DatabaseInsert($pdo);
            if ($db_insert->updateComponente(intval($id_componente), $codigo, $design, $marca, $modelo, $numserie, $dataaq, $obs)) {
                $message = 'Componente atualizado com sucesso.';
            } else {
                $error = 'Erro ao atualizar componente.';
            }
        } else {
            $db_insert = new DatabaseInsert($pdo);
            if ($db_insert->insertComponente($id_equip, $codigo, $design, $marca, $modelo, $numserie, $dataaq, $obs)) {
                header('Location: componentes_lista.php?message=' . urlencode('Componente criado.'));
                exit;
            } else {
                $error = 'Erro ao inserir componente.';
            }
        }
    } catch (Exception $ex) {
        $error = $ex->getMessage();
    }
}
?>

<main class="main-content">
    <header>
        <div class="header-title">
            <h1><?= $editing ? 'Editar Componente' : 'Novo Componente' ?></h1>
            <p><?= $editing ? 'Atualize os dados do componente associado ao equipamento.' : 'Registe um novo componente para um equipamento existente.' ?></p>
        </div>
        <?php include __DIR__ . '/../../includes/user_menu.php'; ?>
    </header>

    <div class="content-wrapper">

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="post">
            <div class="form-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Equipamento associado</label>
                        <select name="id_equipamento" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($equipamentos as $e): ?>
                                <option value="<?= $e['id_equipamento'] ?>" <?= (!empty($componente) && $componente['id_equipamento_principal']==$e['id_equipamento']) ? 'selected' : '' ?>><?= htmlspecialchars($e['designacao']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Código Interno</label>
                        <input type="text" name="codigo_interno" value="<?= ($componente['codigo_interno_componente'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Designação</label>
                        <input type="text" name="designacao" value="<?= ($componente['designacao_componente'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" name="marca" value="<?= ($componente['marca_componente'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" value="<?= ($componente['modelo_componente'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Nº Série</label>
                        <input type="text" name="numero_serie" value="<?= ($componente['numero_serie_componente'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Data Aquisição</label>
                        <input type="date" name="data_aquisicao" value="<?= ($componente['data_aquisicao_componente'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="grid-column:1/ -1;">
                        <label>Observações</label>
                        <textarea name="observacoes"><?= ($componente['observacoes'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i><?= $editing ? 'Atualizar' : 'Criar' ?></button>
                    <a class="btn btn-secondary" href="componentes_lista.php"><i class="fas fa-arrow-left"></i> Voltar</a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
