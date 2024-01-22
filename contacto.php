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

// Obtener el nombre de usuario de la sesión si está disponible
$nombreUsuario = isset($_SESSION["username"]) ? $_SESSION["username"] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $mensaje = $_POST["mensaje"];

    // Recoger nombres de archivos adjuntos
    $archivosAdjuntos = isset($_FILES["adjunto"]) ? $_FILES["adjunto"]["name"] : [];

    // Mostrar los datos recibidos
    echo "<h2>Datos Recibidos:</h2>";
    echo "<p>Nombre: $nombre</p>";
    echo "<p>Correo Electrónico: $email</p>";
    echo "<p>Mensaje: $mensaje</p>";

    if (!empty($archivosAdjuntos)) {
        echo "<p>Archivos Adjuntos:</p>";
        echo "<ul>";
        foreach ($archivosAdjuntos as $nombreArchivo) {
            echo "<li>$nombreArchivo</li>";
        }
        echo "</ul>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto de Soporte Técnico</title>
    <link rel="stylesheet" href="nav.css"> 
    <link rel="stylesheet" href="contacto.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="inicio.php">Inicio</a>
        <a href="contacto.php">Contacto</a>
        <a href="logout.php">Cerrar Sesión</a>
        <a href="perfil.php">
            <?php echo htmlspecialchars($nombreUsuario); ?>
            <?php if (isset($datosUsuario)) : ?>
                <img src="<?php echo obtenerURLImagenPerfil($datosUsuario); ?>" alt="Imagen de Perfil" width="30">
            <?php elseif (isset($_SESSION["perfil_imagen"])) : ?>
                <img src="<?php echo $_SESSION["perfil_imagen"]; ?>" alt="Imagen de Perfil" width="30">
            <?php else : ?>
                <img src="https://imgs.search.brave.com/9AjUPpEmc0mw49GMwPPudd_v6QJHuO2lJHLM3FRgTGU/rs:fit:860:0:0/g:ce/aHR0cHM6Ly9pbWFn/ZXMudmV4ZWxzLmNv/bS9tZWRpYS91c2Vy/cy8zLzEzNTI0Ny9p/c29sYXRlZC9wcmV2/aWV3L2U3MGE2Mjk2/YzJhNzlkYzdhNTZh/YjA1YjEwM2YzOGU4/LXNpZ25vLWRlLXVz/dWFyaW8tY29uLWZv/bmRvLnBuZw" alt="Icono de Perfil" width="30">
            <?php endif; ?>  
        </a>
    </div>

    <h2>Contacto de Soporte Técnico</h2>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br>
        
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>
        <br>
        
        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
        <br>
        
        <label for="adjunto">Adjuntar archivo:</label>
        <input type="file" id="adjunto" name="adjunto[]" multiple>
        <br>
        
        <input type="submit" value="Enviar">
    </form>

</body>
</html>
