<?php
session_start();
use app\views\pantallaInicio;


$usuario = $_POST['usuario'];
$password = $_POST['password'];

// Simulación de usuario almacenado
$usuarioBD = "admin";
$passwordBD = "1234";

if ($usuario === $usuarioBD && $password === $passwordBD) {

    // Token aleatorio
    $token = bin2hex(random_bytes(32));

    $_SESSION['id_usuario'] = 1;
    $_SESSION['usuario'] = $usuario;
    $_SESSION['token'] = $token;

    // Redirige a donde debe ir el usuario si es válido
    header("Location: ../views/pantallaInicio.php");
    exit;
}

echo "Usuario o contraseña incorrectos";