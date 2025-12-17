<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
// Obtener conversaciones con el último mensaje
$stmt = $conn->prepare("
    SELECT u.id_usuario, u.nombre, u.foto_perfil, m.mensaje, m.fecha_envio
    FROM usuarios u
    JOIN mensajes m ON (u.id_usuario = m.emisor_id OR u.id_usuario = m.receptor_id)
    WHERE (m.emisor_id = ? OR m.receptor_id = ?)
    AND u.id_usuario != ? AND m.estado = 'activo'
    GROUP BY u.id_usuario
    ORDER BY MAX(m.fecha_envio) DESC
");
$stmt->execute([$usuario_id, $usuario_id, $usuario_id]);
$conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Verificar si se envió una búsqueda
$busqueda = $_GET['buscar_usuario'] ?? '';
$usuarios_encontrados = [];

if (!empty($busqueda)) {
    $stmt = $conn->prepare("
    SELECT * FROM usuarios 
    WHERE (nombre LIKE ? OR profesion LIKE ?)
    AND id_usuario != ?
    LIMIT 10
");
    $stmt->execute(["%$busqueda%", "%$busqueda%", $usuario_id]);

    $usuarios_encontrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Mensajes - ConectaAtyrá</title>
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
            <a href="publicacion.php" class="block px-4 py-2 hover:bg-gray-600">>Mis Publicaciones</a>
            <a href="../config/funciones/logout.php" class="block px-4 py-2 hover:bg-red-500">Cerrar sesión</a>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto mt-6 grid grid-cols-12 gap-6 items-start">
        <!-- Tabla de conversaciones (centro) -->
        <section class="col-span-8">
            <h1 class="text-3xl font-bold text-gray-700 mb-4">Mensajes</h1>
            <div class="bg-white p-6 rounded-xl shadow">
                <ul id="lista-conversaciones" class="space-y-4"></ul>
            </div>
        </section>

        <!-- Buscador y resultados (derecha) -->
        <aside class="col-span-4 bg-white p-6 rounded-xl shadow">
            <!-- Buscador -->
            <form method="GET" class="mb-6">
                <label for="buscar_usuario" class="block text-gray-700 font-semibold mb-2">Buscar persona:</label>
                <div class="flex gap-2">
                    <input type="text" name="buscar_usuario" id="buscar_usuario"
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-700">
                    <button type="submit"
                        class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600 whitespace-nowrap">Buscar</button>
                </div>
            </form>

            <!-- Resultados -->
            <?php if (!empty($usuarios_encontrados)): ?>
                <h2 class="text-lg font-bold text-gray-700 mb-4">Resultados:</h2>
                <ul class="space-y-4">
                    <?php foreach ($usuarios_encontrados as $usuario): ?>
                        <li class="bg-gray-100 rounded-lg p-3 flex items-center cursor-pointer gap-4 abrir-chat"
                            data-receptor-id="<?php echo $usuario['id_usuario']; ?>"
                            data-receptor-nombre="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                            <img src="../<?php echo htmlspecialchars($usuario['foto_perfil'] ?? 'uploads/default.jpg'); ?>"
                                class="w-12 h-12 rounded-full object-cover">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                                <p class="text-sm text-gray-500 truncate"><?php echo htmlspecialchars($usuario['profesion']); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif (!empty($busqueda)): ?>
                <p class="text-gray-500">No se encontraron usuarios con "<?php echo htmlspecialchars($busqueda); ?>"</p>
            <?php endif; ?>
        </aside>
    </main>


    <!-- Modal para el chat -->
    <div id="chat-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-xl overflow-hidden">
            <div class="bg-gray-600 text-white px-4 py-3 flex justify-between items-center">
                <h3 id="chat-titulo" class="font-semibold text-lg">Chat</h3>
                <button id="cerrar-chat" class="text-2xl">&times;</button>
            </div>

            <div id="chat-mensajes" class="h-72 overflow-y-auto bg-gray-50 p-4 space-y-2">
                <!-- Mensajes cargados dinámicamente -->
            </div>

            <form id="form-enviar-mensaje" class="p-3 flex border-t">
                <input type="hidden" name="emisor_id" value="<?php echo $usuario_id; ?>">
                <input type="hidden" name="receptor_id" id="receptor-id">
                <input type="text" name="mensaje" id="mensaje" placeholder="Escribe un mensaje..."
                    class="flex-1 px-4 py-2 rounded-l-lg border-t border-b border-l text-gray-700 border-gray-300 bg-white focus:outline-none">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-r-lg">
                    Enviar
                </button>
            </form>
        </div>
    </div>


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
    <script>
        const chatModal = document.getElementById('chat-modal');
        const cerrarChat = document.getElementById('cerrar-chat');
        const chatMensajes = document.getElementById('chat-mensajes');
        const formEnviarMensaje = document.getElementById('form-enviar-mensaje');
        const mensajeInput = document.getElementById('mensaje');
        const receptorIdInput = document.getElementById('receptor-id');
        const chatTitulo = document.getElementById('chat-titulo');
        let ultimaFecha = '1970-01-01 00:00:00';

        document.querySelectorAll('.abrir-chat').forEach(item => {
            item.addEventListener('click', () => {
                const receptorId = item.getAttribute('data-receptor-id');
                const receptorNombre = item.getAttribute('data-receptor-nombre');

                receptorIdInput.value = receptorId;
                chatTitulo.textContent = receptorNombre;
                chatMensajes.innerHTML = '';
                ultimaFecha = '1970-01-01 00:00:00';

                cargarMensajes(receptorId);
                chatModal.classList.remove('hidden');
            });
        });

        cerrarChat.addEventListener('click', () => chatModal.classList.add('hidden'));

        formEnviarMensaje.addEventListener('submit', (e) => {
            e.preventDefault();
            const mensaje = mensajeInput.value.trim();
            if (!mensaje) return;

            fetch('procesos/enviarMensaje.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(new FormData(formEnviarMensaje))
            }).then(() => {
                mensajeInput.value = '';
                cargarMensajes(receptorIdInput.value);
            });
        });

        function cargarMensajes(receptorId, reset = false) {
            if (reset) {
                ultimaFecha = '1970-01-01 00:00:00';
                chatMensajes.innerHTML = '';
            }

            fetch(`procesos/obtenerMensajes.php?emisor_id=<?php echo $usuario_id; ?>&receptor_id=${receptorId}`)
                .then(res => res.json())
                .then(mensajes => {
                    if (!Array.isArray(mensajes)) return;

                    chatMensajes.innerHTML = ''; // ⚠️ Limpiar todo para evitar duplicados
                    mensajes.forEach(msg => {
                        chatMensajes.innerHTML += `
<div class="${msg.emisor_id == <?php echo $usuario_id; ?> ? 'text-right' : 'text-left'}">
    <div class="inline-block px-3 py-2 rounded-lg ${msg.emisor_id == <?php echo $usuario_id; ?> ? 'bg-gray-600 text-white' : 'bg-gray-300 text-gray-800'}">
        <span>${msg.mensaje}</span>
        ${msg.emisor_id == <?php echo $usuario_id; ?> ? `
            <button onclick="editarMensaje(${msg.id_mensaje}, '${msg.mensaje.replace(/'/g, "\\'")}')" class="text-xs ml-2">✏️</button>
            <button onclick="eliminarMensaje(${msg.id_mensaje})" class="text-xs text-red-500 ml-1">🗑️</button>
        ` : ''}
    </div>
    <div class="text-xs text-gray-400 mt-1">${new Date(msg.fecha_envio).toLocaleString()}</div>
</div>`;
                    });

                    // No actualices ultimaFecha ya que estás trayendo todos los mensajes
                    chatMensajes.scrollTop = chatMensajes.scrollHeight;
                });
        }



        setInterval(() => {
            if (!chatModal.classList.contains('hidden')) cargarMensajes(receptorIdInput.value);
        }, 1000);
    </script>
    <script>
        function editarMensaje(id, mensajeActual) {
            Swal.fire({
                title: 'Editar mensaje',
                input: 'text',
                inputValue: mensajeActual,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                inputValidator: value => {
                    if (!value) return 'El mensaje no puede estar vacío';
                }
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('procesos/editarMensaje.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                id_mensaje: id,
                                mensaje: result.value
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                cargarMensajes(document.getElementById('receptor-id').value, true); // reset
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo editar el mensaje', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'No se pudo contactar con el servidor', 'error');
                        });
                }
            });
        }

        function eliminarMensaje(id) {
            Swal.fire({
                title: '¿Eliminar mensaje?',
                text: "Esto ocultará el mensaje del chat.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('procesos/eliminarMensaje.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id_mensaje=' + encodeURIComponent(id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Eliminado', 'Mensaje ocultado.', 'success').then(() => {
                                    cargarMensajes(receptorIdInput.value, true); // reset
                                });

                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'No se pudo contactar con el servidor.', 'error');
                        });
                }
            });
        }
    </script>
    <script>
        function actualizarConversaciones() {
            fetch('procesos/obtenerConversaciones.php')
                .then(res => res.json())
                .then(data => {
                    const lista = document.getElementById('lista-conversaciones');
                    lista.innerHTML = ''; // Vaciar actual

                    data.forEach(conv => {
                        lista.innerHTML += `
<li class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-4 flex items-center cursor-pointer gap-4 abrir-chat"
    data-receptor-id="${conv.id_usuario}" data-receptor-nombre="${conv.nombre}">
    <img src="../${conv.foto_perfil || 'uploads/default.jpg'}" class="w-14 h-14 rounded-full object-cover">
    <div class="flex-1 overflow-hidden">
        <p class="text-lg font-semibold text-gray-800 truncate">${conv.nombre}</p>
        <p class="text-gray-500 truncate">${conv.mensaje}</p>
    </div>
    <p class="text-xs text-gray-400 whitespace-nowrap">
        ${new Date(conv.fecha_envio).toLocaleString()}
    </p>
</li>`;
                    });

                    // Volver a asignar los eventos
                    document.querySelectorAll('.abrir-chat').forEach(item => {
                        item.addEventListener('click', () => {
                            const receptorId = item.getAttribute('data-receptor-id');
                            const receptorNombre = item.getAttribute('data-receptor-nombre');

                            receptorIdInput.value = receptorId;
                            chatTitulo.textContent = receptorNombre;
                            chatMensajes.innerHTML = '';
                            ultimaFecha = '1970-01-01 00:00:00';

                            cargarMensajes(receptorId);
                            chatModal.classList.remove('hidden');
                        });
                    });
                });
        }

        // Llamada inicial y actualización periódica
        actualizarConversaciones();
        setInterval(actualizarConversaciones, 3000);
    </script>

</body>

</html>