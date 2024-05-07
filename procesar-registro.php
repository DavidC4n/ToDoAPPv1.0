<?php
// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "Unbo_1234", "usuarios");

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Obtener datos del formulario de inicio de sesión
        $correo = $_POST['email'];
        $contrasena = $_POST['password'];

        // Buscar el usuario en la base de datos
        $sql = "SELECT id, contraseña FROM usuarios WHERE correo_electronico = '$correo'";
        $resultado = mysqli_query($conexion, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            // Usuario encontrado, verificar la contraseña
            $fila = mysqli_fetch_assoc($resultado);
            $contrasena_hash = $fila['contraseña'];
            if (password_verify($contrasena, $contrasena_hash)) {
                // Contraseña válida, iniciar sesión
                session_start();
                $_SESSION['usuario'] = $fila['id'];
                echo "Inicio de sesión exitoso. Redireccionando...";
                // Redireccionar a la página principal u otra página después del inicio de sesión
                header("Location: index.html");
                exit();
            } else {
                // Contraseña incorrecta
                echo "Contraseña incorrecta. Intenta de nuevo.";
            }
        } else {
            // Usuario no encontrado
            echo "El usuario no existe. Por favor, regístrate.";
        }
    } elseif (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        // Obtener datos del formulario de registro
        $nombre_usuario = $_POST['username'];
        $correo = $_POST['email'];
        $contrasena = $_POST['password'];

        // Verificar si el correo electrónico ya está registrado
        $sql = "SELECT * FROM usuarios WHERE correo_electronico = '$correo'";
        $resultado = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($resultado) > 0) {
            // El correo electrónico ya está registrado
            echo "Este correo electrónico ya está registrado. Por favor, intenta con otro.";
        } else {
            // Hashear la contraseña antes de almacenarla
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            $sql_insert = "INSERT INTO usuarios (nombre_usuario, correo_electronico, contraseña) VALUES ('$nombre_usuario', '$correo', '$contrasena_hash')";
            if (mysqli_query($conexion, $sql_insert)) {
                // Registro exitoso
                echo "Registro exitoso. Redireccionando...";
                // Redireccionar al usuario al formulario de inicio de sesión después del registro
                header("Location: login.html");
                exit();
            } else {
                // Error al registrar el usuario
                echo "Error al registrar el usuario: " . mysqli_error($conexion);
            }
        }
    } else {
        // Petición incorrecta
        echo "Petición incorrecta.";
    }
} else {
    // Método de solicitud incorrecto
    echo "Método de solicitud incorrecto.";
}

// Cerrar la conexión
mysqli_close($conexion);
?>


