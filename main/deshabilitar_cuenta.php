<?php
session_start();
header('Content-Type: application/json');

include '../config/db.php'; 

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['success' => false, 'error' => 'No logueado']);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
  $stmt = $conn->prepare("UPDATE usuarios SET estado = 'inactivo' WHERE id_usuario = ?");
  $stmt->execute([$usuario_id]);
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>