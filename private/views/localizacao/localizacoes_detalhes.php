<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
<?php require_once '../../includes/auth.php'; ?>
<?php require_once '../../includes/db_data.php'; ?>
    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Lista de Localizações</h1>
                <p>Gerencie as localizações dos equipamentos do hospital</p>
            </div>
  <?php include '../../includes/user_menu.php'; ?>    
        </header>
<div class="content-wrapper">
            <div class="filters-box">
                <div class="filter-group flex-grow">
                    <label>Pesquisar</label>
                    <input type="text" placeholder="Pesquisar localização...">
                </div>
                <div class="filter-group w-48">
                    <label>Tipo de Local</label>
                    <select>
                        <option>Todos</option>
                        <option>Hospitalar</option>
                        <option>Clínica</option>
                        <option>Laboratório</option>
                    </select>
                </div>
                <div class="filter-button">
                    <button type="button">Filtrar</button>
                </div>
            </div>
            <!-- Botão para Nova Localização -->
            <div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
                <a href="novalocalizacao.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nova Localização
                </a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Edifício</th>
                            <th>Piso</th>
                            <th>Serviço / Departamento</th>
                            <th>Sala / Gabinete</th>
                            <th class="actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Edifício A</td>
                            <td>1</td>
                            <td>UCI</td>
                            <td>Sala 04</td>
                            <td class="actions">
                                <a href="novalocalizacao.php?id=1" class="edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-localizacao="1" data-nome="Edifício A - Piso 1 - UCI - Sala 04">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
          <!-- Pagination -->
            <div class="pagination-wrapper">

                <div class="pagination-info">
                    Mostrando 1 a 10 de 1,248 resultados
                </div>

                <div class="pagination">

                    <button>Anterior</button>

                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>

                    <button>Próximo</button>

                </div>

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
                    <p>Tem a certeza que deseja eliminar esta localização?</p>
                    <p style="font-weight: 600; color: var(--text-main);">
                        <i class="fas fa-map-marker-alt" style="margin-right: 0.5rem; color: var(--primary-color);"></i>
                        <span id="localizacaoName">Edifício A - Piso 1 - UCI - Sala 04</span>
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


<?php include '../../includes/footer.php'; ?>
