<?php 
/**
 * Dashboard do Parque Tecnológico Hospitalar
 * Queries atualizadas de acordo com a estrutura do DatabaseManager.php
 */
require_once __DIR__ . "/../includes/auth.php";
requireAuthentication();
include '../includes/header.php'; 
include '../includes/sidebar.php'; 
require_once __DIR__ . "/../includes/db_connect.php";

// --- Queries para Indicadores Mínimos ---

// 1. Total de equipamentos
$totalQuery = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO")->fetchColumn();

// 2. Equipamentos Ativos (nome_estado = 'Ativo')
$ativosQuery = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e 
                           JOIN ESTADO_EQUIPAMENTO ee ON e.id_estado = ee.id_estado 
                           WHERE ee.nome_estado = 'Ativo'")->fetchColumn();

// 3. Equipamentos em Manutenção (nome_estado = 'Manutenção')
$manutencaoQuery = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e 
                               JOIN ESTADO_EQUIPAMENTO ee ON e.id_estado = ee.id_estado 
                               WHERE ee.nome_estado = 'Manutenção'")->fetchColumn();

// 4. Equipamentos Inativos (nome_estado = 'Inativo')
$inativosQuery = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e 
                             JOIN ESTADO_EQUIPAMENTO ee ON e.id_estado = ee.id_estado 
                             WHERE ee.nome_estado = 'Inativo'")->fetchColumn();

// 5. Garantias Expiradas (Tabela GARANTIA_CONTRATO)
$garantiaExpiradaQuery = $pdo->query("SELECT COUNT(*) FROM GARANTIA_CONTRATO WHERE data_fim_garantia < CURDATE()")->fetchColumn();

// 6. Sem documentação associada (Equipamentos que não têm registo na tabela DOCUMENTO)
$semDocQuery = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e 
                           LEFT JOIN DOCUMENTO d ON e.id_equipamento = d.id_equipamento 
                           WHERE d.id_documento IS NULL")->fetchColumn();

// --- Queries para Indicadores Adicionais ---

