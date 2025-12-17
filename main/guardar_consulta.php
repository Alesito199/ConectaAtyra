<?php
session_start();
include '../config/db.php'; 

header('Content-Type: application/json');

// Validar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No logueado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = $_POST['mensaje'] ?? '';
$asunto = 'Consulta general';

if (empty($mensaje)) {
    echo json_encode(['success' => false, 'error' => 'Mensaje vacío']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO consultas (usuario_id, asunto, mensaje) VALUES (?, ?, ?)");
    $stmt->execute([$usuario_id, $asunto, $mensaje]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
