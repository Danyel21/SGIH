<?php

require_once 'db_connect.php';

class DatabaseManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Funções para a tabela UTILIZADOR
    public function getUtilizadores($ativo = null, $search = null) {
        $sql = "SELECT id_utilizador, nome, email, funcao, departamento, ativo FROM UTILIZADOR";
        $params = [];
        $conditions = [];

        if ($ativo !== null) {
            $conditions[] = "ativo = :ativo";
            $params[":ativo"] = $ativo;
        }

        if (!empty($search)) {
            $conditions[] = "(LOWER(nome) LIKE LOWER(:search) OR LOWER(email) LIKE LOWER(:search) OR LOWER(funcao) LIKE LOWER(:search) OR LOWER(departamento) LIKE LOWER(:search))";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUtilizadorById(int $id_utilizador) {
        $sql = "SELECT id_utilizador, nome, email, funcao, departamento, ativo FROM UTILIZADOR WHERE id_utilizador = :id_utilizador";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilizador' => $id_utilizador]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Funções para a tabela UTILIZADOR_EQUIPAMENTO
    public function getUtilizadorEquipamentos() {
        $sql = "SELECT
                    ue.id_utilizador_equipamento,
                    u.nome AS nome_utilizador,
                    e.designacao AS nome_equipamento,
                    ue.tipo_relacao,
                    ue.data_atribuicao
                FROM UTILIZADOR_EQUIPAMENTO ue
                JOIN UTILIZADOR u ON ue.id_utilizador = u.id_utilizador
                JOIN EQUIPAMENTO e ON ue.id_equipamento = e.id_equipamento;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Retorna true se equipamento tem associação ativa (em uso)
    public function isEquipamentoEmUso(int $id_equipamento): bool {
        $sql = "SELECT COUNT(*) FROM UTILIZADOR_EQUIPAMENTO WHERE id_equipamento = :id AND ativo = 1 AND data_atribuicao <= CURDATE() AND (data_fim IS NULL OR data_fim >= CURDATE())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_equipamento]);
        return $stmt->fetchColumn() > 0;
    }

    // Obtém associações ativas de utilizadores para um equipamento
    public function getUtilizadoresPorEquipamento(int $id_equipamento) {
        $sql = "SELECT ue.*, u.nome, u.email FROM UTILIZADOR_EQUIPAMENTO ue JOIN UTILIZADOR u ON ue.id_utilizador = u.id_utilizador WHERE ue.id_equipamento = :id AND ue.ativo = 1 ORDER BY ue.data_atribuicao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_equipamento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Desativa (marca como não ativo) todas as associações de um equipamento
    public function deactivateUtilizadorEquipamentoByEquipamento(int $id_equipamento) {
        $sql = "UPDATE UTILIZADOR_EQUIPAMENTO SET ativo = 0 WHERE id_equipamento = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id_equipamento]);
    }

    // Funções para a tabela LOCALIZACAO
    public function getLocalizacoes($filters = [], $ativo = null) {
        $sql = "SELECT id_localizacao, edificio, piso, servico_departamento, sala_gabinete, 
                       (SELECT COUNT(*) FROM EQUIPAMENTO e WHERE e.id_localizacao = LOCALIZACAO.id_localizacao) AS equipamentos_count 
                FROM LOCALIZACAO";
        $params = [];
        $conditions = [];

        if ($ativo !== null) {
            $conditions[] = "ativo = :ativo";
            $params[":ativo"] = $ativo;
        }

        // 🔎 SEARCH (edifício, piso, serviço, sala)
        if (!empty($filters['search'])) {
            $conditions[] = "(
                LOWER(edificio) LIKE LOWER(:search) OR
                LOWER(piso) LIKE LOWER(:search) OR
                LOWER(servico_departamento) LIKE LOWER(:search) OR
                LOWER(sala_gabinete) LIKE LOWER(:search)
            )";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        // 📂 CATEGORIA
        if (!empty($filters['servico_departamento'])) {
            $conditions[] = "servico_departamento = :servico_departamento";
            $params[':servico_departamento'] = $filters['servico_departamento'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY id_localizacao DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getLocalizacaoById(int $id_localizacao) {
        $sql = "SELECT id_localizacao, edificio, piso, servico_departamento, sala_gabinete FROM LOCALIZACAO WHERE id_localizacao = :id_localizacao";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_localizacao' => $id_localizacao]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countEquipamentosPorLocalizacao(int $id_localizacao) {
        $sql = "SELECT COUNT(*) FROM EQUIPAMENTO WHERE id_localizacao = :id_localizacao";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_localizacao' => $id_localizacao]);
        return (int) $stmt->fetchColumn();
    }

    public function getEquipamentosPorLocalizacao(int $id_localizacao) {
        $sql = "SELECT
                    e.id_equipamento,
                    e.designacao,
                    e.codigo_interno,
                    ce.nome_categoria,
                    c.nivel_criticidade,
                    ee.nome_estado
                FROM EQUIPAMENTO e
                JOIN CATEGORIA_EQUIPAMENTO ce ON e.id_categoria = ce.id_categoria
                JOIN CRITICIDADE c ON e.id_criticidade = c.id_criticidade
                JOIN ESTADO_EQUIPAMENTO ee ON e.id_estado = ee.id_estado
                WHERE e.id_localizacao = :id_localizacao
                ORDER BY e.id_equipamento DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_localizacao' => $id_localizacao]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLocalizacoesEquipamentos(int $id_localizacao) {
        return $this->getEquipamentosPorLocalizacao($id_localizacao);
    }

    // Remover uma localização pelo ID
    public function deleteLocalizacao(int $id_localizacao) {
        $equipamentosCount = $this->countEquipamentosPorLocalizacao($id_localizacao);
        if ($equipamentosCount > 0) {
            throw new Exception('A localização não pode ser eliminada porque existem equipamentos associados. Primeiro remova ou altere a localização desses equipamentos.');
        }

        $sql = "DELETE FROM LOCALIZACAO WHERE id_localizacao = :id_localizacao";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_localizacao' => $id_localizacao]);
    }
    public function updateEquipamento(int $id_equipamento, array $fields): bool
{
    if (empty($fields)) {
        return false;
    }

    $setParts = [];
    $params = [':id' => $id_equipamento];
    foreach ($fields as $column => $value) {
        $setParts[] = "`$column` = :$column";
        $params[":$column"] = $value;
    }

    $sql = 'UPDATE EQUIPAMENTO SET ' . implode(', ', $setParts) . ' WHERE id_equipamento = :id';
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
}

    // Funções para a tabela CATEGORIA_EQUIPAMENTO
    public function getCategoriasEquipamento($ativo = null) {
        $sql = "SELECT id_categoria, nome_categoria FROM CATEGORIA_EQUIPAMENTO";
        $params = [];
        if ($ativo !== null) {
            $sql .= " WHERE ativo = :ativo";
            $params[":ativo"] = $ativo;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Funções para a tabela CRITICIDADE
    public function getCriticidades() {
        $sql = "SELECT id_criticidade, nivel_criticidade, descricao FROM CRITICIDADE;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Funções para a tabela ESTADO_EQUIPAMENTO
    public function getEstadosEquipamento() {
        $sql = "SELECT id_estado, nome_estado FROM ESTADO_EQUIPAMENTO;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Funções para a tabela TIPO_DOCUMENTO
    public function getTiposDocumento() {
        $sql = "SELECT id_tipo_documento, nome_tipo FROM TIPO_DOCUMENTO;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Funções para a tabela FORNECEDOR
    public function getFornecedores($ativo = 1) {
        $sql = "SELECT id_fornecedor, nome_empresa, nif, tipo_fornecedor, contacto_telefonico, email, morada, codigo_postal, localidade, website, pessoa_contacto, telefone_pessoa_contacto, observacoes FROM FORNECEDOR";
        $params = [];
        if ($ativo !== null) {
            $sql .= " WHERE ativo = :ativo";
            $params[":ativo"] = $ativo;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getFornecedorById(int $id_fornecedor) {
        $sql = "SELECT id_fornecedor, nome_empresa, nif, tipo_fornecedor, contacto_telefonico, email, morada, codigo_postal, localidade, website, pessoa_contacto, telefone_pessoa_contacto, observacoes, ativo FROM FORNECEDOR WHERE id_fornecedor = :id_fornecedor";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_fornecedor' => $id_fornecedor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFornecedoresComFiltro($filtros = []) {
        $sql = "SELECT id_fornecedor, nome_empresa, nif, tipo_fornecedor, contacto_telefonico, email, morada, codigo_postal, localidade, website, pessoa_contacto, telefone_pessoa_contacto, observacoes FROM FORNECEDOR WHERE ativo = 1";
        $params = [];

        // Filtro de pesquisa (nome, NIF, email, contacto)
        if (!empty($filtros['search'])) {
            $sql .= " AND (
                LOWER(nome_empresa) LIKE LOWER(:search) OR
                LOWER(nif) LIKE LOWER(:search) OR
                LOWER(email) LIKE LOWER(:search) OR
                LOWER(contacto_telefonico) LIKE LOWER(:search)
            )";
            $params[':search'] = '%' . $filtros['search'] . '%';
        }

        // Filtro por tipo
        if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'Todos') {
            $sql .= " AND tipo_fornecedor = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        $sql .= " ORDER BY nome_empresa ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function deactivateFornecedor(int $id_fornecedor) {
        $sql = "UPDATE FORNECEDOR SET ativo = 0 WHERE id_fornecedor = :id_fornecedor";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_fornecedor' => $id_fornecedor]);
    }

    // Funções para a tabela EQUIPAMENTO
   public function getEquipamentos($filters = []) {

    $sql = "SELECT
                e.id_equipamento,
                e.designacao,
                e.codigo_interno,
                e.ano_fabrico,
                e.observacoes,
                e.custo_aquisicao,
                ce.nome_categoria,
                c.id_criticidade,
                c.nivel_criticidade,
                ee.id_estado,
                ee.nome_estado,
                l.servico_departamento AS localizacao_servico,
                l.sala_gabinete AS localizacao_sala,
                l.id_localizacao,
                e.marca,
                e.modelo,
                e.numero_serie,
                e.data_aquisicao,
                e.tipo_entrada,
                m.proximo_manutencao_prevista,
                gc.data_fim_garantia,
                f.nome_empresa AS nome_fornecedor,
                f.id_fornecedor
            FROM EQUIPAMENTO e
            JOIN CATEGORIA_EQUIPAMENTO ce ON e.id_categoria = ce.id_categoria
            JOIN CRITICIDADE c ON e.id_criticidade = c.id_criticidade
            JOIN ESTADO_EQUIPAMENTO ee ON e.id_estado = ee.id_estado
            JOIN LOCALIZACAO l ON e.id_localizacao = l.id_localizacao
            LEFT JOIN MANUTENCAO m ON e.id_equipamento = m.id_equipamento
            LEFT JOIN GARANTIA_CONTRATO gc ON e.id_equipamento = gc.id_equipamento
            LEFT JOIN EQUIPAMENTO_FORNECEDOR ef ON e.id_equipamento = ef.id_equipamento
            LEFT JOIN FORNECEDOR f ON ef.id_fornecedor = f.id_fornecedor
            WHERE 1=1";

    $params = [];

    // 🔎 SEARCH (designação, código, marca, modelo, número de série, localização, fornecedor, categoria, criticidade)
    if (!empty($filters['search'])) {
        $sql .= " AND (
            LOWER(e.designacao) LIKE LOWER(:search) OR
            LOWER(e.codigo_interno) LIKE LOWER(:search) OR
            LOWER(e.marca) LIKE LOWER(:search) OR
            LOWER(e.modelo) LIKE LOWER(:search) OR
            LOWER(e.numero_serie) LIKE LOWER(:search) OR
            LOWER(l.servico_departamento) LIKE LOWER(:search) OR
            LOWER(l.sala_gabinete) LIKE LOWER(:search) OR
            LOWER(f.nome_empresa) LIKE LOWER(:search) OR
            LOWER(ce.nome_categoria) LIKE LOWER(:search) OR
            LOWER(c.nivel_criticidade) LIKE LOWER(:search)
        )";

        $params[':search'] = '%' . $filters['search'] . '%';
    }

    // 📂 CATEGORIA
    if (isset($filters['categoria']) && $filters['categoria'] !== '') {
        $sql .= " AND ce.id_categoria = :categoria";
        $params[':categoria'] = $filters['categoria'];
    }

    // 📊 ESTADO
    if (isset($filters['estado']) && $filters['estado'] !== '') {
        $sql .= " AND ee.id_estado = :estado";
        $params[':estado'] = $filters['estado'];
    }

    // 🚩 LOCALIZAÇÃO
    if (isset($filters['localizacao']) && $filters['localizacao'] !== '') {
        $sql .= " AND e.id_localizacao = :localizacao";
        $params[':localizacao'] = $filters['localizacao'];
    }

    // 🏢 FORNECEDOR
    if (isset($filters['fornecedor']) && $filters['fornecedor'] !== '') {
        $sql .= " AND f.id_fornecedor = :fornecedor";
        $params[':fornecedor'] = $filters['fornecedor'];
    }

    // 🧭 CRITICIDADE
    if (isset($filters['criticidade']) && $filters['criticidade'] !== '') {
        $sql .= " AND c.id_criticidade = :criticidade";
        $params[':criticidade'] = $filters['criticidade'];
    }

    $sort = $filters['sort'] ?? '';
    switch ($sort) {
        case 'codigo':
            $sql .= " ORDER BY e.codigo_interno ASC";
            break;
        case 'designacao':
            $sql .= " ORDER BY e.designacao ASC";
            break;
        case 'categoria':
            $sql .= " ORDER BY ce.nome_categoria ASC";
            break;
        case 'criticidade':
            $sql .= " ORDER BY c.nivel_criticidade ASC";
            break;
        case 'fornecedor':
            $sql .= " ORDER BY f.nome_empresa ASC";
            break;
        case 'localizacao':
            $sql .= " ORDER BY l.servico_departamento ASC, l.sala_gabinete ASC";
            break;
        default:
            $sql .= " ORDER BY e.id_equipamento DESC";
            break;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    // 📍 LOCALIZAÇÃO
    if (!empty($filters['localizacao'])) {
        $sql .= " AND e.id_localizacao = :localizacao";
        $params[':localizacao'] = $filters['localizacao'];
    }

    $sql .= " ORDER BY e.id_equipamento DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

   



public function getEquipamentoByCodigo(int $id_equipamento) {
    $sql = "SELECT
                e.id_equipamento,
                e.designacao,
                e.codigo_interno,
                e.ano_fabrico,
                e.custo_aquisicao,
                e.observacoes,
                ce.nome_categoria,
                ce.id_categoria,
                c.nivel_criticidade,
                c.id_criticidade,
                ee.nome_estado,
                ee.id_estado,
                l.servico_departamento AS localizacao_servico,
                l.sala_gabinete AS localizacao_sala,
                l.id_localizacao,
                e.marca,
                e.modelo,
                e.numero_serie,
                e.data_aquisicao,
                e.tipo_entrada,
                m.proximo_manutencao_prevista,
                gc.data_fim_garantia,
                f.id_fornecedor,
                f.nome_empresa AS nome_fornecedor
            FROM EQUIPAMENTO e
            JOIN CATEGORIA_EQUIPAMENTO ce ON e.id_categoria = ce.id_categoria
            JOIN CRITICIDADE c ON e.id_criticidade = c.id_criticidade
            JOIN ESTADO_EQUIPAMENTO ee ON e.id_estado = ee.id_estado
            JOIN LOCALIZACAO l ON e.id_localizacao = l.id_localizacao
            LEFT JOIN GARANTIA_CONTRATO gc ON e.id_equipamento = gc.id_equipamento
            LEFT JOIN MANUTENCAO m ON e.id_equipamento = m.id_equipamento
            LEFT JOIN EQUIPAMENTO_FORNECEDOR ef ON e.id_equipamento = ef.id_equipamento
            LEFT JOIN FORNECEDOR f ON ef.id_fornecedor = f.id_fornecedor
            WHERE e.id_equipamento = :id_equipamento
            LIMIT 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id_equipamento', $id_equipamento, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    // Funções para a tabela COMPONENTE_EQUIPAMENTO
    public function getComponentesEquipamento() {
        $sql = "SELECT
                    co.id_componente,
                    e.designacao AS equipamento_principal,
                    co.designacao_componente,
                    co.codigo_interno_componente,
                    co.marca_componente,
                    co.modelo_componente,
                    co.numero_serie_componente,
                    co.observacoes,
                    co.data_aquisicao_componente
                FROM COMPONENTE_EQUIPAMENTO co
                JOIN EQUIPAMENTO e ON co.id_equipamento_principal = e.id_equipamento;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    public function getComponentesEquipamento_by_id(int $id_equipamento) {
        $sql = "SELECT
                    co.id_componente,
                    e.designacao AS equipamento_principal,
                    co.codigo_interno_componente,
                    co.designacao_componente,
                    co.marca_componente,
                    co.modelo_componente,
                    co.numero_serie_componente,
                    co.observacoes,
                    co.data_aquisicao_componente
                FROM COMPONENTE_EQUIPAMENTO co
                JOIN EQUIPAMENTO e ON co.id_equipamento_principal = e.id_equipamento
                WHERE co.id_equipamento_principal = :id_equipamento;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_equipamento', $id_equipamento, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComponenteById(int $id_componente) {
        $sql = "SELECT
                    co.*,
                    e.designacao AS equipamento_principal
                FROM COMPONENTE_EQUIPAMENTO co
                JOIN EQUIPAMENTO e ON co.id_equipamento_principal = e.id_equipamento
                WHERE co.id_componente = :id_componente;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_componente', $id_componente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteComponente(int $id_componente) {
        $stmt = $this->pdo->prepare("DELETE FROM COMPONENTE_EQUIPAMENTO WHERE id_componente = :id_componente");
        return $stmt->execute([':id_componente' => $id_componente]);
    }

    // Funções para a tabela EQUIPAMENTO_FORNECEDOR
    public function getEquipamentoFornecedores() {
        $sql = "SELECT
                    ef.id_equipamento_fornecedor,
                    e.designacao AS nome_equipamento,
                    f.nome_empresa AS nome_fornecedor,
                    ef.tipo_relacao,
                    ef.data_associacao
                FROM EQUIPAMENTO_FORNECEDOR ef
                JOIN EQUIPAMENTO e ON ef.id_equipamento = e.id_equipamento
                JOIN FORNECEDOR f ON ef.id_fornecedor = f.id_fornecedor;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getEquipamentoFornecedores_by_id(int $id_equipamento) {
        $sql = "SELECT
                    ef.id_equipamento_fornecedor,
                    ef.id_fornecedor,
                    f.nome_empresa,
                    ef.tipo_relacao,
                    ef.data_associacao,
                    ef.observacoes
                FROM EQUIPAMENTO_FORNECEDOR ef
                JOIN FORNECEDOR f ON ef.id_fornecedor = f.id_fornecedor
                WHERE ef.id_equipamento = :id_equipamento;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_equipamento' => $id_equipamento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Funções para a tabela DOCUMENTO
    public function getDocumentos() {
        $sql = "SELECT
                    d.id_documento,
                    e.designacao AS equipamento_associado,
                    td.nome_tipo AS tipo_documento,
                    f.nome_empresa AS fornecedor_associado,
                    d.nome_documento,
                    d.data_documento,
                    d.data_validade,
                    d.tipo_ficheiro
                FROM DOCUMENTO d
                JOIN EQUIPAMENTO e ON d.id_equipamento = e.id_equipamento
                JOIN TIPO_DOCUMENTO td ON d.id_tipo_documento = td.id_tipo_documento
                LEFT JOIN FORNECEDOR f ON d.id_fornecedor = f.id_fornecedor;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getDocumentos_by_equipamento(int $id_equipamento) {
        $sql = "SELECT
                    d.id_documento,
                    d.id_tipo_documento,
                    td.nome_tipo AS tipo_documento,
                    d.nome_documento,
                    d.data_documento,
                    d.data_validade,
                    d.tipo_ficheiro,
                    d.caminho_ficheiro,
                    d.tamanho_ficheiro,
                    d.observacoes
                FROM DOCUMENTO d
                JOIN TIPO_DOCUMENTO td ON d.id_tipo_documento = td.id_tipo_documento
                WHERE d.id_equipamento = :id_equipamento
                ORDER BY d.data_documento DESC;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_equipamento' => $id_equipamento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Funções para a tabela GARANTIA_CONTRATO
    public function getGarantiasContratos() {
        $sql = "SELECT
                    gc.id_garantia,
                    e.codigo_interno AS equipamento_associado,
                    f.nome_empresa AS fornecedor_associado,
                    gc.data_inicio_garantia,
                    gc.data_fim_garantia,
                    gc.tipo_contrato,
                    gc.periodicidade_manutencao,
                    gc.custo_contrato
                FROM GARANTIA_CONTRATO gc
                JOIN EQUIPAMENTO e ON gc.id_equipamento = e.id_equipamento
                JOIN FORNECEDOR f ON gc.id_fornecedor = f.id_fornecedor;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function deleteGarantiaContrato(int $id_garantia) {
        $stmt = $this->pdo->prepare("DELETE FROM GARANTIA_CONTRATO WHERE id_garantia = :id_garantia");
        return $stmt->execute([':id_garantia' => $id_garantia]);
    }

    public function getGarantiasContratos_by_id(int $id_equipamento) {
        $sql = "SELECT
                    gc.id_garantia,
                    e.codigo_interno AS equipamento_associado,
                    f.nome_empresa AS fornecedor_associado,
                    gc.data_inicio_garantia,
                    gc.data_fim_garantia,
                    gc.tipo_contrato,
                    gc.periodicidade_manutencao,
                    gc.custo_contrato
                FROM GARANTIA_CONTRATO gc
                JOIN EQUIPAMENTO e ON gc.id_equipamento = e.id_equipamento
                JOIN FORNECEDOR f ON gc.id_fornecedor = f.id_fornecedor
                WHERE gc.id_equipamento = :id_equipamento;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_equipamento' => $id_equipamento]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : [];
    }

    // Funções para a tabela MANUTENCAO
    public function getManutencoes() {
        $sql = "SELECT
                    m.id_manutencao,
                    e.codigo_interno AS equipamento_manutencao,
                    f.nome_empresa AS fornecedor_manutencao,
                    m.tipo_manutencao,
                    m.data_manutencao,
                    m.descricao_trabalho,
                    m.custo_manutencao,
                    m.proximo_manutencao_prevista
                FROM MANUTENCAO m
                JOIN EQUIPAMENTO e ON m.id_equipamento = e.id_equipamento
                LEFT JOIN FORNECEDOR f ON m.id_fornecedor = f.id_fornecedor;";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    public function getManutencoes_by_id(int $id_equipamento) {
        $sql = "SELECT
                    m.id_manutencao,
                    e.designacao AS equipamento_manutencao,
                    f.nome_empresa AS fornecedor_manutencao,
                    m.tipo_manutencao,
                    m.data_manutencao,
                    m.descricao_trabalho,
                    m.custo_manutencao,
                    m.proximo_manutencao_prevista
                FROM MANUTENCAO m
                JOIN EQUIPAMENTO e ON m.id_equipamento = e.id_equipamento
                LEFT JOIN FORNECEDOR f ON m.id_fornecedor = f.id_fornecedor
                WHERE m.id_equipamento = :id_equipamento;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_equipamento' => $id_equipamento]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteManutencao(int $id_manutencao) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("DELETE FROM DOCUMENTO_MANUTENCAO WHERE id_manutencao = :id_manutencao;");
            $stmt->execute(['id_manutencao' => $id_manutencao]);

            $stmt = $this->pdo->prepare("DELETE FROM MANUTENCAO WHERE id_manutencao = :id_manutencao;");
            $stmt->execute(['id_manutencao' => $id_manutencao]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getDocumentosManutencao_by_id(int $id_manutencao) {
        $sql = "SELECT
                    dm.id_documento_manutencao,
                    dm.nome_documento,
                    dm.tipo_documento,
                    dm.caminho_arquivo,
                    dm.tamanho_arquivo,
                    dm.data_upload,
                    dm.observacoes
                FROM DOCUMENTO_MANUTENCAO dm
                WHERE dm.id_manutencao = :id_manutencao;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_manutencao' => $id_manutencao]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function componenteAssociado(int $id_equipamento): bool {
        $sql = "SELECT COUNT(*) FROM COMPONENTE_EQUIPAMENTO WHERE id_equipamento_principal = :id_equipamento";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_equipamento' => $id_equipamento]);
        return $stmt->fetchColumn() > 0;
    }


   
}

$dbManager = new DatabaseManager($pdo);




?>
