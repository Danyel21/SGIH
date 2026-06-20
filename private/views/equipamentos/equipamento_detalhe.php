<?php include __DIR__ . '/../../includes/header.php'; ?>

<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<?php require_once __DIR__ . '/../../includes/auth.php'; ?>

<?php require_once __DIR__ . '/../../includes/db_data.php'; ?>



<?php



$id_equipamento = $_GET['id_equipamento'] ?? null;


if (!$id_equipamento) {

    die("ID do equipamento não fornecido.");

}



$equipamento = $dbManager->getEquipamentoByCodigo($id_equipamento);
$garantia_contrato = $dbManager->getGarantiasContratos_by_id($id_equipamento);
$componentes = $dbManager->getComponentesEquipamento_by_id($id_equipamento);
$documentos = $dbManager->getDocumentos_by_equipamento($id_equipamento);
// helper para codificar cada segmento do caminho preservando '/' e evitando null
function _encode_path_segments($path) {
    $path = (string)($path ?? '');
    if ($path === '') return '';
    // normalize separators to '/'
    $path = str_replace('\\', '/', $path);
    $parts = preg_split('~/+~', $path);
    return implode('/', array_map('rawurlencode', $parts));
}
 


if (!$equipamento) {

    die("Equipamento não encontrado.");

}

// Classe da criticidade
$criticidadeClass = 'badge-primary';

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

    case 'Suporte de Vida':
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
}
?>



<main class="main-content">

    <div class="content-wrapper">



        <!-- HEADER -->

        <div class="content-card" style="margin-bottom: 1.5rem;">

            <div class="card-header"
                style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">



                <div>

                    <a href="equipamentos_lista.php" style="color:var(--text-muted);text-decoration:none;">

                        <i class="fas fa-arrow-left"></i> Voltar

                    </a>



                    <h2 style="margin:0.75rem 0 0;">Detalhes do Equipamento</h2>



                    <p style="margin:0.25rem 0 0;color:var(--text-muted);">

                        <?= ($equipamento['designacao']) ?>

                        (<?= ($equipamento['codigo_interno']) ?>)

                    </p>

                </div>



                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">

                    <a href="equipamento_form.php?id_equipamento=<?= $equipamento['id_equipamento'] ?>" class="btn btn-primary">

                        <i class="fas fa-edit"></i> Editar

                    </a>



                    <a href="equipamento_delete.php?id_equipamento=<?= $equipamento['id_equipamento'] ?>" class="btn"
                        style="background:var(--danger);color:#fff;">

                        <i class="fas fa-trash"></i> Eliminar

                    </a>

                </div>

            </div>

        </div>






            <!-- COLUNA ESQUERDA -->

