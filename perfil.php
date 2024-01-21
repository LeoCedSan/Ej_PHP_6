<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION["username"];

// Obtener datos del usuario desde el archivo JSON
$usuarios = [];
if (file_exists("usuarios.json")) {
    $usuarios = json_decode(file_get_contents("usuarios.json"), true);
}

// Buscar el usuario actual en el array de usuarios
$datosUsuario = null;

foreach ($usuarios as $key => $usuario) {
    if ($usuario["username"] === $nombreUsuario) {
        $datosUsuario = &$usuarios[$key]; // Referencia al usuario actual
        break;
    }
}

// Si el usuario no se encuentra, redirigir al inicio de sesión
if ($datosUsuario === null) {
    header("Location: login.php");
    exit();
}

// Función para obtener la URL de la imagen de perfil del usuario
function obtenerURLImagenPerfil($usuario) {
    return isset($usuario["perfil_imagen"]) ? $usuario["perfil_imagen"] : "default.jpg";
}

// Procesar la actualización de la URL de la imagen de perfil si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["url_imagen_perfil"])) {
    // Validar la URL de la imagen (puedes agregar más validaciones según tus necesidades)
    $urlImagenPerfil = filter_var($_POST["url_imagen_perfil"], FILTER_VALIDATE_URL);

    if ($urlImagenPerfil !== false) {
        // Actualizar la URL de la imagen de perfil en el array del usuario
        $datosUsuario["perfil_imagen"] = $urlImagenPerfil;

        // Guardar el array actualizado en el archivo JSON
        file_put_contents("usuarios.json", json_encode($usuarios));

        // Guardar la URL en la sesión
        $_SESSION["perfil_imagen"] = $urlImagenPerfil;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Perfil</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="perfil.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="inicio.php">Inicio</a>
        <a href="cerrar_sesion.php">Cerrar Sesión</a>
        <a href="perfil.php">
         <?php echo htmlspecialchars($nombreUsuario); ?>
            <?php if (isset($datosUsuario)) : ?>
                <img src="<?php echo obtenerURLImagenPerfil($datosUsuario); ?>" alt="Imagen de Perfil" width="30">
            <?php elseif (isset($_SESSION["perfil_imagen"])) : ?>
                <img src="<?php echo $_SESSION["perfil_imagen"]; ?>" alt="Imagen de Perfil" width="30">
            <?php else : ?>
                <img src="default.jpg" alt="Icono de Perfil" width="30">
            <?php endif; ?>
          
        </a>
    </div>

    <!-- Contenido de la página -->
    <div class="container">
    <h2>Mi Perfil</h2>
    
    <p>Nombre de Usuario: <?php echo htmlspecialchars($datosUsuario["username"]); ?></p>
    <p>Correo Electrónico: <?php echo htmlspecialchars($datosUsuario["email"]); ?></p>
    
    <!-- Mostrar imagen de perfil -->
    <img src="<?php echo obtenerURLImagenPerfil($datosUsuario); ?>" alt="Imagen de Perfil" width="100">
    
    <!-- Formulario para actualizar la URL de la imagen de perfil -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="url_imagen_perfil">URL de la Imagen de Perfil:</label>
        <input type="text" name="url_imagen_perfil" value="<?php echo obtenerURLImagenPerfil($datosUsuario); ?>" required>
        <input type="submit" value="Actualizar">
    </form>
    </div>
    <!-- Mostrar preferencias -->
    <div class="container">
    <h3>Preferencias de Videojuegos:</h3>
    <?php if (isset($datosUsuario["preferencias"])) : ?>
        <ul>
            <?php foreach ($datosUsuario["preferencias"] as $preferencia) : ?>
                <li><?php echo htmlspecialchars($preferencia); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No has seleccionado preferencias de videojuegos.</p>
    <?php endif; ?>
    </div>
</body>
</html>
