<?php
session_start(); // Inicia a sessão

// Destroi todas as variáveis de sessão
$_SESSION = array();

// Se quiser destruir completamente a sessão, também remova o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão
session_destroy();

// Redireciona para a página de login
header("Location: /system/Secretsusers/LoginSecrets.php");
exit;
