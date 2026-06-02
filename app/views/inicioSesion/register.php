<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
</head>
<body>

<h2>Crear Nueva Cuenta</h2>

<form action="../../models/registroUsuario.php" method="POST">
    <label>Nombre:</label>
    <input type="text" name="nombre" required>
    <br><br>
    <label>Usuario:</label>
    <input type="text" name="usuario" required>
    <br><br>

    <label>Email:</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Contraseña:</label>
    <input type="password" name="password" required>
    <br><br>

    <label>Confirmar Contraseña:</label>
    <input type="password" name="password_confirm" required>
    <br><br>

    <button type="submit">Registrarse</button>
</form>

<p>¿Ya tienes cuenta? <a href="../../index.php">Inicia sesión aquí</a></p>

<?php
if (isset($_GET['error'])) {
    echo "<p style='color:red;'>" . htmlspecialchars($_GET['error']) . "</p>";
}
?>

</body>
</html>
