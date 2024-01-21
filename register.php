<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Lee el archivo JSON existente (si hay alguno)
    $usuarios = [];
    if (file_exists("usuarios.json")) {
        $usuarios = json_decode(file_get_contents("usuarios.json"), true);
    }

    // Verifica si el usuario ya existe
    foreach ($usuarios as $usuarioExistente) {
        if ($usuarioExistente["username"] === $username || $usuarioExistente["email"] === $email) {
            // Si el usuario ya existe, redirige al formulario con un mensaje de error
            header("Location: register.php?error=UsuarioExistente");
            exit();
        }
    }

    // Crea un array con los datos del usuario
    $usuario = array(
        "username" => $username,
        "email" => $email,
        "password" => $password
    );

    // Agrega el nuevo usuario al array
    $usuarios[] = $usuario;

    // Guarda el array actualizado en el archivo JSON
    file_put_contents("usuarios.json", json_encode($usuarios));

    // Redirecciona a una página de éxito o realiza alguna otra acción
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario PHP</title>
</head>
<body>
<?php
// Muestra un mensaje de error si se proporciona en la URL
if (isset($_GET['error']) && $_GET['error'] === 'UsuarioExistente') {
    echo '<p style="color: red;">El usuario o correo electrónico ya existe. Inténtalo con otro.</p>';
}
?>

<h2>Registro de Usuario</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="username">Nombre de Usuario:</label>
    <input type="text" name="username" required><br>

    <label for="email">Correo Electrónico:</label>
    <input type="email" name="email" required><br>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required><br>

    <input type="submit" value="Registrar">
</form>
</body>
</html>
