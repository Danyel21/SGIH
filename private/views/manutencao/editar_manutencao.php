<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Editar Manutenção</h1>
                <p>Atualize os dados da intervenção de manutenção</p>
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

        <div class="content-wrapper">
            <div class="form-card">
                <form id="editarManutencaoForm">
                    <div class="form-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="dataManutencao">Data da Intervenção *</label>
                                <input type="date" id="dataManutencao" value="2026-05-05" required />
                            </div>
                            <div class="form-group">
                                <label for="equipamento">Equipamento *</label>
                                <input type="text" id="equipamento" value="EQP-2026-001" required />
                            </div>
                            <div class="form-group">
                                <label for="tipoManutencao">Tipo de manutenção *</label>
                                <select id="tipoManutencao" required>
                                    <option value="Preventiva" selected>Preventiva</option>
                                    <option value="Correctiva">Correctiva</option>
                                    <option value="Urgente">Urgente</option>
                                    <option value="Calibracao">Calibração</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="responsavel">Responsável *</label>
                                <input type="text" id="responsavel" value="Técnico S. Silva" required />
                            </div>
                            <div class="form-group">
                                <label for="custo">Custo (€)</label>
                                <input type="number" id="custo" min="0" step="0.01" value="120.00" />
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado">
                                    <option value="Concluida" selected>Concluída</option>
                                    <option value="Pendente">Pendente</option>
                                    <option value="Agendada">Agendada</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="descricao">Descrição *</label>
                                <textarea id="descricao" rows="5" required>Substituição de filtros e verificação de sensores.</textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="manutencao_lista.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Guardar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

   <?php include '../../includes/footer.php'; ?>

