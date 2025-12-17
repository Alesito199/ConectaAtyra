<?php
header('Content-Type: application/json');
session_start();
include '../../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_mensaje = $_POST['id_mensaje'] ?? null;
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$id_mensaje || $mensaje === '') {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Solo el emisor puede editar su mensaje
$stmt = $conn->prepare("UPDATE mensajes SET mensaje = ? WHERE id_mensaje = ? AND emisor_id = ?");
$ok = $stmt->execute([$mensaje, $id_mensaje, $_SESSION['usuario_id']]);

echo json_encode(['success' => $ok]);
