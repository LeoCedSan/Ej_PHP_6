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


// Array para almacenar los comentarios
$comentarios = [];

// Verificar si existe un archivo JSON de comentarios
if (file_exists("blog.json")) {
    $comentarios = json_decode(file_get_contents("blog.json"), true);
}

// Procesar el formulario de comentarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["csrf_token"])) {
    // Verificar el token CSRF
    if ($_POST["csrf_token"] === $_SESSION["csrf_token"]) {
        // Recoger datos del formulario
        $nombre = $nombreUsuario; // Utilizar el nombre de usuario actual
        $comentario = $_POST["comentario"];

        // Crear un comentario con id incremental
        $nuevaId = count($comentarios) + 1;
        $nuevoComentario = [
            "id" => $nuevaId,
            "nombre" => $nombre,
            "comentario" => $comentario,
            "fecha" => date("Y-m-d H:i:s")
        ];

        // Agregar el nuevo comentario al array
        $comentarios[] = $nuevoComentario;

        // Guardar el array de comentarios en el archivo JSON
        file_put_contents("blog.json", json_encode($comentarios, JSON_PRETTY_PRINT));
    }
}

// Generar un nuevo token CSRF y guardarlo en la sesión
$_SESSION["csrf_token"] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Blog de Comentarios</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="blog.css">
</head>
<body>

<div class="navbar">
        <a href="inicio.php">Inicio</a>
        <a href="contacto.php">Contacto</a>
        <a href="cerrar_sesion.php">Cerrar Sesión</a>
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

    <h2>Blog de Comentarios</h2>

    <!-- Formulario de comentarios -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
        <label for="comentario">Comentario:</label>
        <textarea id="comentario" name="comentario" rows="4" required></textarea>
        <br>
        <input type="submit" value="Agregar Comentario">
    </form>

    <!-- Mostrar comentarios -->
    <h3>Comentarios anteriores:</h3>
    <?php if (!empty($comentarios)) : ?>
        <?php foreach ($comentarios as $comentario) : ?>
            <div class="comentario">
                <p><strong><?php echo htmlspecialchars($comentario["nombre"]); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($comentario["comentario"])); ?></p>
                <p>Fecha: <?php echo htmlspecialchars($comentario["fecha"]); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No hay comentarios.</p>
    <?php endif; ?>

</body>
</html>
