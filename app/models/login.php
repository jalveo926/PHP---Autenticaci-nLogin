<?php
session_start();
require_once '../config/databaseConfig.php';
require_once 'Database.php';
require_once 'Autenticador2FA.php';

$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($usuario) || empty($password)) {
    header("Location: ../../index.php?error=Usuario y contraseña requeridos");
    exit;
}

$db = new Database($pdo);

// Buscar usuario en la base de datos
$usuarioEncontrado = $db->obtener('usuarios', [':usuario' => $usuario]);

if (
    $usuarioEncontrado &&
    password_verify($password, $usuarioEncontrado['password'])
) {
    $idUsuario = $usuarioEncontrado['id'];
    $token = bin2hex(random_bytes(32));

    // Iniciar sesión temporal
    $_SESSION['id_usuario'] = $idUsuario;
    $_SESSION['usuario'] = $usuarioEncontrado['usuario'];
    $_SESSION['email'] = $usuarioEncontrado['email'];
    $_SESSION['token'] = $token;
    $_SESSION['2fa_pendiente'] = true;

    // Inicializar autenticador 2FA
    $autenticador = new Autenticador2FA($pdo, $idUsuario);

    // Si no tiene secret 2FA, generar y guardar uno
    if (empty($usuarioEncontrado['secret_2fa'])) {
        $secret = $autenticador->generarSecret();
        $autenticador->guardarSecret($secret);
        
        // Guardar en sesión para usar en la página de verificación
        $_SESSION['secret_2fa_temporal'] = $secret;
        $_SESSION['primer_2fa'] = true; // Indicar que es la primera vez
    } else {
        // Ya tiene secret, simplemente indicar que es verificación
        $_SESSION['primer_2fa'] = false;
    }

    // Redirigir a página de verificación 2FA
    header("Location: ../views/verificacion2FA.php");
    exit;
}

header("Location: ../../index.php?error=Usuario o contraseña incorrectos");
exit;
?>