<div style="display:flex;flex-direction:column;gap:1.5rem;max-width:1200px;margin:0 auto;">



                <!-- INFO GERAL -->

                <div class="content-card">

                    <div class="card-header">

                        <h2>Informação Geral</h2>

                    </div>



                    <div style="padding:1.5rem;">

                        <table class="custom-table" style="width:100%;">

                            <tbody>

                                <tr>

                                    <th>Designação</th>

                                    <td><?= ($equipamento['designacao']) ?></td>

                                </tr>



                                <tr>

                                    <th>Código Interno</th>

                                    <td><?= ($equipamento['codigo_interno']) ?></td>

                                </tr>



                                <tr>

                                    <th>Categoria</th>

                                    <td><?= ($equipamento['nome_categoria']) ?></td>

                                </tr>



                                <tr>

                                    <th>Marca / Modelo</th>

                                    <td>

                                        <?= ($equipamento['marca']) ?> /

                                        <?= ($equipamento['modelo']) ?>

                                    </td>

                                </tr>



                                <tr>

                                    <th>Nº Série</th>

                                    <td><?= ($equipamento['numero_serie']) ?></td>

                                </tr>



                                <tr>

                                    <th>Localização</th>

                                    <td>

                                        <?= ($equipamento['localizacao_servico']) ?>

                                        <?php if (!empty($equipamento['localizacao_sala'])): ?>

                                            - <?= ($equipamento['localizacao_sala']) ?>

                                        <?php endif; ?>

                                    </td>

                                </tr>



                                <tr>

                                    <th>Data de Aquisição</th>

                                    <td>

                                        <?= !empty($equipamento['data_aquisicao'])

                                            ? date('d/m/Y', strtotime($equipamento['data_aquisicao']))

                                            : '-' ?>

                                    </td>

                                </tr>



                                <tr>

                                    <th>Tipo de Entrada</th>

                                    <td><?= ($equipamento['tipo_entrada']) ?></td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h2>Componentes associados</h2>
                    </div>

                    <div class="table-container">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Código Interno</th>
                                    <th>Designação</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Nº Série</th>
                                    <th>Data Aquisição</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($componentes)): ?>
                                    <?php foreach ($componentes as $componente): ?>
                                        <tr>
                                            <td><?= ($componente['codigo_interno_componente']) ?></td>
                                            <td><?= ($componente['designacao_componente']) ?></td>
                                            <td><?= ($componente['marca_componente'] ?? '-') ?></td>
                                            <td><?= ($componente['modelo_componente'] ?? '-') ?></td>
                                            <td><?= ($componente['numero_serie_componente'] ?? '-') ?></td>
                                            <td>
                                                <?= !empty($componente['data_aquisicao_componente'])
                                                    ? date('d/m/Y', strtotime($componente['data_aquisicao_componente']))
                                                    : '-' ?>
                                            </td>
                                            <td><?= ($componente['observacoes'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center;">
                                            Nenhum componente associado.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="content-card">
                    <div class="card-header">
                        <h2>Documentação</h2>
                    </div>
                    <div style="padding:1.5rem;">
                        <?php
                        // Define categorias de documento com padrões de correspondência
                        $categoriasDocumentos = [
                            'Manual de utilizador' => [
                                'manual de utilizador', 'manual utilizador', 'manual técnico', 'manual tecnico', 'manual'
                            ],
                            'Manual de serviço' => [
                                'manual de serviço', 'manual de servico', 'manual servico'
                            ],
                            'Certificado de calibração' => [
                                'certificado de calibração', 'certificado de calibracao', 'certificado calibracao', 'certificado'
                            ],
                            'Contrato de manutenção' => [
                                'contrato de manutenção', 'contrato de manutencao', 'contrato manutencao', 'contrato'
                            ],
                            'Fatura ou guia de aquisição' => [
                                'fatura', 'guia de aquisição', 'guia de aquisicao', 'guia de remessa', 'guia de entrega'
                            ],
                            'Declaração de conformidade' => [
                                'declaração de conformidade', 'declaracao de conformidade', 'conformidade'
                            ],
                            'Relatório técnico' => [
                                'relatório técnico', 'relatorio tecnico', 'relatorio'
                            ],
                        ];

                        function normalize_doc_type($text) {
                            $text = (string) ($text ?? '');
                            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
                            $text = mb_strtolower($text);
                            $text = preg_replace('/[^a-z0-9]+/u', ' ', $text);
                            return trim(preg_replace('/\s+/', ' ', $text));
                        }

                        function categorize_document($tipo, $categorias) {
                            $tipoNorm = normalize_doc_type($tipo);
                            foreach ($categorias as $categoria => $paterns) {
                                foreach ($paterns as $pattern) {
                                    if (mb_stripos($tipoNorm, normalize_doc_type($pattern)) !== false) {
                                        return $categoria;
                                    }
                                }
                            }
                            return null;
                        }

                        // Agrupar documentos por categoria definida
                        $docsByCategoria = [];
                        foreach ($categoriasDocumentos as $categoria => $_patterns) {
                            $docsByCategoria[$categoria] = [];
                        }
                        $others = [];

                        foreach ($documentos as $doc) {
                            $tipo = trim($doc['tipo_documento'] ?? '');
                            if ($tipo === '') {
                                $others[] = $doc;
                                continue;
                            }

                            $categoria = categorize_document($tipo, $categoriasDocumentos);
                            if ($categoria !== null) {
                                $docsByCategoria[$categoria][] = $doc;
                            } else {
                                $others[] = $doc;
                            }
                        }

                        // Helper para criar IDs seguros
                        function _safe_id($text) {
                            $s = mb_strtolower($text);
                            $s = preg_replace('/[^a-z0-9]+/u', '_', $s);
                            $s = trim($s, '_');
                            return 'doc_' . ($s === '' ? uniqid() : $s);
                        }
                        ?>

                        <div class="doc-accordion" style="width:100%;">
                            <?php foreach ($categoriasDocumentos as $tipoEsperado => $_patterns):
                                $panelId = _safe_id($tipoEsperado);
                                $hasDocs = !empty($docsByCategoria[$tipoEsperado]);
                                ?>
                                <div class="doc-item" style="border-bottom:1px solid #f1f1f1; padding:0.5rem 0;">
                                    <button type="button" class="doc-toggle" data-target="<?= $panelId ?>" aria-expanded="false" style="width:100%; text-align:left; background:none; border:none; padding:0; display:flex; justify-content:space-between; align-items:center;">
                                        <div>
                                            <strong><?= ($tipoEsperado) ?></strong>
                                            <div style="color:var(--text-muted); font-size:0.875rem;">Tipo: <?= ($tipoEsperado) ?></div>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:0.5rem;">
                                            <?php if ($hasDocs): ?>
                                                <span class="badge badge-pill" style="background:var(--primary-color); color:#fff; padding:0.35rem 0.6rem; font-size:0.85rem;"><?= count($docsByCategoria[$tipoEsperado]) ?></span>
                                            <?php endif; ?>
                                            <i class="fas fa-chevron-down doc-caret" aria-hidden="true" style="transition:transform .15s ease"></i>
                                        </div>
                                    </button>

                                    <div id="<?= $panelId ?>" class="doc-panel" style="display:none; margin-top:0.5rem;">
                                        <?php if ($hasDocs): ?>
                                            <ul style="list-style:none; padding-left:0; margin:0;">
                                                <?php foreach ($docsByCategoria[$tipoEsperado] as $doc):
                                                    $fileName = $doc['caminho_ficheiro'] ?? '';
                                                    $downloadUrl = $fileName !== '' ? '../../uploads/documentos/' . _encode_path_segments($fileName) : '';
                                                    $docId = $doc['id_documento'] ?? null;
                                                    ?>
                                                    <li style="display:flex; justify-content:space-between; align-items:center; padding:0.5rem 0;">
                                                        <div>
                                                            <strong><?= ($doc['nome_documento'] ?? $doc['tipo_documento']) ?></strong>
                                                            <div style="color:var(--text-muted); font-size:0.875rem;">Data: <?= (!empty($doc['data_documento']) ? date('d/m/Y', strtotime($doc['data_documento'])) : '-') ?></div>
                                                        </div>
                                                        <div style="display:flex;gap:0.5rem;align-items:center;">
                                                            <?php if ($downloadUrl !== ''): ?>
                                                                <a href="<?= $downloadUrl ?>" class="btn btn-sm" target="_blank" rel="noopener" download>
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">Sem ficheiro</span>
                                                            <?php endif; ?>
                                                            <?php if ($docId): ?>
                                                                <button type="button" class="btn btn-sm btn-danger doc-delete-btn" data-id="<?= $docId ?>" data-equipamento="<?= $id_equipamento ?>" title="Apagar documento">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <div style="color:var(--text-muted); padding:0.25rem 0;">Sem dados</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!empty($others)):
                        ?>
                            <hr style="margin:1rem 0;">
                            <h4>Outros Documentos</h4>
                            <ul style="list-style:none; padding-left:0; margin:0;">
                                <?php foreach ($others as $doc):
                                    $fileName = $doc['caminho_ficheiro'] ?? '';
                                    $downloadUrl = $fileName !== '' ? '../../uploads/documentos/' . _encode_path_segments($fileName) : '';
                                    $docId = $doc['id_documento'] ?? null;
                                    ?>
                                    <li style="display:flex; justify-content:space-between; align-items:center; padding:0.5rem 0; border-bottom:1px solid #f1f1f1;">
                                        <div>
                                            <strong><?= ($doc['nome_documento'] ?? $doc['tipo_documento']) ?></strong>
                                            <div style="color:var(--text-muted); font-size:0.875rem;">Tipo: <?= ($doc['tipo_documento']) ?> | Data: <?= (!empty($doc['data_documento']) ? date('d/m/Y', strtotime($doc['data_documento'])) : '-') ?></div>
                                        </div>
                                        <div style="display:flex;gap:0.5rem;align-items:center;">
                                            <?php if ($downloadUrl !== ''): ?>
                                                <a href="<?= $downloadUrl ?>" class="btn btn-sm" target="_blank" rel="noopener" download>
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Sem ficheiro</span>
                                            <?php endif; ?>
                                            <?php if ($docId): ?>
                                                <button type="button" class="btn btn-sm btn-danger doc-delete-btn" data-id="<?= $docId ?>" data-equipamento="<?= $id_equipamento ?>" title="Apagar documento">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <script>
                    // Toggle behavior for document panels
                    (function(){
                        document.querySelectorAll('.doc-toggle').forEach(function(btn){
                            btn.addEventListener('click', function(){
                                var target = btn.getAttribute('data-target');
                                var panel = document.getElementById(target);
                                if(!panel) return;
                                var expanded = btn.getAttribute('aria-expanded') === 'true';
                                if(expanded){
                                    panel.style.display = 'none';
                                    btn.setAttribute('aria-expanded','false');
                                    var caret = btn.querySelector('.doc-caret'); if(caret) caret.style.transform='rotate(0deg)';
                                } else {
                                    panel.style.display = 'block';
                                    btn.setAttribute('aria-expanded','true');
                                    var caret = btn.querySelector('.doc-caret'); if(caret) caret.style.transform='rotate(180deg)';
                                }
                            });
                        });
                    })();
                </script>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;">
                    <div class="content-card">
                        <div class="card-header">
                            <h2>Estado e Criticidade</h2>
                        </div>
                        <div style="padding: 1.5rem; display: grid; gap: 1rem;">
                            <div>
                                <strong
                                    style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Estado
                                    Atual</strong>
                                <span class="badge <?= $estadoClass ?>">
                                    <?php if ($equipamento['nome_estado'] === null) {
                                        echo "Estado não definido";
                                    } else {
                                        echo $equipamento['nome_estado'];
                                    } ?></span>
                            </div>
                            <div>

                                <strong
                                    style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Nível
                                    de Criticidade</strong>
                                <span class="badge <?= $criticidadeClass ?>">
                                    <?php if ($equipamento['nivel_criticidade'] === null) {
                                        echo "Nível não definido";
                                    } else {
                                        echo $equipamento['nivel_criticidade'];
                                    } ?></span>
                            </div>
                            <div>
                                <strong
                                    style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Próxima
                                    Manutenção</strong>
                                <p style="margin: 0; font-weight: 700; color: #f97316;">
                                    <?php if ($equipamento['proximo_manutencao_prevista'] === null) {
                                        echo "Nenhuma manutenção programada";
                                    } else {
                                        echo $equipamento['proximo_manutencao_prevista'];
                                    } ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h2>Garantia e Contrato</h2>
                        </div>
                        <div style="padding: 1.5rem; display: grid; gap: 1rem;">
                            <div>
                                <strong
                                    style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Fim
                                    da Garantia</strong>
                                <p style="margin: 0;">
                                    <?php if (empty($garantia_contrato) || $garantia_contrato['data_fim_garantia'] === null) {
                                        echo "Garantia não definida";
                                    } else {
                                        echo date('d/m/Y', strtotime($garantia_contrato['data_fim_garantia']));
                                    } ?>
                                </p>
                            </div>
                            <div>
                                <strong
                                    style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Fornecedor
                                    Principal</strong>
                                <p style="margin: 0;"><a href="#"
                                        style="color: var(--primary-color); text-decoration: none;"></a>
                                    <?php if (empty($garantia_contrato) || $garantia_contrato['fornecedor_associado'] === null) {
                                        echo "Fornecedor não definido";
                                    } else {
                                        echo ($garantia_contrato['fornecedor_associado']);
                                    } ?>
                                </p>
                            </div>
                            <div>
                                <strong
                                    style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Tipo
                                    de Contrato</strong>
                                <p style="margin: 0;">
                                    <?php if (empty($garantia_contrato) || $garantia_contrato['tipo_contrato'] === null) {
                                        echo "Tipo de contrato não definido";
                                    } else {
                                        echo ($garantia_contrato['tipo_contrato']);
                                    } ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                                </div>


            </div>

            <!-- SEÇÃO DE UTILIZADORES -->
            <?php 
            $utilizadores = $dbManager->getUtilizadoresPorEquipamento($id_equipamento);
            $status_disponibilidade = count($utilizadores) === 0 ? 'Disponível' : 'Em Uso';
            ?>
            <div class="content-container" style="margin-top:2rem;">
                <div class="content-card">
                    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <h2>Responsáveis / Utilizadores</h2>
                            <span class="badge" style="background:<?= $status_disponibilidade === 'Disponível' ? '#28a745' : '#ffc107' ?>; padding:0.35rem 0.6rem; margin-top:0.25rem;">
                                <?= $status_disponibilidade ?>
                            </span>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                            <i class="fas fa-plus"></i> Atribuir Utilizador
                        </button>
                    </div>

                    <div class="table-container">
                        <?php if (!empty($utilizadores)): ?>
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Tipo de Relação</th>
                                        <th>Data de Atribuição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($utilizadores as $user): ?>
                                        <tr>
                                            <td><?= ($user['nome']) ?></td>
                                            <td><?= ($user['email']) ?></td>
                                            <td>
                                                <span class="badge badge-danger"><?= ($user['tipo_relacao']) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($user['data_atribuicao'])) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger user-delete-btn" data-id="<?= $user['id_utilizador_equipamento'] ?>" data-equipamento="<?= $id_equipamento ?>" title="Remover utilizador">
                                                    <i class="fas fa-trash"></i> Remover
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div style="padding:2rem; text-align:center; color:var(--text-muted);">
                                <i class="fas fa-user-slash" style="font-size:3rem; margin-bottom:1rem; display:block; opacity:0.5;"></i>
                                <p>Nenhum utilizador atribuído</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- MODAL DE ATRIBUIÇÃO DE UTILIZADOR -->
            <div class="modal fade" id="assignUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Atribuir Utilizador ao Equipamento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="assignUserForm">
                            <div class="modal-body">
                                <input type="hidden" name="id_equipamento" value="<?= $id_equipamento ?>">
                                <input type="hidden" name="acao" value="assign">
                                
                                <div class="mb-3">
                                    <label for="id_utilizador" class="form-label">Utilizador</label>
                                    <select id="id_utilizador" name="id_utilizador" class="form-select" required>
                                        <option value="">Selecione um utilizador...</option>
                                    </select>
                                </div>

                                <div class="mb-3 ">
                                    <label for="tipo_relacao" class="form-label">Tipo de Relação</label>
                                    <select id="tipo_relacao" name="tipo_relacao" class="form-select" required>
                                        <option value="Responsável">Responsável</option>
                                        <option value="Operador" selected>Operador</option>
                                        <option value="Técnico">Técnico</option>
                                        <option value="Gestor">Gestor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Atribuir</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE CONFIRMAÇÃO PARA APAGAR DOCUMENTO -->
        <div class="modal fade" id="deleteDocumentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Apagar Documento</h5>
                    </div>
                    <div class="modal-body">
                        <div style="text-align:center; padding:1rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size:3rem; color:#dc3545; margin-bottom:1rem; display:block;"></i>
                            <p style="font-weight:bold; margin-bottom:1rem;">Tem a certeza que deseja apagar este documento?</p>
                            <p style="color:var(--text-muted); font-size:0.9rem;">Esta ação não pode ser desfeita.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteDocBtn">Apagar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE CONFIRMAÇÃO PARA REMOVER UTILIZADOR -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Remover Utilizador</h5>
                    </div>
                    <div class="modal-body">
                        <div style="text-align:center; padding:1rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size:3rem; color:#dc3545; margin-bottom:1rem; display:block;"></i>
                            <p style="font-weight:bold; margin-bottom:1rem;">Tem a certeza que deseja remover este utilizador?</p>
                            <p style="color:var(--text-muted); font-size:0.9rem;">O utilizador deixará de ter acesso a este equipamento.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteUserBtn">Remover</button>
                    </div>
                </div>
            </div>
        </div>
