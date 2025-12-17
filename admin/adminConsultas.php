<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Si se envió el formulario para activar cuenta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activar_usuario'])) {
    $idUsuario = $_POST['id_usuario'];

    // Activar la cuenta
    $conn->prepare("UPDATE usuarios SET estado = 'activo' WHERE id_usuario = ?")->execute([$idUsuario]);

    // Marcar la consulta como respondida (opcional)
    $conn->prepare("UPDATE consultas SET estado = 'respondida' WHERE usuario_id = ? AND asunto = 'Activar cuenta'")
        ->execute([$idUsuario]);

    // Redirigir para evitar reenvío por F5
    header("Location: adminConsultas.php?activado=$idUsuario");
    exit;
}

// Consultas recibidas
$consultas = $conn->query("
    SELECT 
        c.id, 
        c.usuario_id, 
        c.asunto, 
        c.mensaje, 
        c.fecha, 
        u.nombre, 
        u.email, 
        u.telefono, 
        u.estado
    FROM consultas c
    LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario
    ORDER BY c.fecha DESC
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
        <?php if (isset($_GET['activado'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                ✅ Cuenta activada correctamente (ID usuario: <?= htmlspecialchars($_GET['activado']) ?>)
            </div>
        <?php endif; ?>

        <section class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">📨 Consultas Recibidas</h2>

            <?php if (empty($consultas)): ?>
                <p class="text-gray-500">No hay consultas registradas.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                        <thead>
                            <tr class="bg-gray-200 text-left">
                                <th class="py-2 px-4">#</th>
                                <th class="py-2 px-4">Fecha</th>
                                <th class="py-2 px-4">Asunto</th>
                                <th class="py-2 px-4">Mensaje</th>
                                <th class="py-2 px-4">Usuario</th>
                                <th class="py-2 px-4">Email</th>
                                <th class="py-2 px-4">Teléfono</th>
                                <th class="py-2 px-4">Estado</th>
                                <th class="py-2 px-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consultas as $index => $consulta): ?>
                                <tr class="border-t border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-4"><?= $index + 1 ?></td>
                                    <td class="py-2 px-4"><?= date('d/m/Y H:i', strtotime($consulta['fecha'])) ?></td>
                                    <td class="py-2 px-4 font-semibold <?= $consulta['asunto'] === 'Activar cuenta' ? 'text-blue-600' : 'text-gray-700' ?>">
                                        <?= htmlspecialchars($consulta['asunto']) ?>
                                    </td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($consulta['mensaje']) ?></td>
                                    <td class="py-2 px-4"><?= $consulta['nombre'] ?? 'No registrado' ?></td>
                                    <td class="py-2 px-4"><?= $consulta['email'] ?? '-' ?></td>
                                    <td class="py-2 px-4"><?= $consulta['telefono'] ?? '-' ?></td>
                                    <td class="py-2 px-4"><?= ucfirst($consulta['estado'] ?? 'Desconocido') ?></td>
                                    <td class="py-2 px-4">
                                        <?php if ($consulta['asunto'] === 'Activar cuenta' && $consulta['estado'] === 'inactivo'): ?>
                                            <form method="post" onsubmit="return confirm('¿Seguro que desea activar esta cuenta?');">
                                                <input type="hidden" name="id_usuario" value="<?= $consulta['usuario_id'] ?>">
                                                <input type="hidden" name="activar_usuario" value="1">
                                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                                    Activar
                                                </button>
                                            </form>
                                        <?php elseif ($consulta['asunto'] === 'Consulta general'): ?>
                                            <div class="flex items-center gap-3">
                                                <!-- Icono de correo -->
                                                <a href="mailto:<?= htmlspecialchars($consulta['email']) ?>" title="Enviar correo">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M16 12H8m0 0l4-4m0 8l-4-4m0 4H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v4" />
                                                    </svg>
                                                </a>
                                                <a href="https://wa.me/<?= preg_replace('/\D/', '', $consulta['telefono']) ?>" target="_blank" title="Contactar por WhatsApp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600 hover:text-green-800" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M20.52 3.48A11.77 11.77 0 0012 0a11.94 11.94 0 00-9.94 18.93L0 24l5.28-1.64A11.94 11.94 0 0012 24c6.63 0 12-5.37 12-12a11.77 11.77 0 00-3.48-8.52zM12 21.4a9.38 9.38 0 01-4.91-1.33l-.35-.21-3.13.97.96-3.05-.23-.38a9.44 9.44 0 1117.2-5.43A9.5 9.5 0 0112 21.4zm5.34-7.61l-1.51-.44a.87.87 0 00-.83.22l-.54.55a6.77 6.77 0 01-3.14-3.14l.54-.54a.88.88 0 00.22-.83l-.44-1.51a.89.89 0 00-.83-.62h-.68a1.78 1.78 0 00-1.77 1.77 8.61 8.61 0 008.61 8.61 1.78 1.78 0 001.77-1.77v-.68a.89.89 0 00-.61-.83z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-500 text-sm">Sin acción</span>
                                        <?php endif; ?>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>



    </main>
</body>

</html>