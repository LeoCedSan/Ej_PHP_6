<?php
session_start();

// Función para verificar las credenciales del usuario
function verificarCredenciales($username, $password, $usuarios) {
    foreach ($usuarios as $usuario) {
        if ($usuario["username"] === $username && password_verify($password, $usuario["password"])) {
            return true;
        }
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $usuarios = [];
    if (file_exists("usuarios.json")) {
        $usuarios = json_decode(file_get_contents("usuarios.json"), true);
    }

    if (verificarCredenciales($username, $password, $usuarios)) {
        // Iniciar sesión y redireccionar a la página de inicio
        $_SESSION["username"] = $username;
        header("Location: inicio.php");
        exit();
    } else {
        $mensajeError = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="login.css">

</head>
<body>
<h2>Iniciar Sesión</h2>

<?php if (isset($mensajeError)) : ?>
    <p style="color: red;"><?php echo $mensajeError; ?></p>
<?php endif; ?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="username">Nombre de Usuario:</label>
    <input type="text" name="username" required><br>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required><br>

    <input type="submit" id="btn" value="Iniciar Sesión">
    <a href="register.php">Registrarse</a>
</form>
</body>
</html>
