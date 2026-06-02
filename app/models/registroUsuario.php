<?php
session_start();

// Obtener datos del formulario
$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// Validaciones básicas
if (empty($usuario) || empty($email) || empty($password) || empty($password_confirm)) {
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

// Archivo de almacenamiento de usuarios
$archivoUsuarios = __DIR__ . '/usuarios.json';

// Leer usuarios existentes
$usuarios = [];
if (file_exists($archivoUsuarios)) {
    $contenido = file_get_contents($archivoUsuarios);
    $usuarios = json_decode($contenido, true) ?? [];
}

// Verificar si el usuario ya existe
foreach ($usuarios as $user) {
    if ($user['usuario'] === $usuario) {
        header("Location: ../views/inicioSesion/register.php?error=El usuario ya existe");
        exit;
    }
    if ($user['email'] === $email) {
        header("Location: ../views/inicioSesion/register.php?error=El email ya está registrado");
        exit;
    }
}

// Crear nuevo usuario
$nuevoUsuario = [
    'id' => count($usuarios) + 1,
    'usuario' => $usuario,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'fecha_registro' => date('Y-m-d H:i:s')
];

// Agregar al array
$usuarios[] = $nuevoUsuario;

// Guardar en archivo JSON
if (file_put_contents($archivoUsuarios, json_encode($usuarios, JSON_PRETTY_PRINT))) {
    // Iniciar sesión automáticamente
    $_SESSION['id_usuario'] = $nuevoUsuario['id'];
    $_SESSION['usuario'] = $nuevoUsuario['usuario'];
    $_SESSION['email'] = $nuevoUsuario['email'];
    $_SESSION['token'] = bin2hex(random_bytes(32));
    
    // Redirigir a la pantalla de inicio
    header("Location: ../views/pantallaInicio.php");
    exit;
} else {
    header("Location: ../views/inicioSesion/register.php?error=Error al registrar el usuario");
    exit;
}
?>
