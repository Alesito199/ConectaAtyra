<?php
session_start();
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $lugar_evento = $_POST['lugar_evento'];
    $id_creador = $_SESSION['usuario_id'];
    $estado = 'Pendiente';

    // Manejar la subida de la imagen
    $img_evento = 'uploads/default-event.jpg'; // Imagen predeterminada
    if (isset($_FILES['img_evento']) && $_FILES['img_evento']['error'] === 0) {
        $directorio = "../uploads/eventos/";
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['img_evento']['name'], PATHINFO_EXTENSION));
        $nuevo_nombre = uniqid("evento_", true) . "." . $ext;
        $ruta_final = $directorio . $nuevo_nombre;

        if (move_uploaded_file($_FILES['img_evento']['tmp_name'], $ruta_final)) {
            $img_evento = "uploads/eventos/" . $nuevo_nombre;
        }
    }

    // Insertar el evento en la base de datos
    $stmt = $conn->prepare("
        INSERT INTO eventos (titulo, descripcion, fecha_inicio, fecha_fin, lugar_evento, id_creador, estado, img_evento) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$titulo, $descripcion, $fecha_inicio, $fecha_fin, $lugar_evento, $id_creador, $estado, $img_evento]);

    header('Location: ../eventos.php?creado=1');
    exit;
}
?>