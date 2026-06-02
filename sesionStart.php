<?PHP
session_start();
session_destroy();
// Redirige al inicio de sesión después de cerrar la sesión
header("Location: ../index.php");
exit;
?>