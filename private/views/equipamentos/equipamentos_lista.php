<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>
<?php require_once __DIR__ . '/../../includes/db_connect.php'; ?>
<?php require_once __DIR__ . '/../../includes/auth.php'; ?>
<?php require_once __DIR__ . '/../../includes/db_data.php'; ?>

<?php
$search = trim($_GET['search'] ?? '');
$categoria = $_GET['categoria'] ?? '';
$estado = $_GET['estado'] ?? '';
$localizacao = $_GET['localizacao'] ?? '';
$fornecedor = $_GET['fornecedor'] ?? '';
$criticidade = $_GET['criticidade'] ?? '';
$sort = $_GET['sort'] ?? '';
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
$equipamentos = $dbManager->getEquipamentos([
    'search' => $search,
    'categoria' => $categoria,
    'estado' => $estado,
    'localizacao' => $localizacao,
    'fornecedor' => $fornecedor,
    'criticidade' => $criticidade,
    'sort' => $sort
]) ?? [];
$categorias = $dbManager->getCategoriasEquipamento();
$estados = $dbManager->getEstadosEquipamento();
$localizacoes = $dbManager->getLocalizacoes([], 1);
$fornecedores = $dbManager->getFornecedores(1);
$criticidades = $dbManager->getCriticidades();

function getSelectedLabel(array $items, $selected, string $idKey, string $labelKey) {
    foreach ($items as $item) {
        if ((string)($item[$idKey] ?? '') === (string)$selected) {
            return $item[$labelKey] ?? '';
        }
    }
    return '';
}

$selectedCategoriaLabel = getSelectedLabel($categorias, $categoria, 'id_categoria', 'nome_categoria');
$selectedEstadoLabel = getSelectedLabel($estados, $estado, 'id_estado', 'nome_estado');
$selectedFornecedorLabel = getSelectedLabel($fornecedores, $fornecedor, 'id_fornecedor', 'nome_empresa');
$selectedCriticidadeLabel = getSelectedLabel($criticidades, $criticidade, 'id_criticidade', 'nivel_criticidade');
$selectedLocalizacaoLabel = '';
if ($localizacao !== '') {
    foreach ($localizacoes as $loc) {
        if ((string)($loc['id_localizacao'] ?? '') === (string)$localizacao) {
            $selectedLocalizacaoLabel = $loc['servico_departamento'] . (!empty($loc['sala_gabinete']) ? ' - ' . $loc['sala_gabinete'] : '');
            break;
        }
    }
}

