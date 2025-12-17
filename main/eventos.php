<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

// Cambiar el estado de los eventos pasados a "Finalizado"
$conn->query("
    UPDATE eventos 
    SET estado = 'Finalizado' 
    WHERE fecha_fin < NOW() AND estado != 'Finalizado'
");

// Obtener eventos aceptados desde la base de datos
$stmt_eventos = $conn->query("
    SELECT id_evento, titulo, descripcion, fecha_inicio, fecha_fin, img_evento, estado, lugar_evento
    FROM eventos 
    WHERE estado = 'Aceptado'
    ORDER BY fecha_inicio ASC
");
$eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
  <title>Eventos - ConectaAtyrá</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
</head>

<body class="bg-gray-100">

  <!-- HEADER SUPERIOR MEJORADO -->
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

  <script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  </script>
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 grid grid-cols-1 md:grid-cols-4 gap-6">

    <!-- Sidebar -->
    <aside class="bg-white rounded-xl shadow p-6">
      <h2 class="text-xl font-bold mb-4 text-gray-700">Opciones</h2>
      <button id="crear-evento-btn"
        class="w-full bg-blue-500 hover:bg-blue-600 transition duration-300 text-white py-2 rounded-lg font-semibold">
        Crear evento
      </button>
    </aside>

    <div id="crear-evento-modal"
      class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden">
      <div class="bg-white w-full max-w-lg mx-4 rounded-lg shadow-xl p-6 relative">
        <h3 class="text-2xl font-bold mb-4 text-gray-800">Crear evento</h3>
        <form id="crear-evento-form" method="POST" action="procesos/crearEvento.php" enctype="multipart/form-data">
          <?php
          $fields = [
            ['titulo', 'Título', 'text'],
            ['descripcion', 'Descripción', 'textarea'],
            ['fecha_inicio', 'Fecha de inicio', 'datetime-local'],
            ['fecha_fin', 'Fecha de fin', 'datetime-local'],
            ['img_evento', 'Imagen del evento', 'file']
          ];
          foreach ($fields as [$id, $label, $type]):
          ?>
            <div class="mb-4">
              <label for="<?= $id ?>" class="block font-semibold text-gray-700"><?= $label ?></label>
              <?php if ($type === 'textarea'): ?>
                <textarea id="<?= $id ?>" name="<?= $id ?>" required
                  class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400"></textarea>
              <?php else: ?>
                <input type="<?= $type ?>" id="<?= $id ?>" name="<?= $id ?>" <?= $type !== 'file' ? 'required' : '' ?>
                  class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <div class="mb-4">
            <label for="lugar_evento" class="block font-semibold text-gray-700">Lugar del evento</label>
            <input type="text" id="lugar_evento" name="lugar_evento" required
              class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-400">
          </div>

          <div class="flex justify-end">
            <button type="button" id="cerrar-modal-btn"
              class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 hover:bg-gray-700 transition">Cancelar</button>
            <button type="submit"
              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Crear</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      const crearEventoBtn = document.getElementById('crear-evento-btn');
      const crearEventoModal = document.getElementById('crear-evento-modal');
      const cerrarModalBtn = document.getElementById('cerrar-modal-btn');

      crearEventoBtn.addEventListener('click', () => crearEventoModal.classList.remove('hidden'));
      cerrarModalBtn.addEventListener('click', () => crearEventoModal.classList.add('hidden'));
    </script>

    <!-- Sección de eventos -->
    <section class="md:col-span-3 space-y-6">
      <?php foreach ($eventos as $evento): ?>
        <div
          class="bg-white rounded-xl shadow hover:shadow-lg transition duration-300 overflow-hidden flex flex-col md:flex-row">
          <!-- Imagen -->
          <div class="w-full md:w-1/3">
            <img src="<?= htmlspecialchars($evento['img_evento'] ?? 'uploads/default-event.jpg') ?>"
              alt="Imagen del evento" class="h-48 w-full object-cover">
          </div>

          <!-- Detalles -->
          <div class="w-full md:w-2/3 p-4">
            <h3 class="text-lg font-bold text-gray-800">
              <?= htmlspecialchars($evento['titulo']) ?>
            </h3>
            <p class="text-sm text-gray-600 mt-2">
              <?= htmlspecialchars(substr($evento['descripcion'], 0, 100)) ?>...
            </p>
            <p class="text-sm text-gray-500 mt-4">
              <strong class="text-purple-500">Lugar:</strong> <?= htmlspecialchars($evento['lugar_evento']) ?>
            </p>
            <p class="text-sm text-gray-500 mt-4">
              <strong class="text-blue-500">Inicio:</strong> <?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?><br>
            </p>
            <p class="text-lg font-bold text-yellow-500 mt-2">
              Hora de inicio: <?= date('H:i', strtotime($evento['fecha_inicio'])) ?><br>
              Hora de fin: <?= date('H:i', strtotime($evento['fecha_fin'])) ?>
            </p>
            <p class="text-sm text-gray-500 mt-4">
              <strong class="text-red-500">Fin:</strong> <?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?>
            </p>
            <div class="mt-4">
              <?php if ($evento['estado'] === 'Pendiente'): ?>
                <span class="text-yellow-500 font-semibold">Estado: Pendiente</span>
              <?php elseif ($evento['estado'] === 'Aceptado'): ?>
                <span class="text-green-500 font-semibold">Estado: Aceptado</span>
              <?php elseif ($evento['estado'] === 'Finalizado'): ?>
                <span class="text-gray-500 font-semibold">Estado: Finalizado</span>
              <?php else: ?>
                <form method="POST" action="solicitarEvento.php">
                  <input type="hidden" name="id_evento" value="<?= $evento['id_evento'] ?>">
                  <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition duration-300">
                    Solicitar evento
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
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
</body>

</html>