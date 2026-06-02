<?php
//Verifica si el usuario ya ha iniciado sesión
if (isset($_SESSION['id_usuario'])) {
    header("Location: app/views/pantallaInicio.php");
    exit;
}

include ("../componentes/footer.php");
?>
