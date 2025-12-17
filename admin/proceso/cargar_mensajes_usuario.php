<?php
header('Content-Type: application/json');
include '../../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        u.nombre AS receptor,
        m.mensaje,
        m.estado,
        m.fecha_envio
    FROM mensajes m
    INNER JOIN usuarios u ON u.id_usuario = m.receptor_id
    WHERE m.emisor_id = ?
    ORDER BY m.fecha_envio DESC
");
$stmt->execute([$id]);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($mensajes);
