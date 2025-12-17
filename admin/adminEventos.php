<?php
session_start();
include '../config/db.php'; // Archivo de conexión a la base de datos

// Consulta para obtener los eventos
$eventos = $conn->query("
    SELECT id_evento, titulo, descripcion, fecha_inicio, fecha_fin, img_evento, estado, lugar_evento
    FROM eventos
    ORDER BY fecha_inicio ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los eventos realizados (finalizados) por fecha
$eventos_realizados = $conn->query("
    SELECT DATE(fecha_fin) AS fecha, COUNT(*) AS cantidad
    FROM eventos
    WHERE estado = 'Finalizado'
    GROUP BY DATE(fecha_fin)
    ORDER BY fecha ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Actualizar el estado del evento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_evento = $_POST['id_evento'];
    $nuevo_estado = $_POST['estado'];

    $stmt = $conn->prepare("UPDATE eventos SET estado = ? WHERE id_evento = ?");
    $stmt->execute([$nuevo_estado, $id_evento]);

    header("Location: adminEventos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Eventos - Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Menú principal -->
    <nav class="bg-gray-800 text-white shadow">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <h1 class="text-2xl font-bold">Admin<span class="text-blue-400">Panel</span></h1>
                <div class="hidden md:flex gap-6">
                    <a href="menuAdmin.php" class="hover:text-blue-300">Inicio</a>
                    <a href="adminUsuarios.php" class="hover:text-blue-300">Usuarios</a>
                    <a href="adminEventos.php" class="hover:text-blue-300">Eventos</a>
                    <a href="adminMensajes.php" class="hover:text-blue-300">Mensajes</a>
                    <a href="adminConsultas.php" class="hover:text-blue-300">Consultas</a>
                    <a href="../config/funciones/logout.php" class="hover:text-red-400">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="max-w-7xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tabla de eventos -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Eventos pendientes</h2>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 text-left">Título</th>
                        <th class="border border-gray-300 p-2 text-left">Lugar</th>
                        <th class="border border-gray-300 p-2 text-left">Fecha de inicio</th>
                        <th class="border border-gray-300 p-2 text-center">Estado</th>
                        <th class="border border-gray-300 p-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $evento): ?>
                        <tr>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($evento['titulo']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($evento['lugar_evento']) ?></td>
                            <td class="border border-gray-300 p-2"><?= date('d/m/Y H:i', strtotime($evento['fecha_inicio'])) ?></td>
                            <td class="border border-gray-300 p-2 text-center">
                                <span class="
                                    px-2 py-1 rounded-lg text-black 
                                    <?= $evento['estado'] === 'Aceptado' ? 'bg-green-500' : '' ?>
                                    <?= $evento['estado'] === 'Rechazado' ? 'bg-red-500' : '' ?>
                                    <?= $evento['estado'] === 'Pendiente' ? 'bg-yellow-500' : '' ?>
                                ">
                                    <?= htmlspecialchars($evento['estado']) ?>
                                </span>
                            </td>
                            <td class="border border-gray-300 p-2 text-center">
                                <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"
                                        onclick="abrirModal(<?= htmlspecialchars(json_encode($evento)) ?>)">
                                    Ver detalles
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Gráfico de eventos realizados -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Eventos realizados</h2>
            <canvas id="eventosChart" class="mt-4"></canvas>
        </div>
    </main>

    <!-- Modal de detalles del evento -->
    <div id="modal-evento" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden">
        <div class="bg-white w-full max-w-lg mx-4 rounded-lg shadow-xl p-6 relative">
            <h3 class="text-2xl font-bold mb-4 text-gray-800" id="modal-titulo"></h3>
            <img id="modal-imagen" src="" alt="Imagen del evento" class="w-full h-48 object-cover rounded-lg mb-4">
            <p class="text-gray-600 mb-2" id="modal-descripcion"></p>
            <p class="text-sm text-gray-500 mb-2"><strong>Lugar:</strong> <span id="modal-lugar"></span></p>
            <p class="text-sm text-gray-500 mb-2"><strong>Fecha de inicio:</strong> <span id="modal-fecha-inicio"></span></p>
            <p class="text-sm text-gray-500 mb-4"><strong>Fecha de fin:</strong> <span id="modal-fecha-fin"></span></p>
            <div class="flex justify-end" id="modal-acciones">
                <!-- Los botones se agregarán dinámicamente según el estado -->
            </div>
        </div>
    </div>

    <!-- Script para manejar el modal -->
    <script>
        function abrirModal(evento) {
            document.getElementById('modal-titulo').textContent = evento.titulo;
            document.getElementById('modal-imagen').src = evento.img_evento ? '../main/' + evento.img_evento : '../uploads/default-event.jpg';
            document.getElementById('modal-descripcion').textContent = evento.descripcion;
            document.getElementById('modal-lugar').textContent = evento.lugar_evento;
            document.getElementById('modal-fecha-inicio').textContent = new Date(evento.fecha_inicio).toLocaleString();
            document.getElementById('modal-fecha-fin').textContent = new Date(evento.fecha_fin).toLocaleString();

            const acciones = document.getElementById('modal-acciones');
            acciones.innerHTML = ''; // Limpiar acciones previas

            if (evento.estado === 'Pendiente') {
                acciones.innerHTML = `
                    <form method="POST" class="mr-2">
                        <input type="hidden" name="id_evento" value="${evento.id_evento}">
                        <input type="hidden" name="estado" value="Aceptado">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            Aceptar
                        </button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="id_evento" value="${evento.id_evento}">
                        <input type="hidden" name="estado" value="Rechazado">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            Rechazar
                        </button>
                    </form>
                `;
            }

            document.getElementById('modal-evento').classList.remove('hidden');
        }

        document.addEventListener('click', (e) => {
            if (e.target.id === 'modal-evento') {
                document.getElementById('modal-evento').classList.add('hidden');
            }
        });
    </script>

    <!-- Script para el gráfico -->
    <script>
        const ctx = document.getElementById('eventosChart').getContext('2d');
        const eventosData = <?= json_encode($eventos_realizados) ?>;

        const labels = eventosData.map(data => data.fecha);
        const data = eventosData.map(data => data.cantidad);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Eventos realizados',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Cantidad de eventos'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>