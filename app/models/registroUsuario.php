<?php
session_start();

require_once '../config/databaseConfig.php';
require_once 'Database.php';
require_once '../utils/PasswordHasher.php';
require_once '../utils/Sanitizer.php';
require_once '../clases/RegistroUsuario.php';

$db = new Database($pdo);
$passwordHasher = new PasswordHasher();

$registro = new RegistroUsuario(
    $db,
    $passwordHasher
);

// Obtener datos
$nombre = Sanitizer::obtenerPost('nombre', 'texto');
$usuario = Sanitizer::obtenerPost('usuario', 'texto');
$email = Sanitizer::obtenerPost('email', 'email');

$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Validar nombre
$validarNombre = $registro->validarNombre($nombre);

if (!$validarNombre['valido']) {
    header(
        "Location: ../views/inicioSesion/register.php?error=" .
        urlencode(implode(', ', $validarNombre['errores']))
    );
    exit;
}

// Validar usuario
$validarUsuario = $registro->validarUsuario($usuario);

if (!$validarUsuario['valido']) {
    header(
        "Location: ../views/inicioSesion/register.php?error=" .
        urlencode(implode(', ', $validarUsuario['errores']))
    );
    exit;
}

// Validar email
if (!$registro->validarEmail($email)) {
    header(
        "Location: ../views/inicioSesion/register.php?error=El email no es válido"
    );
    exit;
}

// Validar contraseña
$validarPassword = $registro->validarPassword($password);

if (!$validarPassword['valido']) {
    header(
        "Location: ../views/inicioSesion/register.php?error=" .
        urlencode(implode(', ', $validarPassword['errores']))
    );
    exit;
}

// Verificar coincidencia
if (
    !$registro->validarCoincidenciaPassword(
        $password,
        $password_confirm
    )
) {
    header(
        "Location: ../views/inicioSesion/register.php?error=Las contraseñas no coinciden"
    );
    exit;
}

// Verificar duplicados
if ($registro->usuarioExiste($usuario, $email)) {
    header(
        "Location: ../views/inicioSesion/register.php?error=El usuario o email ya existe"
    );
    exit;
}

// Generar hash
try {
    $passwordHash = $registro->generarHash($password);
} catch (Exception $e) {
    header(
        "Location: ../views/inicioSesion/register.php?error=Error al procesar la contraseña"
    );
    exit;
}

// Registrar usuario
$idUsuario = $registro->registrar(
    $nombre,
    $email,
    $usuario,
    $passwordHash
);

// Crear sesión
$_SESSION['id_usuario'] = $idUsuario;
$_SESSION['usuario'] = $usuario;
$_SESSION['email'] = $email;
$_SESSION['token'] = bin2hex(random_bytes(32));

// Limpiar posibles estados 2FA pendientes anteriores
unset(
    $_SESSION['2fa_pendiente'],
    $_SESSION['secret_2fa_temporal'],
    $_SESSION['primer_2fa']
);

header("Location: ../views/pantallaInicio.php");
exit;