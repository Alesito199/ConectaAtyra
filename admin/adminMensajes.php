<?php
session_start();
include '../config/db.php'; // Archivo de conexión a la base de datos
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Consulta para obtener los usuarios que han intercambiado mensajes, la cantidad de mensajes y la fecha del último mensaje
$mensajes = $conn->query("
    SELECT 
    u1.id_usuario AS id_usuario1,
    u1.nombre AS usuario1, 
    u2.nombre AS usuario2, 
    COUNT(CASE WHEN m.estado = 'activo' THEN 1 END) AS mensajes_activos,
    COUNT(CASE WHEN m.estado = 'inactivo' THEN 1 END) AS mensajes_inactivos,
    MAX(m.fecha_envio) AS ultimo_mensaje
FROM mensajes m
INNER JOIN usuarios u1 ON m.emisor_id = u1.id_usuario
INNER JOIN usuarios u2 ON m.receptor_id = u2.id_usuario
GROUP BY u1.id_usuario, u2.id_usuario
ORDER BY ultimo_mensaje DESC

")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Mensajes - Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <main class="max-w-7xl mx-auto mt-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Mensajes entre usuarios</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 rounded-lg shadow-sm">
                    <thead class="bg-gray-500 text-white">
                        <tr>
                            <th class="border border-gray-300 p-4 text-left">Usuario 1</th>
                            <th class="border border-gray-300 p-4 text-left">Usuario 2</th>
                            <th class="border border-gray-300 p-4 text-center">Mensajes Enviados</th>
                            <th class="border border-gray-300 p-4 text-center">Mensajes Eliminados</th>
                            <th class="border border-gray-300 p-4 text-center">Último mensaje</th>
                            <th class="border border-gray-300 p-4 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensajes as $mensaje): ?>
                            <tr class="hover:bg-gray-100 transition">
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($mensaje['usuario1']) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($mensaje['usuario2']) ?></td>
                                <td class="border border-gray-300 p-4 text-center font-semibold text-green-600"><?= $mensaje['mensajes_activos'] ?></td>
                                <td class="border border-gray-300 p-4 text-center font-semibold text-red-600"><?= $mensaje['mensajes_inactivos'] ?></td>
                                <td class="border border-gray-300 p-4 text-center text-gray-600"><?= date('d/m/Y H:i', strtotime($mensaje['ultimo_mensaje'])) ?></td>
                                <td class="border border-gray-300 p-4 text-center">
                                    <button
                                        onclick="abrirModalMensajes(<?= $mensaje['id_usuario1'] ?>, '<?= htmlspecialchars($mensaje['usuario1']) ?>', '<?= htmlspecialchars($mensaje['usuario2']) ?>')"
                                        class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        Ver mensajes
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div id="modal-mensajes" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                    <div class="bg-white w-full max-w-3xl max-h-[80vh] p-6 rounded-lg shadow-lg relative overflow-y-auto">
                        <button onclick="cerrarModalMensajes()" class="absolute top-2 right-3 text-2xl text-gray-600 hover:text-black">&times;</button>
                        <h2 class="text-2xl font-bold mb-4" id="modal-titulo">Mensajes</h2>
                        <div id="modal-contenido" class="text-sm space-y-2">
                            <!-- Contenido dinámico -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function abrirModalMensajes(idUsuario, nombre1, nombre2) {
            document.getElementById('modal-titulo').textContent = `Mensajes de ${nombre1} a ${nombre2}`;
            document.getElementById('modal-contenido').innerHTML = '<p class="text-gray-500">Cargando...</p>';
            document.getElementById('modal-mensajes').classList.remove('hidden');

            fetch(`proceso/cargar_mensajes_usuario.php?id=${idUsuario}`)
                .then(res => res.json())
                .then(data => {
                    const contenedor = document.getElementById('modal-contenido');
                    contenedor.innerHTML = '';

                    if (data.length === 0) {
                        contenedor.innerHTML = '<p class="text-gray-500">No hay mensajes.</p>';
                        return;
                    }

                    data.forEach(msg => {
                        const estadoColor = msg.estado === 'inactivo' ? 'text-red-500' : 'text-green-600';

                        contenedor.innerHTML += `
        <div class="border-b border-gray-200 py-2">
            <p class="font-semibold"><span class="text-gray-700">Para:</span> ${msg.receptor}</p>
            <p class="text-gray-800"><span class="font-semibold">Mensaje:</span> ${msg.mensaje}</p>
            <p><span class="font-semibold">Estado:</span> <span class="${estadoColor}">${msg.estado}</span></p>
            <p class="text-xs text-gray-500">${new Date(msg.fecha_envio).toLocaleString()}</p>
        </div>
    `;
                    });

                });
        }

        function cerrarModalMensajes() {
            document.getElementById('modal-mensajes').classList.add('hidden');
        }
    </script>

</body>

</html>