<?php
// logout seguro: limpiar y destruir la sesión, borrar cookie y redirigir
session_start();

// Evitar cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Limpiar todas las variables de sesión
$_SESSION = array();

// Borrar cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: ../public/index.php');
exit;
?>

