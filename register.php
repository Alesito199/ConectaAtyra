
<?php
include 'config/db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telefono = $_POST['telefono'];
    $ciudad = $_POST['ciudad'];
    $profesion = $_POST['profesion'];

    // Insertar el usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contrasena, telefono, ciudad, profesion, estado) VALUES (?, ?, ?, ?, ?, ?, 'activo')");
    if ($stmt->execute([$nombre, $email, $password, $telefono, $ciudad, $profesion])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Registro exitoso',
                    text: 'Ahora puedes iniciar sesión.',
                    confirmButtonText: 'Iniciar sesión'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php'; // Redirigir al login
                    }
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="uploads/logoConectaAtyra.png" type="image/png">
    <title>Registro - Aleli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="min-h-screen flex bg-gray-100 dark:bg-gray-900">
    <div class="hidden lg:flex w-1/2 h-screen justify-center items-center bg-gray-200">
        <div class="w-3/4 h-3/4 bg-contain bg-no-repeat bg-center" style="background-image: url('https://shopwindow.wpstagecoach.com/stagecoach/images/StoryIllustration.png');">
        </div>
    </div>

    <!-- Sección del formulario -->
    <div class="w-full lg:w-1/2 flex items-center justify-center">
        <div class="w-full max-w-md p-6 bg-gray-300 backdrop-blur-md rounded-lg shadow-md dark:bg-gray-800/30">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Regístrate</h2>
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-400">Crea una cuenta para acceder al sistema</p>
            </div>

            <form class="space-y-4" method="POST" action="">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Nombre completo</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Tu nombre completo" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Correo electrónico</label>
                    <input type="email" name="email" id="email" placeholder="example@example.com" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="Tu contraseña" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" placeholder="Tu número de teléfono" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="ciudad" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Ciudad</label>
                    <input type="text" name="ciudad" id="ciudad" placeholder="Tu ciudad" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="profesion" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Profesión</label>
                    <input type="text" name="profesion" id="profesion" placeholder="Tu profesión" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:ring-2 focus:ring-blue-400">
                    Registrarse
                </button>
            </form>

            <p class="text-sm text-center mt-4 text-gray-900 dark:text-gray-400">
                ¿Ya tienes una cuenta?
                <a href="index.php" class="text-blue-500 hover:underline">Inicia sesión</a>
            </p>
        </div>
    </div>
</body>

</html>
