<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

$acao = $_POST['acao'] ?? '';
$id_equipamento = intval($_POST['id_equipamento'] ?? 0);

if ($id_equipamento <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID equipamento obrigatório']);
    exit;
}

try {
    $dbManager = new DatabaseManager($pdo);
    $db_insert = new DatabaseINSERT($pdo);

    switch ($acao) {
        case 'assign':
            $id_utilizador = intval($_POST['id_utilizador'] ?? 0);
            $tipo_relacao = $_POST['tipo_relacao'] ?? 'Operador';
            
            if ($id_utilizador <= 0) {
                throw new Exception('ID utilizador obrigatório');
            }

            // Verificar se já existe relação ativa
            $stmt = $pdo->prepare('SELECT id_utilizador_equipamento FROM UTILIZADOR_EQUIPAMENTO WHERE id_utilizador = :u AND id_equipamento = :e AND ativo = 1');
            $stmt->execute([':u' => $id_utilizador, ':e' => $id_equipamento]);
            if ($stmt->fetch()) {
                throw new Exception('Utilizador já atribuído a este equipamento');
            }

            $db_insert->insertUtilizadorEquipamento($id_utilizador, $id_equipamento, $tipo_relacao, date('Y-m-d'));
            echo json_encode(['sucesso' => true, 'mensagem' => 'Utilizador atribuído com sucesso']);
            break;

        case 'unassign':
            $id_utilizador_equipamento = intval($_POST['id_utilizador_equipamento'] ?? 0);
            if ($id_utilizador_equipamento <= 0) {
                throw new Exception('ID atribuição obrigatório');
            }

            $db_insert->deleteUtilizadorEquipamento($id_utilizador_equipamento);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Utilizador removido com sucesso']);
            break;

        case 'get_list':
            $utilizadores = $dbManager->getUtilizadoresPorEquipamento($id_equipamento);
            echo json_encode(['sucesso' => true, 'utilizadores' => $utilizadores]);
            break;

        default:
            throw new Exception('Ação inválida');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['erro' => $e->getMessage()]);
}
exit;
?>
