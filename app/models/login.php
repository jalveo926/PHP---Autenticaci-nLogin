<?php
session_start();
require_once '../config/database.php';

$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($usuario) || empty($password)) {
    header("Location: ../../index.php?error=Usuario y contraseña requeridos");
    exit;
}

// Buscar usuario en la base de datos
$sql = "
SELECT *
FROM usuarios
WHERE usuario = :usuario
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':usuario' => $usuario
]);

$usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    $usuarioEncontrado &&
    password_verify($password, $usuarioEncontrado['password'])
) {
    $token = bin2hex(random_bytes(32));

    $_SESSION['id_usuario'] = $usuarioEncontrado['id'];
    $_SESSION['usuario'] = $usuarioEncontrado['usuario'];
    $_SESSION['email'] = $usuarioEncontrado['email'];
    $_SESSION['token'] = $token;

    header("Location: ../views/pantallaInicio.php");
    exit;
}

header("Location: ../../index.php?error=Usuario o contraseña incorrectos");
exit;
?>