<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Obtener información del usuario
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

// Manejar la adición de nuevas experiencias
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nueva_experiencia'])) {
    $cargo = $_POST['cargo'];
    $empresa = $_POST['empresa'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'] ?? null;

    $stmt = $conn->prepare("INSERT INTO experiencia (id_usuario, cargo, empresa, descripcion, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_usuario, $cargo, $empresa, $descripcion, $fecha_inicio, $fecha_fin]);

    header("Location: perfil.php?experiencia_agregada=1");
    exit();
}

// Obtener las experiencias laborales del usuario
$stmt_experiencias = $conn->prepare("SELECT * FROM experiencia WHERE id_usuario = ? ORDER BY fecha_inicio DESC");
$stmt_experiencias->execute([$id_usuario]);
$experiencias = $stmt_experiencias->fetchAll(PDO::FETCH_ASSOC);

// Manejar la edición del perfil
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['nueva_experiencia'])) {
    $nombre = $_POST['nombre'];
    $profesion = $_POST['profesion'];
    $telefono = $_POST['telefono'];
    $ciudad = $_POST['ciudad'];
    $bio = $_POST['bio'];
    $foto_perfil = $usuario['foto_perfil'];
    $cv_usuario = $usuario['cv_usuario']; // Mantener el CV actual si no se sube uno nuevo

    // Manejar la subida de la foto de perfil
    if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] === 0) {
        $directorio_fotos = "../uploads/perfiles/";
        if (!file_exists($directorio_fotos)) {
            mkdir($directorio_fotos, 0777, true);
        }

        $ext_foto = strtolower(pathinfo($_FILES['nueva_foto']['name'], PATHINFO_EXTENSION));
        $nuevo_nombre_foto = uniqid("perfil_", true) . "." . $ext_foto;
        $ruta_final_foto = $directorio_fotos . $nuevo_nombre_foto;

        if (move_uploaded_file($_FILES['nueva_foto']['tmp_name'], $ruta_final_foto)) {
            $foto_perfil = "uploads/perfiles/" . $nuevo_nombre_foto;
        }
    }

    // Manejar la subida del CV
    if (isset($_FILES['cv_usuario']) && $_FILES['cv_usuario']['error'] === 0) {
        $directorio_documentos = "../uploads/documentos/";
        if (!file_exists($directorio_documentos)) {
            mkdir($directorio_documentos, 0777, true);
        }

        $ext_cv = strtolower(pathinfo($_FILES['cv_usuario']['name'], PATHINFO_EXTENSION));
        $nuevo_nombre_cv = uniqid("cv_", true) . "." . $ext_cv;
        $ruta_final_cv = $directorio_documentos . $nuevo_nombre_cv;

        if (move_uploaded_file($_FILES['cv_usuario']['tmp_name'], $ruta_final_cv)) {
            $cv_usuario = "uploads/documentos/" . $nuevo_nombre_cv;
        }
    }

    // Actualizar los datos del usuario en la base de datos
    $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, profesion=?, telefono=?, ciudad=?, bio=?, foto_perfil=?, cv_usuario=? WHERE id_usuario=?");
    $stmt->execute([$nombre, $profesion, $telefono, $ciudad, $bio, $foto_perfil, $cv_usuario, $id_usuario]);

    header("Location: perfil.php?actualizado=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Mi Perfil - ConectaAtyrá</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
</head>

<body class="bg-gray-100">

    <!-- NAV -->
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

    <!-- CONTENIDO -->
    <main class="max-w-5xl mx-auto mt-8 space-y-8">

        <!-- SECCIÓN DE EDICIÓN DE PERFIL -->
        <section class="bg-white shadow-lg rounded-xl p-8 mb-10">
            <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center">Editar Perfil</h1>

            <?php if (isset($_GET['actualizado'])): ?>
                <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                    ✅ Perfil actualizado con éxito.
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Foto Perfil -->
                <div class="flex flex-col items-center">
                    <img id="preview-foto" src="../<?php echo htmlspecialchars($usuario['foto_perfil'] ?? 'uploads/default.jpg'); ?>"
                        class="w-32 h-32 rounded-full border-4 border-blue-400 object-cover mb-3">
                    <label class="cursor-pointer text-blue-600 hover:text-blue-700">
                        Cambiar foto
                        <input type="file" name="nueva_foto" id="nueva_foto" accept="image/*" class="hidden">
                    </label>
                </div>

                <!-- Información del perfil -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold">Nombre completo</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block font-semibold">Profesión</label>
                        <input type="text" name="profesion" value="<?php echo htmlspecialchars($usuario['profesion']); ?>"
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block font-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>"
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block font-semibold">Ciudad</label>
                        <input type="text" name="ciudad" value="<?php echo htmlspecialchars($usuario['ciudad']); ?>"
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <!-- Biografía -->
                <div>
                    <label class="block font-semibold">Biografía</label>
                    <textarea name="bio" rows="3"
                        class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400"><?php echo htmlspecialchars($usuario['bio'] ?? ''); ?></textarea>
                </div>

                <!-- Subida de CV -->
                <div>
                    <label class="block font-semibold">Subir CV (PDF, DOC, DOCX)</label>
                    <input type="file" name="cv_usuario" id="cv_usuario" accept=".pdf,.doc,.docx"
                        class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <?php if (!empty($usuario['cv_usuario'])): ?>
                    <div class="mt-4">
                        <p class="font-semibold">CV Actual:</p>
                        <iframe src="../<?php echo htmlspecialchars($usuario['cv_usuario']); ?>" class="w-full h-72 border rounded mt-2"></iframe>
                        <a href="../<?php echo htmlspecialchars($usuario['cv_usuario']); ?>" target="_blank"
                            class="text-blue-600 hover:underline mt-2 inline-block">Descargar CV</a>
                    </div>
                <?php endif; ?>

                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    Guardar cambios
                </button>
            </form>

        </section>

        <!-- EXPERIENCIA LABORAL MEJORADA -->
        <section class="bg-white shadow-lg rounded-xl p-8">
            <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center">Experiencia Laboral</h1>

            <form method="POST" class="space-y-5">
                <input type="hidden" name="nueva_experiencia" value="1">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold">Cargo</label>
                        <input type="text" name="cargo" required
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block font-semibold">Empresa</label>
                        <input type="text" name="empresa" required
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold">Descripción</label>
                    <textarea name="descripcion" rows="3"
                        class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" required
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block font-semibold">Fecha fin</label>
                        <input type="date" name="fecha_fin"
                            class="mt-1 p-2 border rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    Agregar
                </button>
            </form>

            <!-- Experiencias -->
            <div class="mt-8">
                <h2 class="text-xl font-bold">Mis Experiencias</h2>
                <ul class="mt-4 space-y-4">
                    <?php foreach ($experiencias as $exp): ?>
                        <li class="p-4 bg-gray-50 rounded shadow-sm">
                            <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($exp['cargo']); ?></h3>
                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($exp['empresa']); ?></p>
                            <p class="text-gray-500 text-xs mt-2"><?php echo htmlspecialchars($exp['descripcion']); ?></p>
                            <span class="text-xs text-gray-400">
                                <?php echo date('d/m/Y', strtotime($exp['fecha_inicio'])); ?> -
                                <?php echo $exp['fecha_fin'] ? date('d/m/Y', strtotime($exp['fecha_fin'])) : 'Presente'; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
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
    <!-- PREVISUALIZAR FOTO -->
    <script>
        const fileInput = document.getElementById('nueva_foto');
        const previewImage = document.getElementById('preview-foto');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <!-- SweetAlert para mensajes -->
    <script>
        <?php if (isset($_GET['actualizado'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Perfil actualizado',
                text: 'Los cambios se han guardado correctamente.',
                confirmButtonText: 'Aceptar'
            });
        <?php endif; ?>

        <?php if (isset($_GET['experiencia_agregada'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Experiencia agregada',
                text: 'La experiencia laboral se ha agregado correctamente.',
                confirmButtonText: 'Aceptar'
            });
        <?php endif; ?>
    </script>
    <script>
        // Previsualización de imagen
        document.getElementById('nueva_foto').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-foto').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>