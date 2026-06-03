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
                <input type="password" name="password" id="password" minlength="8" required>
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña:</label>
                <input type="password" name="password_confirm" id="password_confirm" minlength="8" required>
            </div>


            <button type="submit">Registrarse</button>
        </form>

        <p class="redirect">¿Ya tienes cuenta? <a href="../../../index.php">Inicia sesión aquí</a></p>
    </div>
    <script>
        (function(){
            const form = document.querySelector('form');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');
            const errorDiv = document.getElementById('pw-error');

            form.addEventListener('submit', function(e){
                errorDiv.textContent = '';

                if (!password || !passwordConfirm) return;

                if (password.value.length < 8) {
                    errorDiv.textContent = 'La contraseña debe tener al menos 8 caracteres.';
                    e.preventDefault();
                    return;
                }

                if (password.value !== passwordConfirm.value) {
                    errorDiv.textContent = 'Las contraseñas no coinciden.';
                    e.preventDefault();
                    return;
                }
            });
        })();
    </script>
</body>
</html>
