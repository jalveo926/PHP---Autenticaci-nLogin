<?php
session_start();

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {
    die("Token CSRF inválido");
}

require_once '../config/databaseConfig.php';
require_once 'Database.php';
require_once 'Autenticador2FA.php';
require_once '../utils/PasswordHasher.php';
require_once '../utils/Sanitizer.php';

$usuario = Sanitizer::obtenerPost('usuario', 'texto');
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validar campos requeridos
$validarUsuario = Sanitizer::validarRequerido($usuario, 'Usuario');
$validarPassword = Sanitizer::validarRequerido($password, 'Contraseña');

if (!$validarUsuario['valido'] || !$validarPassword['valido']) {
    header("Location: ../../index.php?error=Usuario y contraseña requeridos");
    exit;
}

$db = new Database($pdo);
$passwordHasher = new PasswordHasher();

// Buscar usuario en la base de datos
$usuarioEncontrado = $db->obtener('usuarios', [':usuario' => $usuario]);

// Verificar usuario y contraseña
if (
    $usuarioEncontrado &&
    $passwordHasher->verify($password, $usuarioEncontrado['password'])
) {
    $idUsuario = $usuarioEncontrado['id'];
    $token = bin2hex(random_bytes(32));

    // Iniciar sesión temporal
    $_SESSION['id_usuario'] = $idUsuario;
    $_SESSION['usuario'] = $usuarioEncontrado['usuario'];
    $_SESSION['email'] = $usuarioEncontrado['email'];
    $_SESSION['token'] = $token;
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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