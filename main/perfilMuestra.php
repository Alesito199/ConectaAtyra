<!-- filepath: c:\wamp64\www\Aleli\main\perfilMuestra.php -->
<?php
session_start();
include '../config/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del usuario a mostrar
$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    echo "Usuario no especificado.";
    exit();
}

// Consultar la información del usuario
$stmt_usuario = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt_usuario->execute([$id_usuario]);
$usuario = $stmt_usuario->fetch();

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

// Consultar las publicaciones del usuario
$stmt_publicaciones = $conn->prepare("SELECT * FROM publicaciones WHERE id_usuario = ? ORDER BY fecha_publicacion DESC");
$stmt_publicaciones->execute([$id_usuario]);
$publicaciones = $stmt_publicaciones->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?> - ConectaAtyrá</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- HEADER SUPERIOR -->
    <nav class="bg-gray-800 text-white shadow">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo o título -->
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold">Conecta<span class="text-blue-400">Atyrá</span></h1>
                </div>

                <!-- Menú desktop -->
                <div class="hidden md:flex gap-6">
                    <a href="menu.php" class="hover:text-blue-300">Inicio</a>
                    <a href="perfil.php" class="hover:text-blue-300">Mi Perfil</a>
                    <a href="mensajes.php" class="hover:text-blue-300">Mensajes</a>
                    <a href="eventos.php" class="hover:text-blue-300">Eventos</a>
                    <a href="publicacion.php" class="hover:text-blue-300">Mis Publicaciones</a>
                    <a href="../config/funciones/logout.php" class="hover:text-red-400">Cerrar sesión</a>
                </div>

                <!-- Botón móvil -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menú móvil -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-700">
            <a href="menu.php" class="block px-4 py-2 hover:bg-gray-600">Inicio</a>
            <a href="perfil.php" class="block px-4 py-2 hover:bg-gray-600">Mi Perfil</a>
            <a href="mensajes.php" class="block px-4 py-2 hover:bg-gray-600">Mensajes</a>
            <a href="eventos.php" class="block px-4 py-2 hover:bg-gray-600">Eventos</a>
                    <a href="publicacion.php" class="block px-4 py-2 hover:bg-gray-600">Mis Publicaciones</a>
            <a href="../config/funciones/logout.php" class="block px-4 py-2 hover:bg-red-500">Cerrar sesión</a>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="max-w-5xl mx-auto mt-6 grid grid-cols-12 gap-4 items-start">

        <!-- PERFIL DEL USUARIO -->
        <section class="col-span-4 bg-white p-6 rounded-xl shadow">
            <div class="text-center">
                <img src="../<?php echo htmlspecialchars($usuario['foto_perfil'] ?? 'uploads/default.jpg'); ?>"
                    class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-2 border-blue-500" alt="Foto de perfil">
                <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($usuario['nombre']); ?></h2>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($usuario['profesion']); ?></p>
                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($usuario['ciudad']); ?></p>
                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($usuario['telefono']); ?></p>
                <p class="text-sm text-gray-600 mt-4"><?php echo nl2br(htmlspecialchars($usuario['bio'] ?? '')); ?></p>
                <p class="text-sm text-gray-600 mt-4"><a href="../<?php echo htmlspecialchars($usuario['cv_usuario']); ?>" target="_blank"
                        class="text-blue-600 hover:underline inline-flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m14 9.006h-.335a1.647 1.647 0 0 1-1.647-1.647v-1.706a1.647 1.647 0 0 1 1.647-1.647L19 12M5 12v5h1.375A1.626 1.626 0 0 0 8 15.375v-1.75A1.626 1.626 0 0 0 6.375 12H5Zm9 1.5v2a1.5 1.5 0 0 1-1.5 1.5v0a1.5 1.5 0 0 1-1.5-1.5v-2a1.5 1.5 0 0 1 1.5-1.5v0a1.5 1.5 0 0 1 1.5 1.5Z" />
                        </svg>
                        <span>Ver mi Curriculum Vitae</span>
                    </a>
                </p>
                <hr class="my-4 border-t-2 border-blue-700">
            </div>

            <!-- Botón para abrir el chat -->
            <div class="mt-4 text-center">
                <button id="abrir-chat" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Enviar mensaje
                </button>
            </div>
            <hr class="my-4 border-t-2 border-blue-700">
            <!-- LÍNEA DE TIEMPO -->
            <div class="mt-6">
                <h3 class="text-lg font-bold text-blue-700 mb-4">Experiencia Laboral</h3>

                <?php
                // Consultar las experiencias laborales del usuario
                $stmt_experiencias = $conn->prepare("SELECT * FROM experiencia WHERE id_usuario = ? ORDER BY fecha_inicio DESC");
                $stmt_experiencias->execute([$id_usuario]);
                $experiencias = $stmt_experiencias->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (!empty($experiencias)): ?>
                    <ul class="space-y-4">
                        <?php foreach ($experiencias as $exp): ?>
                            <li class="relative pl-6">
                                <div class="absolute left-0 top-1 w-4 h-4 bg-blue-600 rounded-full"></div>
                                <div class="bg-gray-50 p-4 rounded shadow">
                                    <h4 class="text-md font-bold"><?php echo htmlspecialchars($exp['cargo']); ?></h4>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($exp['empresa']); ?></p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <?php echo date('d/m/Y', strtotime($exp['fecha_inicio'])); ?> -
                                        <?php echo $exp['fecha_fin'] ? date('d/m/Y', strtotime($exp['fecha_fin'])) : 'Presente'; ?>
                                    </p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-500">Este usuario no ha registrado experiencia laboral.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- PUBLICACIONES DEL USUARIO -->
        <section class="col-span-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Publicaciones de <?php echo htmlspecialchars($usuario['nombre']); ?></h2>

            <?php if (!empty($publicaciones)): ?>
                <?php foreach ($publicaciones as $pub): ?>
                    <div class="bg-white p-5 rounded-xl shadow mb-4">
                        <p class="text-gray-800 mb-2"><?php echo nl2br(htmlspecialchars($pub['contenido'])); ?></p>

                        <?php if (!empty($pub['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($pub['imagen']); ?>" alt="Imagen de la publicación"
                                class="rounded-lg mt-3 max-h-80 object-contain w-full">
                        <?php endif; ?>

                        <div class="text-sm text-gray-500 mt-2">
                            Publicado el <?php echo date('d/m/Y H:i', strtotime($pub['fecha_publicacion'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-sm text-gray-500">Este usuario no ha realizado publicaciones.</p>
            <?php endif; ?>
        </section>

    </main>

    <!-- Modal para el chat -->
    <div id="chat-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white w-96 p-4 rounded-lg shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Chat con <?php echo htmlspecialchars($usuario['nombre']); ?></h3>
                <button id="cerrar-chat" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div id="chat-mensajes" class="h-64 overflow-y-auto border p-2 rounded mb-4 bg-gray-50">
                <!-- Los mensajes se cargarán aquí -->
            </div>
            <form id="form-enviar-mensaje" class="flex">
                <input type="hidden" name="emisor_id" value="<?php echo $_SESSION['usuario_id']; ?>">
                <input type="hidden" name="receptor_id" value="<?php echo $id_usuario; ?>">
                <input type="text" name="mensaje" id="mensaje" placeholder="Escribe un mensaje..."
                    class="flex-1 border rounded-l px-2 py-1 focus:outline-none">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded-r hover:bg-blue-700">Enviar</button>
            </form>
        </div>
    </div>

    <script>
        const abrirChat = document.getElementById('abrir-chat');
        const cerrarChat = document.getElementById('cerrar-chat');
        const chatModal = document.getElementById('chat-modal');
        const chatMensajes = document.getElementById('chat-mensajes');
        const formEnviarMensaje = document.getElementById('form-enviar-mensaje');
        const mensajeInput = document.getElementById('mensaje');

        const emisorId = <?php echo json_encode($_SESSION['usuario_id']); ?>;
        const receptorId = <?php echo json_encode($id_usuario); ?>;

        // Abrir el chat
        abrirChat.addEventListener('click', () => {
            chatModal.classList.remove('hidden');
            cargarMensajes();
        });

        // Cerrar el chat
        cerrarChat.addEventListener('click', () => {
            chatModal.classList.add('hidden');
        });

        // Enviar mensaje
        formEnviarMensaje.addEventListener('submit', (e) => {
            e.preventDefault();

            const mensaje = mensajeInput.value.trim();
            if (mensaje === '') return;

            fetch('procesos/enviarMensaje.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(new FormData(formEnviarMensaje))
            }).then(() => {
                mensajeInput.value = '';
                cargarMensajes();
            });
        });

        // Cargar mensajes en tiempo real
        function cargarMensajes() {
            fetch(`procesos/obtenerMensajes.php?emisor_id=${emisorId}&receptor_id=${receptorId}`)
                .then(response => response.json())
                .then(mensajes => {
                    chatMensajes.innerHTML = mensajes.map(mensaje => `
                        <div class="mb-2 ${mensaje.emisor_id == emisorId ? 'text-right' : 'text-left'}">
                            <p class="inline-block px-3 py-2 rounded-lg ${mensaje.emisor_id == emisorId ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'}">
                                ${mensaje.mensaje}
                            </p>
                        </div>
                    `).join('');
                    chatMensajes.scrollTop = chatMensajes.scrollHeight;
                });
        }

        // Actualizar mensajes cada 3 segundos
        setInterval(cargarMensajes, 3000);
    </script>

</body>

</html>