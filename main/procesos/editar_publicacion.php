<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$id_publicacion = $_POST['id_publicacion'] ?? '';
$contenido = $_POST['contenido'] ?? '';

// Verificar si existe y es del usuario
$stmt = $conn->prepare("SELECT imagen FROM publicaciones WHERE id_publicacion = ? AND id_usuario = ?");
$stmt->execute([$id_publicacion, $id_usuario]);
$pub = $stmt->fetch();

if (!$pub) {
    echo json_encode(['success' => false, 'message' => 'Publicación no encontrada']);
    exit();
}

$nueva_imagen = $pub['imagen'];

// Si se sube nueva imagen
if (!empty($_FILES['nueva_imagen']['name'])) {
    $ext = pathinfo($_FILES['nueva_imagen']['name'], PATHINFO_EXTENSION);
    $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array(strtolower($ext), $permitidos)) {
        $nombreArchivo = uniqid('img_', true) . '.' . $ext;
        $rutaFisica = __DIR__ . '/../uploads/publicaciones/' . $nombreArchivo;
        $rutaBD = 'uploads/publicaciones/' . $nombreArchivo;

        if (move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $rutaFisica)) {
            // Eliminar imagen anterior si existe
            if (!empty($pub['imagen']) && file_exists(__DIR__ . '/../' . $pub['imagen'])) {
                unlink(__DIR__ . '/../' . $pub['imagen']);
            }
            $nueva_imagen = $rutaBD; // Guardar solo ruta relativa en la base de datos
        }
    }
}


// Actualizar
$update = $conn->prepare("UPDATE publicaciones SET contenido = ?, imagen = ? WHERE id_publicacion = ? AND id_usuario = ?");
$update->execute([$contenido, $nueva_imagen, $id_publicacion, $id_usuario]);

echo json_encode(['success' => true]);
