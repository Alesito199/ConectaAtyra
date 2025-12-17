
<?php
session_start();
include '../config/db.php';

$emisor_id = $_SESSION['usuario_id'];
$receptor_id = $_GET['receptor_id'];

// Obtener información del receptor
$stmt_usuario = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt_usuario->execute([$receptor_id]);
$receptor = $stmt_usuario->fetch();

// Obtener historial de mensajes
$stmt_mensajes = $conn->prepare("
    SELECT * FROM mensajes
    WHERE (emisor_id = ? AND receptor_id = ?)
       OR (emisor_id = ? AND receptor_id = ?)
    ORDER BY fecha_envio ASC
");
$stmt_mensajes->execute([$emisor_id, $receptor_id, $receptor_id, $emisor_id]);
$mensajes = $stmt_mensajes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Chat con <?php echo htmlspecialchars($receptor['nombre']); ?> - ConectaAtyrá</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <main class="max-w-5xl mx-auto mt-8">
        <div class="bg-white p-4 rounded shadow">
            <h1 class="text-xl font-bold text-blue-700 mb-4">Chat con <?php echo htmlspecialchars($receptor['nombre']); ?></h1>
            <div id="chat-mensajes" class="h-96 overflow-y-auto border p-4 bg-gray-50 rounded">
                <?php foreach ($mensajes as $mensaje): ?>
                    <div class="mb-2 <?php echo $mensaje['emisor_id'] == $emisor_id ? 'text-right' : 'text-left'; ?>">
                        <p class="inline-block px-3 py-2 rounded-lg <?php echo $mensaje['emisor_id'] == $emisor_id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'; ?>">
                            <?php echo htmlspecialchars($mensaje['mensaje']); ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <?php echo date('d/m/Y H:i', strtotime($mensaje['fecha_envio'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
            <form id="form-enviar-mensaje" class="flex mt-4">
                <input type="hidden" name="emisor_id" value="<?php echo $emisor_id; ?>">
                <input type="hidden" name="receptor_id" value="<?php echo $receptor_id; ?>">
                <input type="text" name="mensaje" id="mensaje" placeholder="Escribe un mensaje..."
                    class="flex-1 border rounded-l px-2 py-1 focus:outline-none">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded-r hover:bg-blue-700">Enviar</button>
            </form>
        </div>
    </main>

    <script>
        const chatMensajes = document.getElementById('chat-mensajes');
        const formEnviarMensaje = document.getElementById('form-enviar-mensaje');
        const mensajeInput = document.getElementById('mensaje');
        let ultimaFecha = '1970-01-01 00:00:00';

        // Enviar mensaje
        formEnviarMensaje.addEventListener('submit', (e) => {
            e.preventDefault();

            const mensaje = mensajeInput.value.trim();
            if (mensaje === '') return;

            fetch('procesos/enviarMensaje.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(new FormData(formEnviarMensaje))
            }).then(() => {
                mensajeInput.value = '';
                cargarMensajes();
            });
        });

        // Cargar mensajes en tiempo real
        function cargarMensajes() {
            fetch(`procesos/obtenerMensajes.php?emisor_id=<?php echo $emisor_id; ?>&receptor_id=<?php echo $receptor_id; ?>&ultima_fecha=${ultimaFecha}`)
                .then(response => response.json())
                .then(mensajes => {
                    mensajes.forEach(mensaje => {
                        chatMensajes.innerHTML += `
                            <div class="mb-2 ${mensaje.emisor_id == <?php echo $emisor_id; ?> ? 'text-right' : 'text-left'}">
                                <p class="inline-block px-3 py-2 rounded-lg ${mensaje.emisor_id == <?php echo $emisor_id; ?> ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'}">
                                    ${mensaje.mensaje}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    ${new Date(mensaje.fecha_envio).toLocaleString()}
                                </p>
                            </div>
                        `;
                        ultimaFecha = mensaje.fecha_envio;
                    });
                    chatMensajes.scrollTop = chatMensajes.scrollHeight;
                });
        }

        // Actualizar mensajes cada 3 segundos
        setInterval(cargarMensajes, 1000);
    </script>
</body>

</html>