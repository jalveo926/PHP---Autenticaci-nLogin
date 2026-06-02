<?php
// footer.php - muestra un footer con cierre de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// manejador simple de logout: al recibir POST 'logout' destruye la sesión y redirige a login.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // limpiar todas las variables de sesión
    $_SESSION = array();
    // destruir cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    
    //Esto se encarga de redirigir al usuario a la página de inicio de sesión después de cerrar la sesión
    header('Location: ../../index.php');
    exit;
}
?>

<footer style="position:fixed;bottom:0;left:0;right:0;background:#f8f9fa;border-top:1px solid #e7e7e7;padding:10px;text-align:center;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:1000px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:10px;">
        <div style="font-size:14px;color:#555;">&copy; <?php echo date('Y'); ?> Mi Aplicación</div>
        <div>
            <?php if (isset($_SESSION['usuario'])): ?>
                <form method="post" style="display:inline;margin:0;">
                    <button type="submit" name="logout" style="background:#dc3545;color:#fff;border:0;padding:8px 12px;border-radius:4px;cursor:pointer;">Cerrar sesión</button>
                </form>
            <?php else: ?>
                <a href="../../index.php" style="color:#007bff;text-decoration:none;font-size:14px;">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</footer>
