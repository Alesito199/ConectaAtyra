<?php
session_start();
include '../../config/db.php';

// Leer el cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$profesiones = $data['profesiones'] ?? [];

if (empty($profesiones)) {
    echo json_encode([]);
    exit;
}

// Construir la consulta con las profesiones seleccionadas
$placeholders = implode(',', array_fill(0, count($profesiones), '?'));
$stmt = $conn->prepare("
    SELECT p.*, u.nombre, u.profesion, u.foto_perfil 
    FROM publicaciones p 
    JOIN usuarios u ON p.id_usuario = u.id_usuario 
    WHERE u.profesion IN ($placeholders)
    ORDER BY p.fecha_publicacion DESC
");
$stmt->execute($profesiones);
$publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($publicaciones);
?>