<?php
var_dump($_FILES);
session_start();
include '../../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$contenido = $_POST['contenido'] ?? '';
$imagen_url = null;

// Validar si el contenido está vacío
if (empty($contenido) && empty($_FILES['imagen']['name'])) {
    die("El contenido o la imagen son obligatorios.");
}

// Subida de imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $directorio_subida = __DIR__ . '/../uploads/publicaciones/';

    // Crear el directorio si no existe
    if (!file_exists($directorio_subida)) {
        if (!mkdir($directorio_subida, 0777, true)) {
            die("Error al crear el directorio de subida.");
        }
    }

    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($ext, $permitidos)) {
        $nombre_unico = uniqid('img_', true) . '.' . $ext;
        $ruta_final = $directorio_subida . $nombre_unico;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_final)) {
            // Ruta relativa para guardar en la base de datos
            $imagen_url = 'uploads/publicaciones/' . $nombre_unico;
        } else {
            die("Error al mover el archivo subido.");
        }
    } else {
        die("Formato de archivo no permitido. Solo se permiten JPG, JPEG, PNG y GIF.");
    }
}

// Guardar en la base de datos
$stmt = $conn->prepare("INSERT INTO publicaciones (id_usuario, contenido, imagen) VALUES (?, ?, ?)");
if ($stmt->execute([$id_usuario, $contenido, $imagen_url])) {
    header("Location: ../menu.php");
    exit();
} else {
    die("Error al guardar la publicación en la base de datos.");
}
?>