</main>

<!-- JavaScript para delete documentos e gerenciar utilizadores -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const equipId = <?= json_encode($id_equipamento) ?>;

    // Carregar lista de utilizadores
    function loadUtilizadores() {
        fetch('./utilizador_equipamento_manage.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'acao=get_list&id_equipamento=' + equipId
        })
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('id_utilizador');
            if (select && data.sucesso && data.utilizadores) {
                const usedIds = new Set(data.utilizadores.map(u => u.id_utilizador));
                // Carregar todos os utilizadores e filtrar
                fetch('../../includes/get_utilizadores.php')
                    .then(r => r.json())
                    .then(users => {
                        select.innerHTML = '<option value="">Selecione um utilizador...</option>';
                        users.forEach(u => {
                            if (!usedIds.has(u.id_utilizador)) {
                                const opt = document.createElement('option');
                                opt.value = u.id_utilizador;
                                opt.textContent = u.nome + ' (' + u.email + ')';
                                select.appendChild(opt);
                            }
                        });
                    });
            }
        })
        .catch(e => console.error('Erro ao carregar utilizadores:', e));
    }

    // ===== DELETE DOCUMENTO =====
    let currentDocId = null;
    let currentDeleteDocModal = null;
    
    // Inicializar modal
    const deleteDocModalEl = document.getElementById('deleteDocumentModal');
    if (deleteDocModalEl) {
        currentDeleteDocModal = new bootstrap.Modal(deleteDocModalEl);
    }

    // Attach click handlers ao botões de delete
    function attachDocDeleteHandlers() {
        document.querySelectorAll('.doc-delete-btn').forEach(btn => {
            btn.removeEventListener('click', docDeleteClickHandler);
            btn.addEventListener('click', docDeleteClickHandler);
        });
    }

    function docDeleteClickHandler(e) {
        e.preventDefault();
        currentDocId = this.dataset.id;
        console.log('Delete documento ID:', currentDocId);
        if (currentDeleteDocModal) {
            currentDeleteDocModal.show();
        }
    }

    // Confirmar delete documento
    const confirmDeleteDocBtn = document.getElementById('confirmDeleteDocBtn');
    if (confirmDeleteDocBtn) {
        confirmDeleteDocBtn.addEventListener('click', function() {
            if (!currentDocId) {
                alert('Nenhum documento selecionado');
                return;
            }
            
            const form = new FormData();
            form.append('id_documento', currentDocId);
            form.append('id_equipamento', equipId);

            fetch('./documento_delete.php', {
                method: 'POST',
                body: form
            })
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    if (currentDeleteDocModal) {
                        currentDeleteDocModal.hide();
                    }
                    alert('Documento apagado com sucesso');
                    location.reload();
                } else {
                    alert('Erro: ' + (data.erro || 'Desconhecido'));
                }
            })
            .catch(e => {
                console.error('Erro:', e);
                alert('Erro: ' + e.message);
            });
        });
    }

    // ===== DELETE UTILIZADOR =====
    let currentUserId = null;
    let currentDeleteUserModal = null;
    
    const deleteUserModalEl = document.getElementById('deleteUserModal');
    if (deleteUserModalEl) {
        currentDeleteUserModal = new bootstrap.Modal(deleteUserModalEl);
    }

    function attachUserDeleteHandlers() {
        document.querySelectorAll('.user-delete-btn').forEach(btn => {
            btn.removeEventListener('click', userDeleteClickHandler);
            btn.addEventListener('click', userDeleteClickHandler);
        });
    }

    function userDeleteClickHandler(e) {
        e.preventDefault();
        currentUserId = this.dataset.id;
        console.log('Delete user ID:', currentUserId);
        if (currentDeleteUserModal) {
            currentDeleteUserModal.show();
        }
    }

    // Confirmar delete utilizador
    const confirmDeleteUserBtn = document.getElementById('confirmDeleteUserBtn');
    if (confirmDeleteUserBtn) {
        confirmDeleteUserBtn.addEventListener('click', function() {
            if (!currentUserId) {
                alert('Nenhum utilizador selecionado');
                return;
            }

            const form = new FormData();
            form.append('acao', 'unassign');
            form.append('id_utilizador_equipamento', currentUserId);
            form.append('id_equipamento', equipId);

            fetch('./utilizador_equipamento_manage.php', {
                method: 'POST',
                body: form
            })
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    if (currentDeleteUserModal) {
                        currentDeleteUserModal.hide();
                    }
                    alert('Utilizador removido com sucesso');
                    location.reload();
                } else {
                    alert('Erro: ' + (data.erro || 'Desconhecido'));
                }
            })
            .catch(e => {
                console.error('Erro:', e);
                alert('Erro: ' + e.message);
            });
        });
    }

    // ===== ASSIGN UTILIZADOR =====
    const assignUserForm = document.getElementById('assignUserForm');
    if (assignUserForm) {
        assignUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = new FormData(this);
            
            fetch('./utilizador_equipamento_manage.php', {
                method: 'POST',
                body: form
            })
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    const assignModal = bootstrap.Modal.getInstance(document.getElementById('assignUserModal'));
                    if (assignModal) {
                        assignModal.hide();
                    }
                    alert('Utilizador atribuído com sucesso');
                    location.reload();
                } else {
                    alert('Erro: ' + (data.erro || 'Desconhecido'));
                }
            })
            .catch(e => {
                console.error('Erro:', e);
                alert('Erro: ' + e.message);
            });
        });
    }

    // ===== MODAL EVENTS =====
    const assignUserModal = document.getElementById('assignUserModal');
    if (assignUserModal) {
        assignUserModal.addEventListener('show.bs.modal', loadUtilizadores);
    }

    // Attach handlers on page load
    attachDocDeleteHandlers();
    attachUserDeleteHandlers();

    // Re-attach handlers after any DOM updates (usar MutationObserver se necessário)
    setInterval(() => {
        attachDocDeleteHandlers();
        attachUserDeleteHandlers();
    }, 1000);
});
</script>
<?php include '../../includes/footer.php'; ?>