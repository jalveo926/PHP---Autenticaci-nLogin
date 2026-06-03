<?php
session_start();
require_once '../config/databaseConfig.php';
require_once 'Database.php';

$db = new Database($pdo);

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// Validaciones básicas
if (
    empty($nombre) ||
    empty($usuario) ||
    empty($email) ||
    empty($password) ||
    empty($password_confirm)
) {
    header("Location: ../views/inicioSesion/register.php?error=Todos los campos son requeridos");
    exit;
}

if ($password !== $password_confirm) {
    header("Location: ../views/inicioSesion/register.php?error=Las contraseñas no coinciden");
    exit;
}

if (strlen($password) < 4) {
    header("Location: ../views/inicioSesion/register.php?error=La contraseña debe tener al menos 4 caracteres");
    exit;
}

// Verificar usuario o email existente
if ($db->existe('usuarios', [':usuario' => $usuario, ':email' => $email])) {
    header("Location: ../views/inicioSesion/register.php?error=El usuario o email ya existe");
    exit;
}

// Insertar usuario
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

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