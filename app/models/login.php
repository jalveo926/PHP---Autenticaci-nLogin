<?php
session_start();

$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($usuario) || empty($password)) {
    header("Location: ../../index.php?error=Usuario y contraseña requeridos");
    exit;
}

// Archivo de almacenamiento de usuarios
$archivoUsuarios = __DIR__ . '/usuarios.json';

// Leer usuarios del archivo
$usuarios = [];
if (file_exists($archivoUsuarios)) {
    $contenido = file_get_contents($archivoUsuarios);
    $usuarios = json_decode($contenido, true) ?? [];
}

// Buscar el usuario
$usuarioEncontrado = null;
foreach ($usuarios as $user) {
    if ($user['usuario'] === $usuario) {
        $usuarioEncontrado = $user;
        break;
    }
}

if ($usuarioEncontrado && password_verify($password, $usuarioEncontrado['password'])) {
    // Token aleatorio
    $token = bin2hex(random_bytes(32));

    $_SESSION['id_usuario'] = $usuarioEncontrado['id'];
    $_SESSION['usuario'] = $usuarioEncontrado['usuario'];
    $_SESSION['email'] = $usuarioEncontrado['email'];
    $_SESSION['token'] = $token;

    // Redirige a la pantalla de inicio
    header("Location: ../views/pantallaInicio.php");
    exit;
}

header("Location: ../../index.php?error=Usuario o contraseña incorrectos");
exit;
?>