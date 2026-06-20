<?php

// Inicia a sessão caso ainda não esteja ativa.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Tempo máximo de inatividade antes de expirar a sessão (em segundos).
define('SESSION_TIMEOUT_SECONDS', 1800); // 30 minutos

/**
 * Retorna true se o utilizador estiver autenticado.
 */
function isUserAuthenticated() {
    return !empty($_SESSION['logged_in'])
        && $_SESSION['logged_in'] === true
        && !empty($_SESSION['user_id'])
        && !empty($_SESSION['last_activity']);
}

/**
 * Retorna true se a sessão expirou por inatividade.
 */
function isSessionExpired() {
    if (empty($_SESSION['last_activity'])) {
        return true;
    }
    return (time() - intval($_SESSION['last_activity'])) > SESSION_TIMEOUT_SECONDS;
}

/**
 * Atualiza a última atividade da sessão.
 */
function refreshSessionActivity() {
    $_SESSION['last_activity'] = time();
}

/**
 * Retorna true se o utilizador autenticado também for administrador.
 */
function isUserAdmin() {
    return isUserAuthenticated()
        && !empty($_SESSION['is_admin'])
        && $_SESSION['is_admin'] === true;
}

/**
 * Redireciona para a página de dashboard se o utilizador não for administrador.
 */
function requireAdmin($redirectUrl = '/SGIH/hospital_inventory_php/private/dashboard/dashboard_admin.php') {
    requireAuthentication($redirectUrl);
    if (!isUserAdmin()) {
        header('Location: ' . $redirectUrl);
        exit();
    }
}

/**
 * Finaliza a sessão do utilizador.
 */
function logoutUser() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Redireciona para a página de login se o utilizador não estiver autenticado.
 */
function requireAuthentication($redirectUrl = '/SGIH/hospital_inventory_php/public/logout.php') {
    if (!isUserAuthenticated() || isSessionExpired()) {
        logoutUser();
        header('Location: ' . $redirectUrl);
        exit();
    }
    refreshSessionActivity();
}

/**
 * Obtém o ID do utilizador autenticado.
 */
function getAuthenticatedUserId() {
    return isUserAuthenticated() ? intval($_SESSION['user_id']) : null;
}

/**
 * Obtém o nome do utilizador autenticado.
 */
function getAuthenticatedUserName() {
    return isUserAuthenticated() ? ($_SESSION['username'] ?? null) : null;
}

/**
 * Obtém o email do utilizador autenticado.
 */
function getAuthenticatedUserEmail() {
    return isUserAuthenticated() ? ($_SESSION['email'] ?? null) : null;
}

