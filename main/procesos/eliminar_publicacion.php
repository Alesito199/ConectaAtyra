<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$id_publicacion = $_POST['id_publicacion'] ?? null;

// Verifica si la publicación es del usuario
$stmt = $conn->prepare("SELECT imagen FROM publicaciones WHERE id_publicacion = ? AND id_usuario = ?");
$stmt->execute([$id_publicacion, $id_usuario]);
$publicacion = $stmt->fetch();

if ($publicacion) {
    // Eliminar imagen si existe
    if (!empty($publicacion['imagen']) && file_exists('../../' . $publicacion['imagen'])) {
        unlink('../../' . $publicacion['imagen']);
    }

    // Eliminar publicación
    $delete = $conn->prepare("DELETE FROM publicaciones WHERE id_publicacion = ? AND id_usuario = ?");
    $delete->execute([$id_publicacion, $id_usuario]);
}

header("Location: ../publicacion.php");
exit();