$hasActiveFilters = !empty($search) || $categoria !== '' || $estado !== '' || $localizacao !== '' || $fornecedor !== '' || $criticidade !== '';
?>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Lista de Equipamentos</h1>
                <p>Gerencie os equipamentos do hospital</p>
            </div>
            <?php include __DIR__ . '/../../includes/user_menu.php'; ?>
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
                <?php if (!empty($search) || !empty($categoria) || !empty($estado) || !empty($localizacao)): ?>
                    <div class="alert alert-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #e7f3fe; color: #31708f; border-radius: 0.5rem; border-left: 4px solid #31708f;">
                        <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                    A ver resultados filtrados.
                        <?php if (!empty($search)): ?>
                        <strong>Pesquisa:</strong> <?= ($search) ?>
                    <?php endif; ?>
                    <?php if (!empty($categoria)): ?>
                        | <strong>Categoria:</strong> <?= ($selectedCategoriaLabel) ?>
                    <?php endif; ?>
                    <?php if (!empty($estado)): ?>
                        | <strong>Estado:</strong> <?= ($selectedEstadoLabel) ?>
                    <?php endif; ?>
                    <?php if (!empty($localizacao)): ?>
                        | <strong>Localização:</strong> <?= ($selectedLocalizacaoLabel) ?>
                    <?php endif; ?>
                    <?php if (!empty($fornecedor)): ?>
                        | <strong>Fornecedor:</strong> <?= ($selectedFornecedorLabel) ?>
                    <?php endif; ?>
                    <?php if (!empty($criticidade)): ?>
                        | <strong>Criticidade:</strong> <?= ($selectedCriticidadeLabel) ?>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>
      <form method="GET" class="filters-box">
        <div class="filter-group flex-grow">
            <label>Pesquisar</label>
            <input type="text" name="search" value="<?= ($search) ?>" placeholder="Código, Designação, Marca, Modelo, Série...">
        </div>

        <div class="filter-group w-48">
            <label>Categoria</label>
            <select name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= ($cat['id_categoria']) ?>" <?= $cat['id_categoria'] == $categoria ? 'selected' : '' ?>>
                        <?= ($cat['nome_categoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group w-48">
            <label>Estado</label>
            <select name="estado">
                <option value="">Todos</option>
                <?php foreach ($estados as $estadoItem): ?>
                    <option value="<?= ($estadoItem['id_estado']) ?>" <?= $estadoItem['id_estado'] == $estado ? 'selected' : '' ?>>
                        <?= ($estadoItem['nome_estado']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group w-48">
            <label>Localização</label>
            <select name="localizacao">
                <option value="">Todas</option>
                <?php foreach ($localizacoes as $loc): ?>
                    <option value="<?= ($loc['id_localizacao']) ?>" <?= $loc['id_localizacao'] == $localizacao ? 'selected' : '' ?>>
                        <?= ($loc['servico_departamento'] . ($loc['sala_gabinete'] . $loc['edificio'] ? ' - ' . $loc['sala_gabinete'] : '')) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group w-48">
            <label>Fornecedor</label>
            <select name="fornecedor">
                <option value="">Todos</option>
                <?php foreach ($fornecedores as $fornecedorItem): ?>
                    <option value="<?= ($fornecedorItem['id_fornecedor']) ?>" <?= $fornecedorItem['id_fornecedor'] == $fornecedor ? 'selected' : '' ?>>
                        <?= ($fornecedorItem['nome_empresa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group w-48">
            <label>Criticidade</label>
            <select name="criticidade">
                <option value="">Todas</option>
                <?php foreach ($criticidades as $crit): ?>
                    <option value="<?= ($crit['id_criticidade']) ?>" <?= $crit['id_criticidade'] == $criticidade ? 'selected' : '' ?>>
                        <?= ($crit['nivel_criticidade']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group w-48">
            <label>Ordenar por</label>
            <select name="sort">
                <option value="">Mais recentes</option>
                <option value="codigo" <?= $sort === 'codigo' ? 'selected' : '' ?>>Código interno</option>
                <option value="designacao" <?= $sort === 'designacao' ? 'selected' : '' ?>>Designação</option>
                <option value="categoria" <?= $sort === 'categoria' ? 'selected' : '' ?>>Categoria</option>
                <option value="criticidade" <?= $sort === 'criticidade' ? 'selected' : '' ?>>Criticidade</option>
                <option value="fornecedor" <?= $sort === 'fornecedor' ? 'selected' : '' ?>>Fornecedor</option>
                <option value="localizacao" <?= $sort === 'localizacao' ? 'selected' : '' ?>>Localização</option>
            </select>
        </div>

        <div class="filter-button">
            <button type="submit">
                <i class="fas fa-filter"></i>
                Filtrar
            </button>
            <a href="equipamentos_lista.php" class="btn btn-secondary" style="margin-left: 0.75rem;">
                Limpar
            </a>
        </div>
</form>
            
      <!-- Botão para Novo equipamento -->
            <div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
                <a href="equipamento_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Novo Equipamento
                </a>
            </div>

      <!-- Table -->
            <div class="table-container">

                <table class="custom-table">

                    <thead>
                        <tr>
                            <th>Cód. Interno</th>
                            <th>Designação</th>
                            <th>Categoria</th>
                            <th>Localização</th>
                            <th>Criticidade</th>
                            <th>Disponibilidade</th>
                            <th class="actions">Ações</th>
                        </tr>
                    </thead>

<tbody>

<?php if (!empty($equipamentos)): ?>

    <?php foreach ($equipamentos as $equipamento): ?>

        <?php

        // Classe da criticidade
        $criticidadeClass = 'badge-blue';

        switch ($equipamento['nivel_criticidade']) {
            case 'Alta':
                $criticidadeClass = 'badge-danger';
                break;

            case 'Média':
                $criticidadeClass = 'badge-warning';
                break;

            case 'Baixa':
                $criticidadeClass = 'badge-success';
                break;

            default:
                $criticidadeClass = 'badge-danger';
                break;
        }

        // Classe do estado
        $estadoClass = 'badge-secondary';

        switch ($equipamento['nome_estado']) {
            case 'Ativo':
                $estadoClass = 'badge-success';
                break;

            case 'Em Manutenção':
                $estadoClass = 'badge-warning';
                break;

            case 'Inativo':
                $estadoClass = 'badge-danger';
                break;
            
            default:
                $estadoClass = 'badge-warning';
                break;
        }

        ?>

        <tr>

            <td class="code">
                <?= ($equipamento['codigo_interno']) ?>
            </td>

            <td>
                <?= ($equipamento['designacao']) ?>
            </td>


            <td class="code">
                <?= ($equipamento['nome_categoria']) ?>
            </td>

            <td>
                <?= ($equipamento['localizacao_servico']) ?>
                <?php if (!empty($equipamento['localizacao_sala'])): ?>
                    - <?= ($equipamento['localizacao_sala']) ?>
                <?php endif; ?>
            </td>

            <td>
                <span class="badge <?= $criticidadeClass ?>">
                    <?= ($equipamento['nivel_criticidade']) ?>
                </span>
            </td>

            <?php $emUso = $dbManager->isEquipamentoEmUso($equipamento['id_equipamento']); ?>
            <td>
                <span class="badge" style="background: <?= $emUso ? '#ffc107' : '#28a745' ?>; color: #000;">
                    <?= $emUso ? 'Em Uso' : 'Disponível' ?>
                </span>
            </td>
            
            <td class="actions">

                <a href="equipamento_detalhe.php?id_equipamento=<?= $equipamento['id_equipamento'] ?>" class="view">
                    <i class="fas fa-info-circle"></i>
                </a>

                <a href="equipamento_form.php?id_equipamento=<?= $equipamento['id_equipamento'] ?>" class="edit">
                    <i class="fas fa-edit"></i>
                </a>

                <a href="#"
                   class="delete"
                   data-bs-toggle="modal"
                   data-bs-target="#deleteModal"
                   data-id="<?= $equipamento['id_equipamento'] ?>"
                   data-nome="<?= ($equipamento['designacao']) ?>">
                    <i class="fas fa-trash"></i>
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>
        <td colspan="7" style="text-align:center;">
            Nenhum equipamento encontrado.
        </td>
    </tr>

<?php endif; ?>

</tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">

                <div class="pagination-info">
                    Mostrando 1 a 10 de 1,248 resultados
                </div>

             <div class="pagination">


</div>

        </div>
    </main>

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
                    <p>Tem a certeza que deseja eliminar este equipamento?</p>
                    <p style="font-weight: 600; color: var(--text-main);">
                        <i class="fas fa-microscope" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                        <span id="equipamentoNome"></span>
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

    let equipamentoId = null;

    document.querySelectorAll('.delete').forEach(btn => {

        btn.addEventListener('click', function() {

            equipamentoId = this.dataset.id;

            document.getElementById('equipamentoNome').textContent =
                this.dataset.nome;
        });
    });

    document.getElementById('confirmDeleteBtn')
        .addEventListener('click', function() {

            if(equipamentoId){
                window.location.href =
                    'equipamento_delete.php?id_equipamento=' + equipamentoId;
            }
        });
});
</script>
    <?php include '../../includes/footer.php'; ?>
