<?php

require_once 'db_connect.php';

class DatabaseINSERT {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // =========================
    // UTILIZADOR
    // =========================
    public function insertUtilizador($nome, $email, $funcao, $departamento, $password_hash, $ativo = 1) {
        $sql = "INSERT INTO UTILIZADOR (nome, email, funcao, departamento, password_hash, ativo)
                VALUES (:nome, :email, :funcao, :departamento, :password_hash, :ativo)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':funcao' => $funcao,
            ':departamento' => $departamento,
            ':password_hash' => $password_hash,
            ':ativo' => $ativo
        ]);
    }

    public function updateUtilizador($id_utilizador, $nome, $email, $funcao, $departamento, $password_hash = null, $ativo = 1) {
        $sql = "UPDATE UTILIZADOR SET nome = :nome, email = :email, funcao = :funcao, departamento = :departamento, ativo = :ativo";
        if ($password_hash !== null) {
            $sql .= ", password_hash = :password_hash";
        }
        $sql .= " WHERE id_utilizador = :id_utilizador";

        $params = [
            ':nome' => $nome,
            ':email' => $email,
            ':funcao' => $funcao,
            ':departamento' => $departamento,
            ':ativo' => $ativo,
            ':id_utilizador' => $id_utilizador
        ];

        if ($password_hash !== null) {
            $params[':password_hash'] = $password_hash;
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function deactivateUtilizador($id_utilizador) {
        $sql = "UPDATE UTILIZADOR SET ativo = 0 WHERE id_utilizador = :id_utilizador";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id_utilizador' => $id_utilizador]);
    }

    // =========================
    // LOCALIZAÇÃO
    // =========================
    public function insertLocalizacao($edificio, $piso, $servico_departamento, $sala_gabinete) {
        $sql = "INSERT INTO LOCALIZACAO (edificio, piso, servico_departamento, sala_gabinete)
                VALUES (:edificio, :piso, :servico, :sala)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':edificio' => $edificio,
            ':piso' => $piso,
            ':servico' => $servico_departamento,
            ':sala' => $sala_gabinete
        ]);
    }

    public function updateLocalizacao($id_localizacao, $edificio, $piso, $servico_departamento, $sala_gabinete) {
        $sql = "UPDATE LOCALIZACAO SET edificio = :edificio, piso = :piso, servico_departamento = :servico, sala_gabinete = :sala WHERE id_localizacao = :id_localizacao";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':edificio' => $edificio,
            ':piso' => $piso,
            ':servico' => $servico_departamento,
            ':sala' => $sala_gabinete,
            ':id_localizacao' => $id_localizacao
        ]);
    }

    // =========================
    // CATEGORIA EQUIPAMENTO
    // =========================
    public function insertCategoriaEquipamento($nome_categoria) {
        $sql = "INSERT INTO CATEGORIA_EQUIPAMENTO (nome_categoria)
                VALUES (:nome)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':nome' => $nome_categoria]);
    }

    // =========================
    // CRITICIDADE
    // =========================
    public function insertCriticidade($nivel, $descricao) {
        $sql = "INSERT INTO CRITICIDADE (nivel_criticidade, descricao)
                VALUES (:nivel, :descricao)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nivel' => $nivel,
            ':descricao' => $descricao
        ]);
    }

    // =========================
    // ESTADO EQUIPAMENTO
    // =========================
    public function insertEstadoEquipamento($nome_estado) {
        $sql = "INSERT INTO ESTADO_EQUIPAMENTO (nome_estado)
                VALUES (:estado)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':estado' => $nome_estado]);
    }

    // =========================
    // FORNECEDOR
    // =========================
    public function insertFornecedor($nome_empresa, $nif, $tipo_fornecedor, $contacto_telefonico = null, $email = null, $morada = null, $codigo_postal = null, $localidade = null, $website = null, $pessoa_contacto = null, $telefone_pessoa_contacto = null, $observacoes = null, $ativo = 1) {
        $sql = "INSERT INTO FORNECEDOR (
                    nome_empresa, nif, tipo_fornecedor, contacto_telefonico, email, morada,
                    codigo_postal, localidade, website, pessoa_contacto, telefone_pessoa_contacto,
                    observacoes, ativo
                ) VALUES (
                    :nome, :nif, :tipo, :contacto, :email, :morada,
                    :codigo_postal, :localidade, :website, :pessoa_contacto, :telefone_pessoa_contacto,
                    :observacoes, :ativo
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nome' => $nome_empresa,
            ':nif' => $nif,
            ':tipo' => $tipo_fornecedor,
            ':contacto' => $contacto_telefonico,
            ':email' => $email,
            ':morada' => $morada,
            ':codigo_postal' => $codigo_postal,
            ':localidade' => $localidade,
            ':website' => $website,
            ':pessoa_contacto' => $pessoa_contacto,
            ':telefone_pessoa_contacto' => $telefone_pessoa_contacto,
            ':observacoes' => $observacoes,
            ':ativo' => $ativo
        ]);
    }

    public function updateFornecedor($id_fornecedor, $nome_empresa, $nif, $tipo_fornecedor, $contacto_telefonico = null, $email = null, $morada = null, $codigo_postal = null, $localidade = null, $website = null, $pessoa_contacto = null, $telefone_pessoa_contacto = null, $observacoes = null, $ativo = 1) {
        $sql = "UPDATE FORNECEDOR SET
                    nome_empresa = :nome,
                    nif = :nif,
                    tipo_fornecedor = :tipo,
                    contacto_telefonico = :contacto,
                    email = :email,
                    morada = :morada,
                    codigo_postal = :codigo_postal,
                    localidade = :localidade,
                    website = :website,
                    pessoa_contacto = :pessoa_contacto,
                    telefone_pessoa_contacto = :telefone_pessoa_contacto,
                    observacoes = :observacoes,
                    ativo = :ativo
                WHERE id_fornecedor = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id_fornecedor,
            ':nome' => $nome_empresa,
            ':nif' => $nif,
            ':tipo' => $tipo_fornecedor,
            ':contacto' => $contacto_telefonico,
            ':email' => $email,
            ':morada' => $morada,
            ':codigo_postal' => $codigo_postal,
            ':localidade' => $localidade,
            ':website' => $website,
            ':pessoa_contacto' => $pessoa_contacto,
            ':telefone_pessoa_contacto' => $telefone_pessoa_contacto,
            ':observacoes' => $observacoes,
            ':ativo' => $ativo
        ]);
    }

    // =========================
    // EQUIPAMENTO
    // =========================
    public function insertEquipamento($data) {
        $sql = "INSERT INTO EQUIPAMENTO (
                    codigo_interno, designacao, id_categoria, marca, modelo, numero_serie,
                    id_criticidade, id_estado, id_localizacao, data_aquisicao, ano_fabrico, custo_aquisicao,
                    tipo_entrada, observacoes
                ) VALUES (
                    :codigo_interno, :designacao, :categoria, :marca, :modelo, :numero_serie,
                    :criticidade, :estado, :localizacao, :data_aquisicao, :ano_fabrico, :custo_aquisicao,
                    :tipo_entrada, :observacoes
                )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codigo_interno' => $data['codigo_interno'],
            ':designacao' => $data['designacao'],
            ':categoria' => $data['categoria'],
            ':marca' => $data['marca'],
            ':modelo' => $data['modelo'],
            ':numero_serie' => $data['numero_serie'],
            ':criticidade' => $data['criticidade'],
            ':estado' => $data['estado'],
            ':localizacao' => $data['localizacao'],
            ':data_aquisicao' => $data['data_aquisicao'],
            ':ano_fabrico' => $data['ano_fabrico'],
            ':custo_aquisicao' => $data['custo_aquisicao'],
            ':tipo_entrada' => $data['tipo_entrada'],
            ':observacoes' => $data['observacoes']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function updateEquipamento($id_equipamento, $data) {
        $sql = "UPDATE EQUIPAMENTO SET
                    codigo_interno = :codigo_interno,
                    designacao = :designacao,
                    id_categoria = :categoria,
                    marca = :marca,
                    modelo = :modelo,
                    numero_serie = :numero_serie,
                    id_criticidade = :criticidade,
                    id_estado = :estado,
                    id_localizacao = :localizacao,
                    data_aquisicao = :data_aquisicao,
                    ano_fabrico = :ano_fabrico,
                    custo_aquisicao = :custo_aquisicao,
                    tipo_entrada = :tipo_entrada,
                    observacoes = :observacoes
                WHERE id_equipamento = :id_equipamento";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':codigo_interno' => $data['codigo_interno'],
            ':designacao' => $data['designacao'],
            ':categoria' => $data['categoria'],
            ':marca' => $data['marca'],
            ':modelo' => $data['modelo'],
            ':numero_serie' => $data['numero_serie'],
            ':criticidade' => $data['criticidade'],
            ':estado' => $data['estado'],
            ':localizacao' => $data['localizacao'],
            ':data_aquisicao' => $data['data_aquisicao'],
            ':ano_fabrico' => $data['ano_fabrico'],
            ':custo_aquisicao' => $data['custo_aquisicao'],
            ':tipo_entrada' => $data['tipo_entrada'],
            ':observacoes' => $data['observacoes'],
            ':id_equipamento' => $id_equipamento
        ]);
    }

    // =========================
    // COMPONENTE EQUIPAMENTO (renomeado para COMPONENTES para corresponder à tabela)
    // =========================
    public function insertComponente($id_equipamento, $codigo_interno, $designacao, $marca, $modelo, $numero_serie, $data_aquisicao, $observacoes) {
        $sql = "INSERT INTO COMPONENTE_EQUIPAMENTO (
                    id_equipamento_principal,
                    codigo_interno_componente,
                    designacao_componente,
                    marca_componente,
                    modelo_componente,
                    numero_serie_componente,
                    data_aquisicao_componente,
                    observacoes
                ) VALUES (
                    :id_equipamento,
                    :codigo_interno,
                    :designacao,
                    :marca,
                    :modelo,
                    :numero_serie,
                    :data_aquisicao,
                    :observacoes
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_equipamento' => $id_equipamento,
            ':codigo_interno' => $codigo_interno,
            ':designacao' => $designacao,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':numero_serie' => $numero_serie,
            ':data_aquisicao' => !empty($data_aquisicao) ? $data_aquisicao : null,
            ':observacoes' => $observacoes
        ]);
    }

    public function updateComponente($id_componente, $codigo_interno, $designacao, $marca, $modelo, $numero_serie, $data_aquisicao, $observacoes) {
        $sql = "UPDATE COMPONENTE_EQUIPAMENTO SET
                    codigo_interno_componente = :codigo_interno,
                    designacao_componente = :designacao,
                    marca_componente = :marca,
                    modelo_componente = :modelo,
                    numero_serie_componente = :numero_serie,
                    data_aquisicao_componente = :data_aquisicao,
                    observacoes = :observacoes
                WHERE id_componente = :id_componente";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':codigo_interno' => $codigo_interno,
            ':designacao' => $designacao,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':numero_serie' => $numero_serie,
            ':data_aquisicao' => !empty($data_aquisicao) ? $data_aquisicao : null,
            ':observacoes' => $observacoes,
            ':id_componente' => $id_componente
        ]);
    }

    public function deleteComponentesByEquipamento($id_equipamento) {
        $stmt = $this->pdo->prepare("DELETE FROM COMPONENTE_EQUIPAMENTO WHERE id_equipamento_principal = :id_equipamento");
        return $stmt->execute([':id_equipamento' => $id_equipamento]);
    }

    // =========================
    // EQUIPAMENTO_FORNECEDOR
    // =========================
    public function insertEquipamentoFornecedor($id_equipamento, $id_fornecedor, $tipo_relacao, $data_associacao, $observacoes) {
        $sql = "INSERT INTO EQUIPAMENTO_FORNECEDOR (
                    id_equipamento,
                    id_fornecedor,
                    tipo_relacao,
                    data_associacao,
                    observacoes
                ) VALUES (
                    :id_equipamento,
                    :id_fornecedor,
                    :tipo_relacao,
                    :data_associacao,
                    :observacoes
                )";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_equipamento' => $id_equipamento,
            ':id_fornecedor' => $id_fornecedor,
            ':tipo_relacao' => $tipo_relacao,
            ':data_associacao' => $data_associacao,
            ':observacoes' => $observacoes
        ]);
    }

    public function deleteEquipamentoFornecedorByEquipamento($id_equipamento) {
        $stmt = $this->pdo->prepare("DELETE FROM EQUIPAMENTO_FORNECEDOR WHERE id_equipamento = :id_equipamento");
        return $stmt->execute([':id_equipamento' => $id_equipamento]);
    }

    // =========================
    // DOCUMENTO
    // =========================
    public function insertDocumento($id_equipamento, $id_tipo, $id_fornecedor, $nome, $data_doc, $data_validade, $tipo_ficheiro, $caminho_ficheiro = null, $tamanho_ficheiro = null, $observacoes = null) {
        $sql = "INSERT INTO DOCUMENTO (
                    id_equipamento,
                    id_tipo_documento,
                    id_fornecedor,
                    nome_documento,
                    data_documento,
                    data_validade,
                    tipo_ficheiro,
                    caminho_ficheiro,
                    tamanho_ficheiro,
                    observacoes
                ) VALUES (
                    :equipamento,
                    :tipo,
                    :fornecedor,
                    :nome,
                    :data_doc,
                    :validade,
                    :ficheiro,
                    :caminho,
                    :tamanho,
                    :observacoes
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':equipamento' => $id_equipamento,
            ':tipo' => $id_tipo,
            ':fornecedor' => $id_fornecedor,
            ':nome' => $nome,
            ':data_doc' => $data_doc,
            ':validade' => $data_validade,
            ':ficheiro' => $tipo_ficheiro,
            ':caminho' => $caminho_ficheiro,
            ':tamanho' => $tamanho_ficheiro,
            ':observacoes' => $observacoes
        ]);
    }

    public function insertDocumentoManutencao($id_manutencao, $nome_documento, $tipo_documento, $caminho_arquivo, $tamanho_arquivo = null, $data_upload = null, $observacoes = null) {
        $sql = "INSERT INTO DOCUMENTO_MANUTENCAO (
                    id_manutencao,
                    nome_documento,
                    tipo_documento,
                    caminho_arquivo,
                    tamanho_arquivo,
                    data_upload,
                    observacoes
                ) VALUES (
                    :id_manutencao,
                    :nome_documento,
                    :tipo_documento,
                    :caminho_arquivo,
                    :tamanho_arquivo,
                    :data_upload,
                    :observacoes
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_manutencao' => $id_manutencao,
            ':nome_documento' => $nome_documento,
            ':tipo_documento' => $tipo_documento,
            ':caminho_arquivo' => $caminho_arquivo,
            ':tamanho_arquivo' => $tamanho_arquivo,
            ':data_upload' => $data_upload,
            ':observacoes' => $observacoes
        ]);
    }

    public function deleteDocumentosByEquipamento($id_equipamento) {
        $stmt = $this->pdo->prepare("DELETE FROM DOCUMENTO WHERE id_equipamento = :id_equipamento");
        return $stmt->execute([':id_equipamento' => $id_equipamento]);
    }

    // =========================
    // GARANTIA_CONTRATO
    // =========================
    public function insertGarantiaContrato($id_equipamento, $id_fornecedor, $inicio, $fim, $tipo, $periodicidade, $custo) {
        $sql = "INSERT INTO GARANTIA_CONTRATO (
                    id_equipamento,
                    id_fornecedor,
                    data_inicio_garantia,
                    data_fim_garantia,
                    tipo_contrato,
                    periodicidade_manutencao,
                    custo_contrato
                ) VALUES (
                    :equipamento,
                    :fornecedor,
                    :inicio,
                    :fim,
                    :tipo,
                    :periodicidade,
                    :custo
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':equipamento' => $id_equipamento,
            ':fornecedor' => $id_fornecedor,
            ':inicio' => $inicio,
            ':fim' => $fim,
            ':tipo' => $tipo,
            ':periodicidade' => $periodicidade,
            ':custo' => $custo
        ]);
    }

    // =========================
    // MANUTENÇÃO
    // =========================
    public function insertManutencao($id_equipamento, $id_fornecedor, $tipo, $data, $descricao, $custo, $proximo) {
        $sql = "INSERT INTO MANUTENCAO (
                    id_equipamento,
                    id_fornecedor,
                    tipo_manutencao,
                    data_manutencao,
                    descricao_trabalho,
                    custo_manutencao,
                    proximo_manutencao_prevista
                ) VALUES (
                    :equipamento,
                    :fornecedor,
                    :tipo,
                    :data,
                    :descricao,
                    :custo,
                    :proximo
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':equipamento' => $id_equipamento,
            ':fornecedor' => $id_fornecedor,
            ':tipo' => $tipo,
            ':data' => $data,
            ':descricao' => $descricao,
            ':custo' => $custo,
            ':proximo' => $proximo
        ]);
    }

    // =========================
    // UTILIZADOR_EQUIPAMENTO
    // =========================
    public function insertUtilizadorEquipamento($id_utilizador, $id_equipamento, $tipo_relacao, $data_atribuicao, $observacoes = null) {
        $sql = "INSERT INTO UTILIZADOR_EQUIPAMENTO (
                    id_utilizador,
                    id_equipamento,
                    tipo_relacao,
                    data_atribuicao,
                    observacoes,
                    ativo
                ) VALUES (
                    :utilizador,
                    :equipamento,
                    :tipo,
                    :data,
                    :observacoes,
                    1
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':utilizador' => $id_utilizador,
            ':equipamento' => $id_equipamento,
            ':tipo' => $tipo_relacao,
            ':data' => $data_atribuicao,
            ':observacoes' => $observacoes
        ]);
    }

    public function deleteUtilizadorEquipamento($id_utilizador_equipamento) {
        $sql = "UPDATE UTILIZADOR_EQUIPAMENTO SET ativo = 0, data_fim = CURDATE() WHERE id_utilizador_equipamento = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id_utilizador_equipamento]);
    }

    public function deleteDocumento($id_documento) {
        $stmt = $this->pdo->prepare("DELETE FROM DOCUMENTO WHERE id_documento = :id");
        return $stmt->execute([':id' => $id_documento]);
    }
}

$db_insert = new DatabaseINSERT($pdo);
?>
