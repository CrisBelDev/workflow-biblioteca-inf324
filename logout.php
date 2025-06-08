<?php
session_start(); // Inicia la sesión

// Elimina todas las variables de sesión
$_SESSION = array();

// Si se desea destruir completamente la sesión, se debe borrar también la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio o login
header("Location: login.php");
exit;
?>