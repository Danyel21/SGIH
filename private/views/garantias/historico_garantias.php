<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Histórico de Garantias</h1>
                <p>Veja todas as garantias associadas ao equipamento selecionado.</p>
            </div>
        </header>
        <div class="content-wrapper">
                <div class="filters-box">
                    <div class="filter-group flex-grow">
                        <label>Pesquisar</label>
                        <input type="text" placeholder="Pesquisar garantia...">
                    </div>
                    <div class="filter-group w-48">
                        <label>Estado</label>
                        <select>
                            <option>Todos</option>
                            <option>Ativa</option>
                            <option>Expirada</option>
                            <option>Em renovação</option>
                        </select>
                    </div>
                    <div class="filter-button">
                        <button type="button">Filtrar</button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Equipamento</th>
                                <th>Fornecedor</th>
                                <th>Tipo</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>EQP-2026-001</td>
                                <td>Dräger Portugal</td>
                                <td>Garantia de Fábrica</td>
                                <td>01/01/2024</td>
                                <td>01/01/2026</td>
                                <td><span class="badge badge-success">Expirada</span></td>
                            </tr>
                            <tr>
                                <td>EQP-2026-001</td>
                                <td>Dräger Portugal</td>
                                <td>Extensão Contratual</td>
                                <td>02/01/2026</td>
                                <td>01/01/2028</td>
                                <td><span class="badge badge-success">Ativa</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </main>
<?php include '../../includes/footer.php'; ?>
