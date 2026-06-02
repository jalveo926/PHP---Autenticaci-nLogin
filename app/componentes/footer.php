<?php
// footer.php - Muestra el footer con opciones de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir el manejador de logout
include("../models/logout.php");
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
