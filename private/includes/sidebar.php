<?php
// Obtém o nome do ficheiro atual da URL para determinar qual link deve estar ativo
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-hospital-user"></i>
            <h2>SGIH</h2>
        </div>

        <nav class="nav-menu">
            <div class="nav-item">
                <a href="<?php echo $waypath; ?>dashboard/dashboard_admin.php" class="nav-link  <?php echo ($current_page == 'dashboard_admin.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?php echo $waypath; ?>views/equipamentos/equipamentos_lista.php" class="nav-link  <?php echo ($current_page == 'equipamentos_lista.php' or $current_page == 'equipamento_detalhe.php' or $current_page == 'equipamento_form.php') ? 'active' : ''; ?>">
                    <i class="fas fa-microscope"></i>
                    <span>Equipamentos</span>
                </a>
            </div>


            <!-- Componentes -->
            <div class="nav-item">
                <a href="<?php echo $waypath; ?>views/componentes/componentes_lista.php" class="nav-link  <?php echo ($current_page == 'componentes_lista.php' or $current_page == 'componente_form.php' or $current_page == 'componente_detalhe.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cogs"></i>
                    <span>Componentes</span>
                </a>
            </div>

            
            <!-- Localizações -->
            <div class="nav-item">
                <a href="<?php echo $waypath; ?>views/localizacao/localizacoes_lista.php" class="nav-link  <?php echo ($current_page == 'localizacoes_lista.php' or $current_page == 'localizacao_edit.php') ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Localizações</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="<?php echo $waypath; ?>views/fornecedor/fornecedores_lista.php" class="nav-link  <?php echo ($current_page == 'fornecedores_lista.php' or $current_page == 'editar_fornecedor.php' or $current_page == 'detalhes_fornecedor.php' or $current_page == 'novo_fornecedor.php') ? 'active' : ''; ?>">
                    <i class="fas fa-truck"></i>
                    <span>Fornecedores</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?php echo $waypath; ?>views/manutencao/manutencao_lista.php" class="nav-link  <?php echo ($current_page == 'manutencao_lista.php' or $current_page == 'manage_manutencao.php') ? 'active' : ''; ?>">
                    <i class="fas fa-file-medical"></i>
                    <span>Manutenção</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?php echo $waypath; ?>views/garantias/garantias_lista.php" class="nav-link  <?php echo ($current_page == 'garantias_lista.php' or $current_page == 'manage_garantia.php') ? 'active' : ''; ?>">
                    <i class="fas fa-file-contract"></i>
                    <span>Garantias</span>
                </a>
            </div>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <div class="nav-item">
                <a href="<?php echo $waypath; ?>views/utilizadores/utilizador_lista.php" class="nav-link  <?php echo ($current_page == 'utilizador_lista.php' or $current_page == 'utilizador_form.php' or $current_page == 'utilizador_detalhe.php') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Utilizadores</span>
                </a>
            </div>
        <?php endif; ?>
        </nav>



        <div class="sidebar-footer">
            <a href="#" class="nav-link">
                <i class="fas fa-cog"></i>
                <span>Configurações</span>
            </a>
            <a href="<?php echo $waypath; ?>logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>
    </aside>
