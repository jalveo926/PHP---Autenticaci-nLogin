<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../../styles/register.css">
</head>
<body>
    <div class="container">
        <h2>Crear Nueva Cuenta</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="../../models/registroUsuario.php" method="POST">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="form-group">
                <label>Usuario:</label>
                <input type="text" name="usuario" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña:</label>
                <input type="password" name="password_confirm" required>
            </div>

            <button type="submit">Registrarse</button>
        </form>

        <p class="redirect">¿Ya tienes cuenta? <a href="../../../index.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
