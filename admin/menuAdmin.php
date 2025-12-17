<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM admin WHERE id_admin = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Error al obtener la información del usuario.";
    exit();
}

// Consulta para obtener el total de usuarios registrados
$total_usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch(PDO::FETCH_ASSOC)['total'];

// Consulta para obtener la cantidad de usuarios registrados por día
$usuarios_por_dia = $conn->query("
    SELECT DATE(fecha_registro) AS fecha, COUNT(*) AS cantidad 
    FROM usuarios 
    GROUP BY DATE(fecha_registro)
    ORDER BY fecha ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener el total de publicaciones
$total_publicaciones = $conn->query("SELECT COUNT(*) AS total FROM publicaciones")->fetch(PDO::FETCH_ASSOC)['total'];

// Consulta para obtener la cantidad de publicaciones por día
$publicaciones_por_dia = $conn->query("
    SELECT DATE(fecha_publicacion) AS fecha, COUNT(*) AS cantidad 
    FROM publicaciones 
    GROUP BY DATE(fecha_publicacion)
    ORDER BY fecha ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener el total de mensajes enviados
$total_mensajes = $conn->query("SELECT COUNT(*) AS total FROM mensajes")->fetch(PDO::FETCH_ASSOC)['total'];

// Consulta para obtener la cantidad de mensajes enviados por día
$mensajes_por_dia = $conn->query("
    SELECT DATE(fecha_envio) AS fecha, COUNT(*) AS cantidad 
    FROM mensajes 
    GROUP BY DATE(fecha_envio)
    ORDER BY fecha ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Para gráficos -->
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
        <!-- Usuarios registrados -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800">Usuarios registrados</h2>
            <canvas id="usuariosChart" class="mt-4 h-40"></canvas>
            <p class="text-sm text-gray-600 mt-2">Cantidad de usuarios registrados por fecha.</p>
        </div>

        <!-- Publicaciones -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800">Publicaciones</h2>
            <canvas id="publicacionesChart" class="mt-4 h-40"></canvas>
            <p class="text-sm text-gray-600 mt-2">Cantidad de publicaciones realizadas por fecha.</p>
        </div>

        <!-- Mensajes enviados -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800">Mensajes enviados</h2>
            <canvas id="mensajesChart" class="mt-4 h-40"></canvas>
            <p class="text-sm text-gray-600 mt-2">Cantidad de mensajes enviados por fecha.</p>
        </div>
    </main>

    <!-- Scripts para los gráficos -->
    <script>
        // Gráfico de usuarios registrados
        const usuariosData = <?= json_encode($usuarios_por_dia) ?>;
        const usuariosLabels = usuariosData.map(data => data.fecha);
        const usuariosCantidad = usuariosData.map(data => data.cantidad);

        const usuariosCtx = document.getElementById('usuariosChart').getContext('2d');
        new Chart(usuariosCtx, {
            type: 'line',
            data: {
                labels: usuariosLabels,
                datasets: [{
                    label: 'Usuarios registrados por día',
                    data: usuariosCantidad,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderWidth: 2,
                    fill: true,
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
                            text: 'Cantidad de usuarios'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de publicaciones
        const publicacionesData = <?= json_encode($publicaciones_por_dia) ?>;
        const publicacionesLabels = publicacionesData.map(data => data.fecha);
        const publicacionesCantidad = publicacionesData.map(data => data.cantidad);

        const publicacionesCtx = document.getElementById('publicacionesChart').getContext('2d');
        new Chart(publicacionesCtx, {
            type: 'line',
            data: {
                labels: publicacionesLabels,
                datasets: [{
                    label: 'Publicaciones por día',
                    data: publicacionesCantidad,
                    borderColor: 'rgba(34, 197, 94, 1)',
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderWidth: 2,
                    fill: true,
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
                            text: 'Cantidad de publicaciones'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de mensajes enviados
        const mensajesData = <?= json_encode($mensajes_por_dia) ?>;
        const mensajesLabels = mensajesData.map(data => data.fecha);
        const mensajesCantidad = mensajesData.map(data => data.cantidad);

        const mensajesCtx = document.getElementById('mensajesChart').getContext('2d');
        new Chart(mensajesCtx, {
            type: 'line',
            data: {
                labels: mensajesLabels,
                datasets: [{
                    label: 'Mensajes enviados por día',
                    data: mensajesCantidad,
                    borderColor: 'rgba(234, 179, 8, 1)',
                    backgroundColor: 'rgba(234, 179, 8, 0.2)',
                    borderWidth: 2,
                    fill: true,
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
                            text: 'Cantidad de mensajes'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>