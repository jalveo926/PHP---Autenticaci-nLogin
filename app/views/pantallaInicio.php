<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../index.php?error=Debes iniciar sesión");
    exit;
}

// Verifica si aún tiene 2FA pendiente - debe completar verificación
if (isset($_SESSION['2fa_pendiente']) && $_SESSION['2fa_pendiente']) {
    header("Location: ../../index.php?error=Debes completar la verificación 2FA");
    exit;
}

// Usuario autenticado correctamente, aquí va el contenido de la pantalla de inicio
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Inicio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }

        .navbar h1 {
            color: white;
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .content {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }

        .content h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .user-info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .user-info p {
            margin: 10px 0;
            color: #333;
        }

        .user-info strong {
            color: #667eea;
        }

        .logout-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
        }

        .success-badge {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Mi Aplicación</h1>
        <a href="../models/logout.php">Cerrar Sesión</a>
    </div>

    <div class="container">
        <div class="content">
            <h2>Bienvenido <span class="success-badge">✓ Verificado</span></h2>
            
            <div class="user-info">
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($_SESSION['id_usuario']); ?></p>
                <p><strong>Token:</strong> <?php echo htmlspecialchars(substr($_SESSION['token'], 0, 20) . '...'); ?></p>
            </div>

            <p style="color: #666; margin-bottom: 20px; text-align: center;">
                Has completado exitosamente la autenticación en dos factores y accedido a tu cuenta.
            </p>

            <form method="GET" action="../models/logout.php">
                <button type="submit" class="logout-btn">Cerrar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
include("../componentes/footer.php");
?>
