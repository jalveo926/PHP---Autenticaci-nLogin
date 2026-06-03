<?php
session_start();
require_once '../config/databaseConfig.php';
require_once '../models/Database.php';
require_once '../models/Autenticador2FA.php';

// Verificar que el usuario ha pasado por login
if (empty($_SESSION['2fa_pendiente']) || empty($_SESSION['id_usuario'])) {
    header("Location: ../../index.php?error=Acceso denegado");
    exit;
}

$db = new Database($pdo);
$idUsuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['usuario'];
$email = $_SESSION['email'];
$esPrimer2FA = $_SESSION['primer_2fa'] ?? false;

// Obtener datos del usuario para obtener el secret
$usuarioData = $db->obtener('usuarios', [':id' => $idUsuario]);

if (!$usuarioData) {
    header("Location: ../../index.php?error=Usuario no encontrado");
    exit;
}

$secret = $esPrimer2FA ? ($_SESSION['secret_2fa_temporal'] ?? null) : $usuarioData['secret_2fa'];
$autenticador = new Autenticador2FA($pdo, $idUsuario);

// Procesar verificación del código
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';

    if (empty($codigo)) {
        $error = "Por favor ingresa el código";
    } elseif ($autenticador->verificarCodigo($codigo, $secret)) {
        // Código válido
        unset($_SESSION['2fa_pendiente']);
        unset($_SESSION['secret_2fa_temporal']);
        unset($_SESSION['primer_2fa']);

        header("Location: pantallaInicio.php");
        exit;
    } else {
        $error = "Código 2FA inválido";
    }
}

$urlQR = $esPrimer2FA ? $autenticador->obtenerUrlQR($secret, $email) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .qr-section {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }

        .qr-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .qr-code {
            margin-bottom: 15px;
        }

        .qr-code img {
            max-width: 200px;
            height: auto;
        }

        .qr-instructions {
            color: #666;
            font-size: 13px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .manual-entry {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #856404;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            letter-spacing: 2px;
            text-align: center;
            font-weight: bold;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .error {
            color: #d32f2f;
            background: #ffebee;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #d32f2f;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #1976d2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verificación 2FA</h1>
        <p class="subtitle">Autenticación en dos factores</p>

        <?php if (!empty($error)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($esPrimer2FA && $urlQR): ?>
            <div class="qr-section">
                <h3>Escanea este código QR</h3>
                <div class="qr-code">
                    <img src="<?php echo htmlspecialchars($urlQR); ?>" alt="Código QR">
                </div>
                <div class="qr-instructions">
                    <strong>Instrucciones:</strong><br>
                    1. Descarga una aplicación autenticadora como Google Authenticator, Microsoft Authenticator o Authy<br>
                    2. Abre la aplicación y selecciona "Agregar cuenta"<br>
                    3. Escanea este código QR<br>
                    4. Ingresa el código de 6 dígitos generado
                </div>
            </div>

            <div class="manual-entry">
                <strong>¿No puedes escanear?</strong><br>
                Ingresa manualmente este código en tu aplicación autenticadora:<br>
                <code style="display: block; margin-top: 10px; font-weight: bold; font-size: 14px;">
                    <?php echo htmlspecialchars($secret); ?>
                </code>
            </div>
        <?php else: ?>
            <div class="info-box">
                Ingresa el código de 6 dígitos de tu aplicación autenticadora
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="codigo">Código de Verificación</label>
                <input 
                    type="text" 
                    id="codigo" 
                    name="codigo" 
                    placeholder="000000" 
                    maxlength="6" 
                    pattern="[0-9]{6}" 
                    required
                    inputmode="numeric"
                >
            </div>

            <button type="submit">Verificar Código</button>
        </form>

        <div class="back-link">
            <a href="../../index.php">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
