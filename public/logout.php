<?php
require_once(__DIR__ . '/../private/includes/db_connect.php');

session_start();

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se for desejado destruir completamente a sessão, também se deve eliminar o cookie de sessão.
// Nota: Isto destruirá a sessão, e não apenas os dados da sessão!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), 
        '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Remove todas as variáveis da sessão
// Isto limpa os dados armazenados, como $_SESSION['utilizador'], etc.
session_unset();

// Finalmente, destrói a sessão.
session_destroy();

// set ativo = 0 for the user in the database
if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];
    $updateActiveStmt = $pdo->prepare("UPDATE UTILIZADOR SET ativo = 0 WHERE id_utilizador = ?");
    $updateActiveStmt->execute([$userId]);
}

// Redireciona para a página de login
header("Location: login.php");
exit();
?>
