<?php

require_once '../../includes/db_connect.php';
require_once '../../includes/auth.php';
require_once '../../includes/db_data.php';
require_once '../../includes/insert_db.php';

if (!isset($_GET['id_equipamento'])) {
    header("Location: equipamentos_lista.php?error=ID não fornecido");
    exit;
}

$id_equipamento = intval($_GET['id_equipamento']);

if ($id_equipamento <= 0) {
    header("Location: equipamentos_lista.php?error=ID inválido");
    exit;
}

// Verificar se equipamento existe
$stmt = $pdo->prepare("SELECT id_equipamento, designacao FROM EQUIPAMENTO WHERE id_equipamento = :id");
$stmt->execute([':id' => $id_equipamento]);
$equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipamento) {
    header("Location: equipamentos_lista.php?error=Equipamento não encontrado");
    exit;
}

try {
    // Iniciar transação
    $pdo->beginTransaction();

    // Deletar componentes associados
    $db_insert->deleteComponentesByEquipamento($id_equipamento);

    // Deletar fornecedores associados
    $db_insert->deleteEquipamentoFornecedorByEquipamento($id_equipamento);

    // Deletar documentos associados
    $stmt = $pdo->prepare("DELETE FROM DOCUMENTO WHERE id_equipamento = :id");
    $stmt->execute([':id' => $id_equipamento]);

    // Deletar garantias associadas
    $stmt = $pdo->prepare("DELETE FROM GARANTIA_CONTRATO WHERE id_equipamento = :id");
    $stmt->execute([':id' => $id_equipamento]);

    // Deletar manutenções associadas
    $stmt = $pdo->prepare("DELETE FROM MANUTENCAO WHERE id_equipamento = :id");
    $stmt->execute([':id' => $id_equipamento]);

    // Deletar o equipamento
    $stmt = $pdo->prepare("DELETE FROM EQUIPAMENTO WHERE id_equipamento = :id");
    $stmt->execute([':id' => $id_equipamento]);

    // Confirmar transação
    $pdo->commit();

    header("Location: equipamentos_lista.php?message=" . urlencode("Equipamento '{$equipamento['designacao']}' eliminado com sucesso!"));

} catch (Exception $e) {
    // Reverter transação em caso de erro
    $pdo->rollBack();
    header("Location: equipamentos_lista.php?error=" . urlencode("Erro ao eliminar: " . $e->getMessage()));
}

exit;
?>