// 7. Garantia a expirar nos próximos 30 dias
$garantiaProximaQuery = $pdo->query("SELECT COUNT(*) FROM GARANTIA_CONTRATO 
                                     WHERE data_fim_garantia BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();

// 8. Criticidade Elevada (nivel_criticidade = 'Alta')
$criticidadeAltaQuery = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e 
                                    JOIN CRITICIDADE c ON e.id_criticidade = c.id_criticidade 
                                    WHERE c.nivel_criticidade = 'Alta'")->fetchColumn();

// 9. Equipamentos por Serviço (LOCALIZACAO.servico_departamento)
$porServico = $pdo->query("SELECT l.servico_departamento as designacao, COUNT(e.id_equipamento) as total 
                           FROM EQUIPAMENTO e 
                           JOIN LOCALIZACAO l ON e.id_localizacao = l.id_localizacao 
                           GROUP BY l.servico_departamento")->fetchAll(PDO::FETCH_ASSOC);

// 10. Equipamentos de Suporte de Vida por Serviço
// Assumindo que a categoria com nome 'Suporte de Vida' identifica estes equipamentos
$suporteVidaPorServico = $pdo->query("SELECT l.servico_departamento as designacao, COUNT(e.id_equipamento) as total 
                                     FROM EQUIPAMENTO e 
                                     JOIN LOCALIZACAO l ON e.id_localizacao = l.id_localizacao 
                                     JOIN CATEGORIA_EQUIPAMENTO ce ON e.id_categoria = ce.id_categoria
                                     WHERE ce.nome_categoria = 'Suporte de Vida'
                                     GROUP BY l.servico_departamento")->fetchAll(PDO::FETCH_ASSOC);

// 11. Distribuição por Categoria (CATEGORIA_EQUIPAMENTO)
$porCategoria = $pdo->query("SELECT ce.nome_categoria as categoria, COUNT(e.id_equipamento) as total 
                             FROM EQUIPAMENTO e 
                             JOIN CATEGORIA_EQUIPAMENTO ce ON e.id_categoria = ce.id_categoria 
                             GROUP BY ce.nome_categoria")->fetchAll(PDO::FETCH_ASSOC);

$catLabels = array_column($porCategoria, 'categoria');
$catData = array_column($porCategoria, 'total');

// 12. Distribuição por Estado de Equipamento
$porEstado = $pdo->query("SELECT ee.nome_estado as estado, COUNT(e.id_equipamento) as total 
                           FROM EQUIPAMENTO e 
                           JOIN ESTADO_EQUIPAMENTO ee ON e.id_estado = ee.id_estado 
                           GROUP BY ee.nome_estado")->fetchAll(PDO::FETCH_ASSOC);

$estLabels = array_column($porEstado, 'estado');
$estData = array_column($porEstado, 'total');

// 13. Alertas Recentes de Garantia (para a tabela)
$alertasGarantia = $pdo->query("SELECT e.designacao, gc.data_fim_garantia, 
                                DATEDIFF(gc.data_fim_garantia, CURDATE()) as dias_restantes 
                                FROM GARANTIA_CONTRATO gc
                                JOIN EQUIPAMENTO e ON gc.id_equipamento = e.id_equipamento
                                WHERE gc.data_fim_garantia BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                                ORDER BY gc.data_fim_garantia ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content -->
<main class="main-content">
    <header>
        <div class="header-title">
            <h1>Dashboard Global</h1>
            <p>Estado do Parque Tecnológico Hospitalar</p>
        </div> 
        <?php include '../includes/user_menu.php'; ?>            
    </header>

    <!-- Stats Grid - Indicadores Principais -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="stat-info">
                <h3>Total Equipamentos</h3>
                <p><?= $totalQuery ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>Ativos</h3>
                <p><?= $ativosQuery ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-yellow">
                <i class="fas fa-tools"></i>
            </div>
            <div class="stat-info">
                <h3>Em Manutenção</h3>
                <p><?= $manutencaoQuery ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-red">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3>Inativos</h3>
                <p><?= $inativosQuery ?></p>
            </div>
        </div>
    </div>

    <!-- Second Stats Grid - Alertas e Documentação -->
    <div class="stats-grid" style="margin-top: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon icon-red">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="stat-info">
                <h3>Garantias Expiradas</h3>
                <p><?= $garantiaExpiradaQuery ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-orange">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="stat-info">
                <h3>Sem Documentação</h3>
                <p><?= $semDocQuery ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-purple">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <h3>Criticidade Alta</h3>
                <p><?= $criticidadeAltaQuery ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <h3>Garantia < 30 dias</h3>
                <p><?= $garantiaProximaQuery ?></p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
        <!-- Tabela: Equipamentos por Serviço -->
        <div class="content-card">
            <div class="card-header">
                <h2>Equipamentos por Serviço</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Serviço / Departamento</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($porServico as $servico): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($servico['designacao'] ?? 'N/D') ?></strong></td>
                            <td><?= $servico['total'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Alertas de Garantia Próximos -->
        <div class="content-card">
            <div class="card-header">
                <h2>Próximos Vencimentos de Garantia</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Equipamento</th>
                            <th>Data Fim</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alertasGarantia as $alerta): ?>
                        <tr>
                            <td><?= htmlspecialchars($alerta['designacao']) ?></td>
                            <td><?= date('d/m/Y', strtotime($alerta['data_fim_garantia'])) ?></td>
                            <td>
                                <?php if($alerta['dias_restantes'] <= 7): ?>
                                    <span class="badge badge-danger"><?= $alerta['dias_restantes'] ?> dias</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= $alerta['dias_restantes'] ?> dias</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($alertasGarantia)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 1rem;">Sem alertas para os próximos 30 dias.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
        <div class="content-card">
            <div class="card-header">
                <h2>Distribuição por Categoria</h2>
            </div>
            <div style="padding: 1rem; height: 300px;">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>
        <div class="content-card">
            <div class="card-header">
                <h2>Estado dos Equipamentos</h2>
            </div>
            <div style="padding: 1rem; height: 300px;">
                <canvas id="chartEstados"></canvas>
            </div>
        </div>
    </div>

        <!-- Resumo de Criticidade -->
        <div class="content-card">
            <div class="card-header">
                <h2>Resumo de Criticidade</h2>
            </div>
            <div style="padding: 1.5rem; display: flex; justify-content: space-around; text-align: center;">
                <?php
                $critAlta = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e JOIN CRITICIDADE c ON e.id_criticidade = c.id_criticidade WHERE c.nivel_criticidade = 'Alta'")->fetchColumn();
                $critMedia = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e JOIN CRITICIDADE c ON e.id_criticidade = c.id_criticidade WHERE c.nivel_criticidade = 'Média'")->fetchColumn();
                $critBaixa = $pdo->query("SELECT COUNT(*) FROM EQUIPAMENTO e JOIN CRITICIDADE c ON e.id_criticidade = c.id_criticidade WHERE c.nivel_criticidade = 'Baixa'")->fetchColumn();
                ?>
                <div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--danger);"><?= $critAlta ?></div>
                    <div style="color: #666;">Alta</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--warning);"><?= $critMedia ?></div>
                    <div style="color: #666;">Média</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--success);"><?= $critBaixa ?></div>
                    <div style="color: #666;">Baixa</div>
                </div>
            </div>
        </div>
</main>

<!-- Scripts para Gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Categorias
    const ctxCat = document.getElementById('chartCategorias').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($catLabels) ?>,
            datasets: [{
                data: <?= json_encode($catData) ?>,
                backgroundColor: ['#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6', '#34495e'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Gráfico de Estados
    const ctxEst = document.getElementById('chartEstados').getContext('2d');
    new Chart(ctxEst, {
        type: 'bar',
        data: {
            labels: <?= json_encode($estLabels) ?>,
            datasets: [{
                label: 'Quantidade',
                data: <?= json_encode($estData) ?>,
                backgroundColor: '#3498db',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>