<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Editar Garantia</h1>
                <p>Atualize as informações da garantia</p>
            </div>
            <div class="dropdown user-profile">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">JD</div>
                    <span style="font-weight: 600; font-size: 0.875rem;">João Duarte</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--text-muted);"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            <div class="form-card">
                <div class="card-header">
                    <h2>Informações da Garantia</h2>
                </div>

                <form id="editarGarantiaForm" class="form-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="equipamento">Equipamento</label>
                            <select id="equipamento" name="equipamento" required>
                                <option value="">Selecionar equipamento</option>
                                <option value="EQP-2026-001" selected>EQP-2026-001 - Respirador Dräger</option>
                                <!-- Outras opções aqui -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fornecedor">Fornecedor</label>
                            <select id="fornecedor" name="fornecedor" required>
                                <option value="">Selecionar fornecedor</option>
                                <option value="draeger" selected>Dräger Portugal</option>
                                <!-- Outras opções aqui -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo de Garantia</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Selecionar tipo</option>
                                <option value="fabrica" selected>Garantia de Fábrica</option>
                                <option value="extendida">Garantia Extendida</option>
                                <option value="terceiro">Garantia de Terceiro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dataInicio">Data de Início</label>
                            <input type="date" id="dataInicio" name="dataInicio" value="2026-01-01" required>
                        </div>

                        <div class="form-group">
                            <label for="dataFim">Data de Fim</label>
                            <input type="date" id="dataFim" name="dataFim" value="2028-01-01" required>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" required>
                                <option value="ativo" selected>Ativo</option>
                                <option value="expirado">Expirado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section-title">
                        <h3>Informações Adicionais</h3>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="numeroSerie">Número de Série</label>
                            <input type="text" id="numeroSerie" name="numeroSerie" value="DRG-2026-001" placeholder="Número de série do equipamento">
                        </div>

                        <div class="form-group">
                            <label for="numeroGarantia">Número da Garantia</label>
                            <input type="text" id="numeroGarantia" name="numeroGarantia" value="GAR-2026-001" placeholder="Número da garantia">
                        </div>

                        <div class="form-group">
                            <label for="custo">Custo (€)</label>
                            <input type="number" id="custo" name="custo" step="0.01" value="0.00" placeholder="Custo da garantia">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes" name="observacoes" rows="4" placeholder="Observações adicionais sobre a garantia"></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="garantias_lista.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Atualizar Garantia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Lógica para o formulário de edição de garantia
        document.getElementById('editarGarantiaForm').addEventListener('submit', function (event) {
            event.preventDefault();
            alert('Garantia atualizada com sucesso!');
            window.location.href = 'garantias_lista.php';
        });
    </script>
<?php include '../../includes/footer.php'; ?>
