<?php
session_start();
require_once '../config/databaseConfig.php';
require_once 'Database.php';
require_once '../utils/PasswordHasher.php';
require_once '../utils/Sanitizer.php';

$db = new Database($pdo);
$passwordHasher = new PasswordHasher();

// Obtener datos del formulario
$nombre = Sanitizer::obtenerPost('nombre', 'texto');
$usuario = Sanitizer::obtenerPost('usuario', 'texto');
$email = Sanitizer::obtenerPost('email', 'email');
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// Validar nombre
$validarNombre = Sanitizer::validarNombre($nombre);
if (!$validarNombre['valido']) {
    header("Location: ../views/inicioSesion/register.php?error=" . urlencode(implode(', ', $validarNombre['errores'])));
    exit;
}

// Validar usuario
$validarUsuario = Sanitizer::validarUsuario($usuario);
if (!$validarUsuario['valido']) {
    header("Location: ../views/inicioSesion/register.php?error=" . urlencode(implode(', ', $validarUsuario['errores'])));
    exit;
}

// Validar email
if (!Sanitizer::validarEmail($email)) {
    header("Location: ../views/inicioSesion/register.php?error=El email no es válido");
    exit;
}

// Validar password
$validarPassword = Sanitizer::validarPassword($password, 4);
if (!$validarPassword['valido']) {
    header("Location: ../views/inicioSesion/register.php?error=" . urlencode(implode(', ', $validarPassword['errores'])));
    exit;
}

// Validar que las contraseñas coincidan
if (!Sanitizer::validarPasswordCoinciden($password, $password_confirm)) {
    header("Location: ../views/inicioSesion/register.php?error=Las contraseñas no coinciden");
    exit;
}

// Verificar usuario o email existente
if ($db->existe('usuarios', [':usuario' => $usuario, ':email' => $email])) {
    header("Location: ../views/inicioSesion/register.php?error=El usuario o email ya existe");
    exit;
}

// Generar hash de la contraseña
try {
    $passwordHash = $passwordHasher->hash($password);
} catch (Exception $e) {
    header("Location: ../views/inicioSesion/register.php?error=Error al procesar la contraseña");
    exit;
}

// Insertar usuario
$datosUsuario = [
    ':nombre' => $nombre,
    ':email' => $email,
    ':usuario' => $usuario,
    ':password' => $passwordHash
];

$idUsuario = $db->insertar('usuarios', $datosUsuario);

// Iniciar sesión automáticamente
$_SESSION['id_usuario'] = $idUsuario;
$_SESSION['usuario'] = $usuario;
$_SESSION['email'] = $email;
$_SESSION['token'] = bin2hex(random_bytes(32));

header("Location: ../views/pantallaInicio.php");
exit;
?>