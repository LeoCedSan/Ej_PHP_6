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

// Verificar si el formulario de géneros de videojuegos se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_genres"])) {
    // Obtener los géneros seleccionados por el usuario
    $genres = isset($_POST["genres"]) ? $_POST["genres"] : [];

    // Guardar los géneros en el array del usuario
    $datosUsuario["preferencias"] = $genres;

    // Guardar el array actualizado en el archivo JSON
    file_put_contents("usuarios.json", json_encode($usuarios));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="inicio.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="inicio.php">Inicio</a>
        <a href="contacto.php">Contacto</a>
        <a href="blog.php">Blog</a>
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

    <!-- Contenido de la página -->
    <h2>Bienvenido</h2>
    <p>Hola, <?php echo htmlspecialchars($nombreUsuario); ?>.</p>

    <!-- Formulario de géneros de videojuegos -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h2>Selecciona tus géneros de videojuegos favoritos:</h2>
        <label class="checkbox-container">
            <input type="checkbox" name="genres[]" value="Aventura" <?php echo (isset($datosUsuario["preferencias"]) && in_array("Aventura", $datosUsuario["preferencias"])) ? "checked" : ""; ?>> Aventura
        </label>
        <label class="checkbox-container">
            <input type="checkbox" name="genres[]" value="Acción" <?php echo (isset($datosUsuario["preferencias"]) && in_array("Acción", $datosUsuario["preferencias"])) ? "checked" : ""; ?>> Acción
        </label>
        <label class="checkbox-container">
            <input type="checkbox" name="genres[]" value="Estrategia" <?php echo (isset($datosUsuario["preferencias"]) && in_array("Estrategia", $datosUsuario["preferencias"])) ? "checked" : ""; ?>> Estrategia
        </label>
        <label class="checkbox-container">
            <input type="checkbox" name="genres[]" value="Deportes" <?php echo (isset($datosUsuario["preferencias"]) && in_array("Deportes", $datosUsuario["preferencias"])) ? "checked" : ""; ?>> Deportes
        </label>
        <br>
        <input type="submit" name="submit_genres" value="Guardar Géneros">
    </form>


</body>
</html>
