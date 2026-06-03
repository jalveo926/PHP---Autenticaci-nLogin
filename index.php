<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verifica si el usuario ya ha iniciado sesión
if (isset($_SESSION['id_usuario'])) {
    // Si 2FA está pendiente, no permitir acceso a pantallaInicio
    if (isset($_SESSION['2fa_pendiente']) && $_SESSION['2fa_pendiente']) {
        // Permitir que vea la página de login para redirigirse a 2FA
        // pero si trata de acceder directamente a pantallaInicio será bloqueado
    } else {
        // Usuario completó 2FA, redirigir a pantalla de inicio
        header("Location: app/views/pantallaInicio.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Iniciar Sesión</h2>

<form action="app/models/login.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <label>Usuario:</label>
    <input type="text" name="usuario" required>

    <br><br>

    <label>Contraseña:</label>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit">Ingresar</button>
</form>

<p>¿No tienes cuenta? <a href="app/views/inicioSesion/register.php">Regístrate aquí</a></p>

<?php
if (isset($_GET['error'])) {
    echo "<p style='color:red;'>" . htmlspecialchars($_GET['error']) . "</p>";
}
?>
</html>