<?php
// logout.php - Maneja la lógica de cerrar sesión

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si es una solicitud POST con logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    
    // Destruir sesión
    session_destroy();
    
    // Redirigir a la página de login
    header('Location: ../../index.php');
    exit;
}
?>
