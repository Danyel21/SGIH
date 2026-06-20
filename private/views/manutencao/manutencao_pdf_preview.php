<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

    <main class="main-content">
        <header>
            <div class="header-title">
                <h1>Pré-visualização PDF</h1>
                <p>Veja o documento de manutenção antes de o transferir.</p>
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
            <div class="content-card">
                <div class="card-header" style="align-items: flex-start; gap: 1rem;">
                    <div>
                        <h2>Pré-visualização PDF</h2>
                        <p style="margin-top: 0.5rem; color: var(--text-muted);">Veja o documento de manutenção antes de o transferir.</p>
                    </div>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <a href="manutencao_lista.html" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Voltar
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-download"></i>
                            Baixar PDF
                        </button>
                    </div>
                </div>

                <div class="table-container" style="padding: 24px;">
                    <div style="margin-bottom: 24px;">
                        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 24px;">
                            <div>
                                <h3 style="margin: 0;">Hospital Central de Lisboa</h3>
                                <p style="margin: 8px 0 0; color: var(--text-muted);">Departamento de Manutenção e Engenharia Clínica</p>
                                <p style="margin: 8px 0 0; color: var(--text-muted);">Avenida Principal, 1000 - Lisboa, Portugal</p>
                                <p style="margin: 8px 0 0; color: var(--text-muted);"><i class="fas fa-phone"></i> +351 21 XXXX XXXX</p>
                                <p style="margin: 8px 0 0; color: var(--text-muted);"><i class="fas fa-envelope"></i> manutencao@hospitalcentral.pt</p>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border-radius: 12px; color: white; font-size: 32px;">
                                <i class="fas fa-hospital-user"></i>
                            </div>
                        </div>

                        <div style="text-align: center; margin-bottom: 24px;">
                            <h2 style="margin-bottom: 8px;">RELATÓRIO DE MANUTENÇÃO DE EQUIPAMENTOS</h2>
                            <p style="margin: 0 0 12px; color: var(--text-muted);">Período: Maio 2026</p>
                            <span style="display: inline-block; background: #e3f2fd; border: 1px solid #90caf9; border-radius: 6px; padding: 8px 16px; font-size: 12px; color: #1976d2; font-weight: 600;">Documento gerado em: 14/05/2026</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <div style="background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center;">
                            <h4 style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Total de registos</h4>
                            <div style="font-size: 28px; font-weight: 700; color: #1a1a1a;">3</div>
                        </div>
                        <div style="background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center;">
                            <h4 style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Custo total</h4>
                            <div style="font-size: 28px; font-weight: 700; color: #1a1a1a;">555,50 €</div>
                        </div>
                        <div style="background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center;">
                            <h4 style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Custo médio</h4>
                            <div style="font-size: 28px; font-weight: 700; color: #1a1a1a;">185,17 €</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <h3 style="margin: 0 0 16px;">Detalhes de manutenção</h3>
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Equipamento</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th style="text-align: right;">Custo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>05/05/2026</td>
                                    <td>EQP-2026-001</td>
                                    <td><span class="badge badge-success">Preventiva</span></td>
                                    <td>Substituição de filtros e verificação de sensores.</td>
                                    <td style="text-align: right; font-weight: 600;">120,00 €</td>
                                </tr>
                                <tr>
                                    <td>03/05/2026</td>
                                    <td>EQP-2026-002</td>
                                    <td><span class="badge badge-danger">Corretiva</span></td>
                                    <td>Reparação de bomba de circulação e teste de pressão.</td>
                                    <td style="text-align: right; font-weight: 600;">350,50 €</td>
                                </tr>
                                <tr>
                                    <td>01/05/2026</td>
                                    <td>EQP-2026-003</td>
                                    <td><span class="badge badge-success">Preventiva</span></td>
                                    <td>Limpeza de componentes internos e lubrificação.</td>
                                    <td style="text-align: right; font-weight: 600;">85,00 €</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                            <div>
                                <h4 style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Responsável pela manutenção</h4>
                                <p style="font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0;">João Duarte</p>
                                <p class="subtitle" style="font-size: 12px; color: #999; margin-top: 3px;">Engenheiro Clínico Sénior</p>
                            </div>
                            <div style="text-align: right;">
                                <h4 style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Custo total do período</h4>
                                <div style="font-size: 28px; font-weight: 700; color: #4f46e5;">555,50 €</div>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px; margin-bottom: 24px;">
                        <div style="text-align: center;">
                            <div style="border-bottom: 1px solid #333; margin-bottom: 10px; height: 40px;"></div>
                            <p style="font-size: 12px; font-weight: 600; color: #1a1a1a; margin: 0;">Responsável de manutenção</p>
                            <p class="date" style="font-size: 11px; color: #666; margin-top: 5px;">Data: 14/05/2026</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="border-bottom: 1px solid #333; margin-bottom: 10px; height: 40px;"></div>
                            <p style="font-size: 12px; font-weight: 600; color: #1a1a1a; margin: 0;">Diretor de Engenharia Clínica</p>
                            <p class="date" style="font-size: 11px; color: #666; margin-top: 5px;">Data: 14/05/2026</p>
                        </div>
                    </div>

                    <div style="text-align: center; font-size: 11px; color: #999;">
                        <p style="margin: 0;">© 2026 Hospital Central de Lisboa. Todos os direitos reservados.</p>
                        <p style="margin: 8px 0 0;">Este documento é confidencial e destinado apenas ao uso interno.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

  <?php include '../../includes/footer.php'; ?>
