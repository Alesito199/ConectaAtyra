<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Consulta para contar usuarios por estado
$usuarios_por_estado = $conn->query("
    SELECT estado, COUNT(*) AS cantidad
    FROM usuarios
    GROUP BY estado
")->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los usuarios y el total de publicaciones de cada uno
$usuarios = $conn->query("
    SELECT 
        u.id_usuario, 
        u.nombre, 
        u.email, 
        u.profesion, 
        COUNT(p.id_publicacion) AS total_publicaciones,
        estado
    FROM usuarios u
    LEFT JOIN publicaciones p ON u.id_usuario = p.id_usuario
    GROUP BY u.id_usuario
    ORDER BY u.nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener la cantidad de usuarios registrados por día
$usuarios_por_dia = $conn->query("
    SELECT DATE(fecha_registro) AS fecha, COUNT(*) AS cantidad 
    FROM usuarios 
    GROUP BY DATE(fecha_registro)
    ORDER BY fecha ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../uploads/logoConectaAtyra.png" type="image/png">
    <title>Usuarios - Panel de Administración</title>
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
    <main class="max-w-7xl mx-auto mt-6 space-y-8">
        <!-- Tabla de usuarios -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Usuarios registrados</h2>
            <!-- Resumen de usuarios por estado -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($usuarios_por_estado as $estado): ?>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 
            <?= $estado['estado'] === 'activo' ? 'border-green-500' : ($estado['estado'] === 'inactivo' ? 'border-red-500' : 'border-gray-400') ?>">
                        <h3 class="text-lg font-semibold text-gray-700"><?= ucfirst($estado['estado']) ?></h3>
                        <p class="text-2xl font-bold text-gray-900"><?= $estado['cantidad'] ?> usuario<?= $estado['cantidad'] > 1 ? 's' : '' ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <br>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 rounded-lg shadow-sm">
                    <thead class="bg-blue-500 text-white">
                        <tr>
                            <th class="border border-gray-300 p-4 text-left">Nombre</th>
                            <th class="border border-gray-300 p-4 text-left">Correo</th>
                            <th class="border border-gray-300 p-4 text-left">Profesión</th>
                            <th class="border border-gray-300 p-4 text-center">Total de publicaciones</th>
                            <th class="border border-gray-300 p-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="hover:bg-gray-100 transition">
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($usuario['email']) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($usuario['profesion']) ?></td>
                                <td class="border border-gray-300 p-4 text-center font-semibold text-blue-600"><?= $usuario['total_publicaciones'] ?></td>
                                <td class="border border-gray-300 p-4 text-center">
                                    <span class="<?= $usuario['estado'] === 'activo' ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold' ?>">
                                        <?= ucfirst($usuario['estado']) ?>
                                    </span>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Gráfico de barras -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Usuarios registrados por día</h2>
                <canvas id="usuariosBarChart" class="mt-4 h-40"></canvas>
                <p class="text-sm text-gray-600 mt-2">Cantidad de usuarios registrados por fecha en formato de barras.</p>
            </div>

            <!-- Gráfico de líneas -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Usuarios registrados por día</h2>
                <canvas id="usuariosLineChart" class="mt-4 h-40"></canvas>
                <p class="text-sm text-gray-600 mt-2">Cantidad de usuarios registrados por fecha en formato de líneas.</p>
            </div>
        </div>
    </main>

    <!-- Scripts para los gráficos -->
    <script>
        const usuariosData = <?= json_encode($usuarios_por_dia) ?>;

        const labels = usuariosData.map(data => data.fecha);
        const data = usuariosData.map(data => data.cantidad);

        // Gráfico de barras
        const barCtx = document.getElementById('usuariosBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Usuarios registrados',
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
                            text: 'Cantidad de usuarios'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de líneas
        const lineCtx = document.getElementById('usuariosLineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Usuarios registrados',
                    data: data,
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
    </script>
</body>

</html>