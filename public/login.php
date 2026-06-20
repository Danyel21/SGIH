<?php
require_once(__DIR__ . '/../private/includes/db_connect.php');

session_start();

// Redireciona se o utilizador já estiver logado
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("Location: /SGIH/hospital_inventory_php/private/dashboard/dashboard_admin.php"); // Ajuste para a sua página de dashboard
    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        $error_message = "Por favor, preencha todos os campos.";
    } else {
        // Prepara a query para buscar o utilizador por email
        $stmt = $pdo->prepare("SELECT id_utilizador, nome, email, password_hash, funcao FROM UTILIZADOR WHERE email = :email AND ativo = 1");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Tenta verificar a password como hash (para passwords já migradas ou novas)
            if (password_verify($password, $user['password_hash'])) {
                // Autenticação bem-sucedida com hash
            } else {
                // Se a verificação de hash falhar, verifica se a password é em texto limpo (para migração)
                // ATENÇÃO: Esta é uma verificação TEMPORÁRIA para migração.
                // Deve ser removida após todos os utilizadores terem as suas passwords migradas.
                if ($password === $user['password_hash']) {
                    // Autenticação bem-sucedida com password em texto limpo
                    // HASH a password e atualiza na base de dados
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $pdo->prepare("UPDATE UTILIZADOR SET password_hash = ? WHERE id_utilizador = ?");
                    $updateStmt->execute([$newHash, $user['id_utilizador']]);

                    // Log de migração (opcional, para depuração)
                    error_log("Password do utilizador " . $user['email'] . " migrada para hash.");
                } else {
                    // Password incorreta (nem hash, nem texto limpo)
                    $error_message = "Email ou palavra-passe inválidos.";
                }
            }

            // Se a autenticação foi bem-sucedida (seja por hash ou por migração)
            if (!empty($user) && empty($error_message)) {
                $_SESSION["user_id"] = $user['id_utilizador'];
                $_SESSION["username"] = $user['nome'];
                $_SESSION["email"] = $user['email'];
                $_SESSION["funcao"] = $user['funcao'];
                $_SESSION["is_admin"] = stripos(trim($user['funcao']), 'admin') !== false;
                $_SESSION["logged_in"] = true;
                $_SESSION["last_activity"] = time();

                // set ativo = 1 for the user in the database
                $updateActiveStmt = $pdo->prepare("UPDATE UTILIZADOR SET ativo = 1 WHERE id_utilizador = ?");
                $updateActiveStmt->execute([$user['id_utilizador']]);

                header("Location: /SGIH/hospital_inventory_php/private/dashboard/dashboard_admin.php");
                exit();
            }
        } else {
            // Utilizador não encontrado ou inativo
            $error_message = "Email ou palavra-passe inválidos.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventário Hospitalar</title>
    <!-- Bootstrap 5 CSS -->
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="assets/fontawesome/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

    <div class="card login-card">
        
        <div class="card-header">
            <i class="fas fa-hospital-user"></i>
            <h4 class="hospital-logo mb-0">SGI HOSPITALAR</h4>
            <p class="small mb-0">Sistema de Gestão do Inventário</p>
        </div>
        <div class="card-body p-4">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Seu email" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Palavra-passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Sua senha" required>
                    </div>
                </div>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= ($error_message) ?>
                    </div>
                <?php endif; ?>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Entrar no Sistema</button>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none small">Esqueceu a palavra-passe?</a>
                </div>

            </form>
<!-- Separado do formulário -->
<div class="text-center mt-4">
    <a href="index.html" class="back-link text-decoration-none">
        <i class="fas fa-arrow-left"></i>
        Voltar à Página Principal
    </a>
</div>
        </div>
        <div class="card-footer text-center py-3 bg-white border-0">
            <p class="text-muted small mb-0">&copy; 2026 Gestão de Equipamentos Médicos</p>
        </div>
    </div>


   
    <!-- Bootstrap 5 JS Bundle -->
    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
