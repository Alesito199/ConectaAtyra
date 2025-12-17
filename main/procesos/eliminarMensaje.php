<?php
header('Content-Type: application/json');
session_start();
include '../../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id_mensaje = $_POST['id_mensaje'] ?? null;

if (!$id_mensaje) {
    echo json_encode(['success' => false, 'message' => 'ID no válido']);
    exit();
}

// Solo marca como inactivo, no lo borra
$stmt = $conn->prepare("UPDATE mensajes SET estado = 'inactivo' WHERE id_mensaje = ?");
$success = $stmt->execute([$id_mensaje]);

echo json_encode(['success' => $success]);
