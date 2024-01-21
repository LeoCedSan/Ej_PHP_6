<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Inicio</title>
</head>
<body>
<h2>Bienvenido</h2>
<p>Hola, <?php echo htmlspecialchars($nombreUsuario); ?>.</p>
<p><a href="cerrar_sesion.php">Cerrar Sesión</a></p>
</body>
</html>
