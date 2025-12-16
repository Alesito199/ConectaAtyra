<?php
session_start();
include 'config/db.php'; // Archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Consulta en la tabla de administradores
    $stmt_admin = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt_admin->execute([$email]);
    $admin = $stmt_admin->fetch();

    if ($admin && password_verify($pass, $admin['contrasena'])) {
        // Si es administrador, redirigir al panel de administración
        $_SESSION['admin_id'] = $admin['id_admin'];
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Inicio de sesión exitoso',
                    text: 'Bienvenido, {$admin['nombre']}!',
                    confirmButtonText: 'Continuar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'admin/menuAdmin.php';
                    }
                });
            });
        </script>";
    } else {
        // Si no es administrador, consultar en la tabla de usuarios
        $stmt_user = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND estado = 'activo'");
        $stmt_user->execute([$email]);
        $user = $stmt_user->fetch();

        if ($user && password_verify($pass, $user['contrasena'])) {
            // Si es usuario regular, redirigir al menú principal
            $_SESSION['usuario_id'] = $user['id_usuario'];
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Inicio de sesión exitoso',
                        text: 'Bienvenido, {$user['nombre']}!',
                        confirmButtonText: 'Continuar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'main/menu.php';
                        }
                    });
                });
            </script>";
        } else {
            // Si no se encuentra en ninguna tabla, mostrar error
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de inicio de sesión',
                        text: 'Correo o contraseña incorrectos. Inténtalo de nuevo.',
                        confirmButtonText: 'Aceptar'
                    });
                });
            </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="uploads/logoConectaAtyra.png" type="image/png">
    <title>Login - ConectaAtyrá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
</head>

<body class="h-screen flex">

    <!-- Sección de la imagen -->
    <div class="hidden lg:flex w-3/4 h-full justify-center items-center bg-gray-200">
        <div class="w-3/4 h-3/4 bg-contain bg-no-repeat bg-center" style="background-image: url('https://shopwindow.wpstagecoach.com/stagecoach/images/StoryIllustration.png');">
        </div>
    </div>

    <!-- Sección del formulario -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-md p-8 space-y-6 bg-gray-300 backdrop-blur-md rounded-lg shadow-md dark:bg-gray-800/30">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Bienvenido</h2>
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-400">Inicia sesión para acceder a tu cuenta</p>
            </div>

            <form class="space-y-6" method="POST" action="">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Dirección de correo electrónico</label>
                    <input type="email" name="email" id="email" placeholder="example@example.com" required
                        class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white/70 border border-gray-300 rounded-md dark:bg-gray-900/70 dark:text-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 dark:text-gray-200">Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="Tu contraseña" required
                        class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white/70 border border-gray-300 rounded-md dark:bg-gray-900/70 dark:text-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-800 dark:text-gray-300">Recuérdame</span>
                    </label>
                    <a href="#" class="text-sm text-blue-500 hover:underline">¿Olvidó su contraseña?</a>
                </div>

                <div>
                    <button type="submit"
                        class="w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                        Iniciar sesión
                    </button>
                </div>
            </form>

            <p class="text-sm text-center text-gray-900 dark:text-gray-400">
                ¿No tienes una cuenta?
                <a href="register.php" class="text-blue-500 hover:underline">Regístrate</a>.
            </p>
        </div>
    </div>
    <!-- Botón tipo chat -->
<div id="activarBtn" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
  <button onclick="activarCuentaPaso1()" style="background-color: #4b5563; border: none; border-radius: 50%; width: 60px; height: 60px; color: white; font-size: 24px;">
    ⚙️
  </button>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function activarCuentaPaso1() {
  Swal.fire({
    title: 'Activar cuenta',
    input: 'email',
    inputLabel: 'Ingrese su correo electrónico',
    confirmButtonText: 'Siguiente',
    showCancelButton: true,
    cancelButtonText: 'Cancelar'
  }).then(result => {
    if (!result.isConfirmed) return;

    const correo = result.value;
    if (!correo) return;

    activarCuentaPaso2(correo);
  });
}

function activarCuentaPaso2(correo) {
  Swal.fire({
    title: 'Verificación',
    input: 'text',
    inputLabel: 'Ingrese su número de teléfono',
    confirmButtonText: 'Siguiente',
    showCancelButton: true
  }).then(result => {
    if (!result.isConfirmed) return;

    const telefono = result.value;
    if (!telefono) return;

    activarCuentaPaso3(correo, telefono);
  });
}

function activarCuentaPaso3(correo, telefono) {
  Swal.fire({
    title: 'Último paso',
    input: 'password',
    inputLabel: 'Ingrese su contraseña',
    confirmButtonText: 'Verificar',
    showCancelButton: true
  }).then(result => {
    if (!result.isConfirmed) return;

    const contrasena = result.value;
    if (!contrasena) return;

    // Enviar todo al backend
    fetch('verificar_datos.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `correo=${encodeURIComponent(correo)}&telefono=${encodeURIComponent(telefono)}&contrasena=${encodeURIComponent(contrasena)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        Swal.fire('✅ Cuenta activada', 'Ahora puede iniciar sesión normalmente.', 'success');
      } else {
        Swal.fire('❌ Verificación fallida', 'Un administrador se pondrá en contacto vía WhatsApp.', 'info');
      }
    });
  });
}
</script>

</script>
</body>

</html>