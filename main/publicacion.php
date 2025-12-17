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

            <h1 class="text-6xl font-bold mb-4 text-gray-700">Mis Publicaiones</h1>


            <?php
            // Consulta para obtener las publicaciones junto con la información del usuario que las publicó
            $stmt = $conn->query("
                SELECT p.*, u.nombre, u.profesion, u.foto_perfil 
                FROM publicaciones p 
                JOIN usuarios u ON p.id_usuario = u.id_usuario WHERE p.id_usuario = $usuario_id
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
                    <div class="flex justify-end gap-2 mt-3">
                        <button
                            onclick='abrirModalEdicion(<?php echo json_encode([
                                                            'id_publicacion' => $pub['id_publicacion'],
                                                            'contenido' => $pub['contenido'],
                                                            'imagen' => $pub['imagen']
                                                        ]); ?>)'
                            class="px-4 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">
                            Editar
                        </button>



                        <form id="form-eliminar-<?php echo $pub['id_publicacion']; ?>" action="procesos/eliminar_publicacion.php" method="POST" class="inline">
                            <input type="hidden" name="id_publicacion" value="<?php echo $pub['id_publicacion']; ?>">
                            <button type="button" onclick="confirmarEliminacion(<?php echo $pub['id_publicacion']; ?>)" class="px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                Eliminar
                            </button>
                        </form>

                    </div>
                </div>
            <?php endwhile; ?>
        </section>



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
    <!-- Modal de Edición -->
    <div id="modal-editar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">

        <div class="bg-white rounded-lg w-full max-w-xl p-6 shadow-lg relative">
            <button onclick="cerrarModalEdicion()" class="absolute top-2 right-3 text-gray-600 text-2xl font-bold">&times;</button>
            <h2 class="text-lg font-bold mb-4">Editar Publicación</h2>

            <form id="form-edicion" enctype="multipart/form-data">
                <input type="hidden" name="id_publicacion" id="editar-id">

                <textarea name="contenido" id="editar-contenido" rows="4"
                    class="w-full border border-gray-300 p-3 rounded mb-3"></textarea>

                <div class="mb-3">
                    <label class="block mb-1">Imagen actual:</label>
                    <img id="editar-imagen-actual" src="" class="max-h-48 rounded">
                </div>

                <label class="block mb-1">Cambiar imagen:</label>
                <input type="file" name="nueva_imagen" id="editar-nueva-imagen" class="mb-4">

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="cerrarModalEdicion()" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function abrirModalEdicion(pub) {
            document.getElementById('modal-editar').classList.remove('hidden');
            document.getElementById('editar-id').value = pub.id_publicacion;
            document.getElementById('editar-contenido').value = pub.contenido;
            document.getElementById('editar-imagen-actual').src = (pub.imagen || 'uploads/default.jpg');

        }

        function cerrarModalEdicion() {
            document.getElementById('modal-editar').classList.add('hidden');
            document.getElementById('form-edicion').reset();
        }

        document.getElementById('form-edicion').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch('procesos/editar_publicacion.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Éxito', 'Publicación actualizada', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Error al actualizar', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Error inesperado', 'error');
                });
        });
        // Vista previa de nueva imagen en el modal
        const inputNuevaImagen = document.getElementById('editar-nueva-imagen');
        const imgPreview = document.getElementById('editar-imagen-actual');

        inputNuevaImagen.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
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

    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará la publicación permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-eliminar-' + id).submit();
                }
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


    <div class="min-h-screen bg-gray-100 px-4">
        <div class="bg-white shadow-xl rounded-2xl max-w-4xl mx-auto p-8">
            <!-- CABECERA -->
            <div class="w-full bg-white shadow-md py-4 px-6 flex justify-start mb-4">
                <button onclick="window.location.href='../blank_boletines_disponibles/'"
                    class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 transition font-semibold">
                    ← Volver a Competencias
                </button>
            </div>
            <div class="text-center mb-10">
                <div class="flex justify-center gap-8 items-center mb-4">
                    <img src="https://cdn.conmebol.com/wp-content/uploads/2024/05/logo-conmebol-principal.png" class="w-20 h-auto">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800"><?php echo $nombreCompe; ?></h2>
                        <p class="text-gray-500"><?php echo $ShortNameCompe . ' - Temporada ' . $temporadaCompe; ?></p>
                    </div>
                    <img src="<?php echo $imgCompe; ?>" class="w-20 h-auto">
                </div>
                <p class="text-lg text-gray-600">Configuración del Boletín Tecnico</p>
            </div>

            <!-- Formulario -->
            <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Configuración del Boletín Técnico <?= $temporadaCompe ?></h2>

                <form method="POST" onsubmit="return validarYEnviar();" class="space-y-5">
                    <input type="hidden" name="competitionFifaId" value="<?= $competitionFifaIdGlobal ?>">
                    <input type="hidden" name="organizacionFifaId" value="<?= $organizacionFifaIdGlobal ?>">

                    <!-- Imagen fondo portada -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Imagen de Fondo Portada:</label>
                        <input type="file" accept="image/*" onchange="previsualizarFondo(event)" class="w-full" <?= $hayConfiguracion ? '' : 'required' ?> />
                        <input type="hidden" name="imgFondoBase64" id="imgFondoBase64" />
                        <?php if ($imgFondoURL): ?>
                            <img src="<?= $imgFondoURL ?>" id="fondoPreview" class="mx-auto rounded-md border mt-2 max-h-48" />
                        <?php else: ?>
                            <img id="fondoPreview" class="hidden mx-auto rounded-md border mt-2 max-h-48" />
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Color del texto de portada:</label>
                        <select name="colorTextoPortada" class="w-full border rounded px-3 py-2">
                            <option value="#ffffff" <?= $colorTextoPortada == '#ffffff' ? 'selected' : '' ?>>Blanco</option>
                            <option value="#000000" <?= $colorTextoPortada == '#000000' ? 'selected' : '' ?>>Negro</option>
                            <option value="#007bff" <?= $colorTextoPortada == '#007bff' ? 'selected' : '' ?>>Azul</option>
                        </select>
                    </div>

                    <!-- Colores tabla -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Color de la tabla A:</label>
                        <input type="color" name="colorA" value="<?= $colorA ?>" class="w-20 h-10 border rounded cursor-pointer" />
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Color de la tabla B:</label>
                        <input type="color" name="colorB" value="<?= $colorB ?>" class="w-20 h-10 border rounded cursor-pointer" />
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Color de las Tablas en General:</label>
                        <input type="color" name="colorTablas" value="<?= $colorTablas ?>" class="w-20 h-10 border rounded cursor-pointer" />
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Color del texto de las Tablas:</label>
                        <select name="colorTextoTablaGeneral" class="w-full border rounded px-3 py-2">
                            <option value="#ffffff" <?= $colorTextoTablas == '#ffffff' ? 'selected' : '' ?>>Blanco</option>
                            <option value="#000000" <?= $colorTextoTablas == '#000000' ? 'selected' : '' ?>>Negro</option>
                            <option value="#007bff" <?= $colorTextoTablas == '#007bff' ? 'selected' : '' ?>>Azul</option>
                        </select>
                    </div>

                    <!-- Imagen fondo final -->
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Imagen de Fondo Final:</label>
                        <input type="file" accept="image/*" onchange="previsualizarFondoB(event)" class="w-full" <?= $hayConfiguracion ? '' : 'required' ?> />
                        <input type="hidden" name="imgFondoBase64B" id="imgFondoBase64B" />
                        <?php if ($imgFondoFinalURL): ?>
                            <img src="<?= $imgFondoFinalURL ?>" id="fondoPreviewB" class="mx-auto rounded-md border mt-2 max-h-48" />
                        <?php else: ?>
                            <img id="fondoPreviewB" class="hidden mx-auto rounded-md border mt-2 max-h-48" />
                        <?php endif; ?>
                    </div>

                    <!-- Botón -->
                    <div class="text-center pt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                            <?= $hayConfiguracion ? 'Actualizar' : 'Guardar' ?> Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>