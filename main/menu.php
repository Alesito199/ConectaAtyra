<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Error al obtener la información del usuario.";
    exit();
}

// Buscar usuarios por profesión
$profesion_buscada = $_GET['profesion'] ?? '';
$usuarios_filtrados = [];
if (!empty($profesion_buscada)) {
    $stmt_busqueda = $conn->prepare("SELECT * FROM usuarios WHERE profesion LIKE ?");
    $stmt_busqueda->execute(['%' . $profesion_buscada . '%']);
    $usuarios_filtrados = $stmt_busqueda->fetchAll(PDO::FETCH_ASSOC);
}
// Obtener profesiones únicas de las publicaciones
$stmt_profesiones = $conn->query("
    SELECT DISTINCT u.profesion 
    FROM publicaciones p 
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    WHERE u.profesion IS NOT NULL
");
$profesiones_publicaciones = $stmt_profesiones->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Inicio - ConectaAtyrá</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

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

    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>


    <!-- CONTENIDO PRINCIPAL -->
    <main class="max-w-7xl mx-auto mt-6 grid grid-cols-12 gap-4 items-start">

        <!-- SIDEBAR IZQUIERDO -->
        <aside class="col-span-3 bg-white p-4 rounded-xl shadow">
            <div class="text-center">
                <img id="preview-foto" src="../<?php echo htmlspecialchars($usuario['foto_perfil'] ?? 'uploads/default.jpg'); ?>"
                    class="w-24 h-24 rounded-full mx-auto mb-2 object-cover border-2 border-blue-500">
                <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($usuario['nombre']); ?></h2>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($usuario['profesion']); ?></p>
            </div>
            <hr class="my-4 border-t-2 border-blue-700">
            <ul class="space-y-2 text-sm text-gray-700 text-center">
                <?php if (!empty($usuario['cv_usuario'])): ?>
                    <li>
                        <a href="../<?php echo htmlspecialchars($usuario['cv_usuario']); ?>" target="_blank"
                            class="text-blue-600 hover:underline inline-flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m14 9.006h-.335a1.647 1.647 0 0 1-1.647-1.647v-1.706a1.647 1.647 0 0 1 1.647-1.647L19 12M5 12v5h1.375A1.626 1.626 0 0 0 8 15.375v-1.75A1.626 1.626 0 0 0 6.375 12H5Zm9 1.5v2a1.5 1.5 0 0 1-1.5 1.5v0a1.5 1.5 0 0 1-1.5-1.5v-2a1.5 1.5 0 0 1 1.5-1.5v0a1.5 1.5 0 0 1 1.5 1.5Z" />
                            </svg>
                            <span>Ver mi Curriculum Vitae</span>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </aside>

        <!-- FEED DE PUBLICACIONES -->
        <section class="col-span-6">
            <div class="bg-white p-6 rounded-xl shadow mb-6">
                <h2 class="text-xl font-bold mb-4 text-blue-500">Crear publicación</h2>

                <form action="procesos/publicar.php" method="POST" enctype="multipart/form-data" class="space-y-4">

                    <!-- Campo de texto -->
                    <textarea name="contenido" rows="4" placeholder="¿Qué querés compartir hoy?"
                        class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"></textarea>

                    <!-- Subida de imagen -->
                    <div class="border-2 border-dashed border-blue-800 p-4 text-center rounded-md">
                        <label for="imagen" class="cursor-pointer text-gray-600 hover:underline">
                            Click para subir una imagen o arrastrala aquí (jpg, png, gif)
                        </label>
                        <input type="file" name="imagen" id="imagen" class="hidden" accept="image/*">
                    </div>

                    <!-- Vista previa -->
                    <div id="preview-container" class="mt-4 hidden">
                        <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
                        <img id="preview-image" src="" class="max-h-64 mx-auto rounded-lg shadow" alt="Vista previa">
                    </div>

                    <!-- Botón de publicar -->
                    <div class="flex justify-end gap-2">
                        <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Publicar</button>
                    </div>

                </form>
            </div>

            <?php
            // Consulta para obtener las publicaciones junto con la información del usuario que las publicó
            $stmt = $conn->query("
                SELECT p.*, u.nombre, u.profesion, u.foto_perfil 
                FROM publicaciones p 
                JOIN usuarios u ON p.id_usuario = u.id_usuario 
                ORDER BY p.fecha_publicacion DESC
            ");

            while ($pub = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="bg-white p-5 rounded-xl shadow mb-4">
                    <div class="flex items-center mb-3 gap-3">
                        <!-- Mostrar la foto del usuario que publicó -->
                        <img src="../<?php echo htmlspecialchars($pub['foto_perfil'] ?? 'uploads/default.jpg'); ?>"
                            class="w-12 h-12 rounded-lg" alt="Perfil">
                        <div>
                            <p class="font-semibold"><?php echo htmlspecialchars($pub['nombre']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($pub['profesion']); ?></p>
                        </div>
                    </div>

                    <p class="text-gray-800 mb-2"><?php echo nl2br(htmlspecialchars($pub['contenido'])); ?></p>

                    <?php if (!empty($pub['imagen'])): ?>
                        <img src="<?php echo htmlspecialchars($pub['imagen']); ?>" alt="Imagen de la publicación"
                            class="rounded-lg mt-3 max-h-80 object-contain w-full">
                    <?php endif; ?>

                    <div class="text-sm text-blue-500 mt-2">
                        Publicado el <?php echo date('d/m/Y H:i', strtotime($pub['fecha_publicacion'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>

        <!-- SIDEBAR DERECHO -->
        <aside class="col-span-3 bg-white p-4 rounded-xl shadow">
            <div class="p-2 rounded-xl shadow mt-4">
                <div class="mt-6">
                    <h3 class="font-semibold text-lg mb-3">Buscar por profesión</h3>
                    <form method="GET" action="menu.php" class="space-y-4">
                        <input type="text" name="profesion" placeholder="Ej: Diseñador, Ingeniero"
                            value="<?php echo htmlspecialchars($profesion_buscada); ?>"
                            class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800">
                        <button type="submit" class="w-full bg-gray-600 px-4 py-2 rounded hover:bg-gray-400 text-white">
                            Buscar
                        </button>
                    </form>

                    <?php if (!empty($usuarios_filtrados)): ?>
                        <h4 class="font-semibold text-lg mt-6">Resultados:</h4>
                        <ul class="mt-4 space-y-3">
                            <?php foreach ($usuarios_filtrados as $usuario): ?>
                                <li class="flex items-center gap-3">
                                    <img src="../<?php echo htmlspecialchars($usuario['foto_perfil'] ?? 'uploads/default.jpg'); ?>"
                                        class="w-10 h-10 rounded-full" alt="">
                                    <div>
                                        <a href="perfilMuestra.php?id=<?php echo $usuario['id_usuario']; ?>" class="block">
                                            <p class="font-semibold"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($usuario['profesion']); ?></p>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif (!empty($profesion_buscada)): ?>
                        <p class="text-sm text-gray-500 mt-4">No se encontraron resultados para "<?php echo htmlspecialchars($profesion_buscada); ?>".</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="p-4 rounded-xl shadow mt-4">
                <!-- Filtro por profesiones -->
                <div class="mt-6">
                    <h4 class="font-semibold text-lg mb-3">Filtrar por profesión</h4>
                    <form id="filtro-profesiones" class="space-y-2">
                        <?php foreach ($profesiones_publicaciones as $profesion): ?>
                            <div>
                                <input type="checkbox" name="profesion[]" value="<?php echo htmlspecialchars($profesion); ?>"
                                    id="profesion-<?php echo htmlspecialchars($profesion); ?>"
                                    class="mr-2">
                                <label for="profesion-<?php echo htmlspecialchars($profesion); ?>">
                                    <?php echo htmlspecialchars($profesion); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <button type="button" id="filtrar-publicaciones"
                            class="w-full bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 mt-4">
                            Filtrar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

    </main>
    <!-- Botón tipo chat -->
    <div id="chatBtn" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button onclick="abrirChat()" style="background-color: #4b5563; border: none; border-radius: 50%; width: 60px; height: 60px; color: white; font-size: 24px;">
            ⚙️
        </button>
    </div>

    <!-- Ventana tipo chat -->
    <div id="chatVentana" style="display: none; position: fixed; bottom: 90px; right: 20px; width: 350px; background: white; border: 2px solid #4b5563; border-radius: 12px; box-shadow: 0 0 12px rgba(0,0,0,0.3); z-index: 1001; padding: 20px;">
        <div id="chatMensajes" style="max-height: 350px; overflow-y: auto; font-size: 15px;">
            <p><strong>🤖 Soporte:</strong> ¡Buenas! Tiene dos opciones:<br>
                1️⃣ Deshabilitar cuenta<br>
                2️⃣ Hacer una consulta<br>
                <em>Responda con 1 o 2</em>
            </p>
        </div>

        <!-- Input + Botón -->
        <div style="display: flex; gap: 5px; margin-top: 12px;">
            <input type="text" id="chatInput" placeholder="Escriba aquí..."
                onkeypress="if(event.key === 'Enter') enviarChat()"
                style="flex: 1; padding: 10px; border: 2px solid #4b5563; border-radius: 6px;">
            <button onclick="enviarChat()"
                style="padding: 10px 14px; background-color: #4b5563; color: white; border: none; border-radius: 6px;">
                Enviar
            </button>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let estadoChat = 'inicio';

        function abrirChat() {
            document.getElementById("chatVentana").style.display = 'block';
        }

        function enviarChat() {
            const input = document.getElementById("chatInput");
            const mensaje = input.value.trim();
            if (!mensaje) return;

            const chat = document.getElementById("chatMensajes");
            chat.innerHTML += `<p><strong>🙋 Usted:</strong> ${mensaje}</p>`;

            if (estadoChat === 'inicio') {
                if (mensaje === '1') {
                    chat.innerHTML += `<p><strong>🤖 Soporte:</strong> Su cuenta será deshabilitada.</p>`;
                    deshabilitarCuenta();
                } else if (mensaje === '2') {
                    chat.innerHTML += `<p><strong>🤖 Soporte:</strong> Por favor, escriba su consulta.</p>`;
                    estadoChat = 'consulta';
                } else {
                    chat.innerHTML += `<p><strong>🤖 Soporte:</strong> Opción inválida. Escriba 1 o 2.</p>`;
                }
            } else if (estadoChat === 'consulta') {
                guardarConsulta(mensaje);
                chat.innerHTML += `<p><strong>🤖 Soporte:</strong> Gracias por su consulta. Le responderemos pronto.</p>`;
                estadoChat = 'fin';
            }

            input.value = '';
            chat.scrollTop = chat.scrollHeight;
        }

        function deshabilitarCuenta() {
            Swal.fire({
                title: '¿Está seguro?',
                text: 'Si deshabilita su usuario, ya no podrá ingresar a su cuenta. Deberá contactar al administrador para reactivarla.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, deshabilitar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#4b5563'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llamada al backend para actualizar estado
                    fetch('deshabilitar_cuenta.php', {
                            method: 'POST'
                        })
                        .then(res => res.text()) // 👈 usar .text() en vez de .json()
                        .then(responseText => {
                            console.log('Respuesta del servidor:', responseText);
                            try {
                                const data = JSON.parse(responseText); // Intentamos convertirlo a JSON
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Cuenta deshabilitada',
                                        text: 'Ya no podrá ingresar nuevamente. Contáctese con un administrador si desea recuperar el acceso.',
                                        confirmButtonText: 'Aceptar'
                                    }).then(() => {
                                        window.location.href = '../config/funciones/logout.php';
                                    });
                                } else {
                                    Swal.fire('Error', 'No se pudo deshabilitar la cuenta.', 'error');
                                }
                            } catch (e) {
                                Swal.fire('Error', 'Respuesta inválida del servidor. Ver consola.', 'error');
                                console.error('Error al parsear JSON:', e);
                            }
                        });

                }
            });
        }


        function guardarConsulta(texto) {
            fetch('guardar_consulta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'mensaje=' + encodeURIComponent(texto)
            });
        }
    </script>



    <!-- Script vista previa -->
    <script>
        const fileInput = document.getElementById('imagen');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
                previewImage.src = '';
            }
        });

        const filtroProfesionesForm = document.getElementById('filtro-profesiones');
        const botonFiltrar = document.getElementById('filtrar-publicaciones');
        const publicacionesContainer = document.querySelector('section.col-span-6'); // Contenedor de publicaciones

        botonFiltrar.addEventListener('click', () => {
            const checkboxes = filtroProfesionesForm.querySelectorAll('input[name="profesion[]"]:checked');
            const profesionesSeleccionadas = Array.from(checkboxes).map(checkbox => checkbox.value);

            // Enviar las profesiones seleccionadas al servidor
            fetch('procesos/filtrarPublicaciones.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        profesiones: profesionesSeleccionadas
                    })
                })
                .then(response => response.json())
                .then(publicaciones => {
                    // Limpiar el contenedor de publicaciones
                    publicacionesContainer.innerHTML = '';

                    // Mostrar las publicaciones filtradas
                    publicaciones.forEach(pub => {
                        publicacionesContainer.innerHTML += `
                <div class="bg-white p-5 rounded-xl shadow mb-4">
                    <div class="flex items-center mb-3 gap-3">
                        <img src="../${pub.foto_perfil ?? 'uploads/default.jpg'}" class="w-12 h-12 rounded-lg" alt="Perfil">
                        <div>
                            <p class="font-semibold">${pub.nombre}</p>
                            <p class="text-sm text-gray-500">${pub.profesion}</p>
                        </div>
                    </div>
                    <p class="text-gray-800 mb-2">${pub.contenido}</p>
                    ${pub.imagen ? `<img src="${pub.imagen}" alt="Imagen de la publicación" class="rounded-lg mt-3 max-h-80 object-contain w-full">` : ''}
                    <div class="text-sm text-gray-500 mt-2">
                        Publicado el ${new Date(pub.fecha_publicacion).toLocaleString()}
                    </div>
                </div>
            `;
                    });
                })
                .catch(error => console.error('Error al filtrar publicaciones:', error));
        });
    </script>

</body>

</html>