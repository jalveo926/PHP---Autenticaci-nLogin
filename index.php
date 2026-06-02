<?php
session_start();

//Verifica si el usuario ya ha iniciado sesión
if (isset($_SESSION['id_usuario'])) {
    header("Location: app/views/pantallaInicio.php");
    exit;
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