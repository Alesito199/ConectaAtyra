<!-- filepath: c:\wamp64\www\Aleli\main\procesos\enviarMensaje.php -->
<?php
session_start();
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emisor_id = $_POST['emisor_id'];
    $receptor_id = $_POST['receptor_id'];
    $mensaje = $_POST['mensaje'];

    $stmt = $conn->prepare("INSERT INTO mensajes (emisor_id, receptor_id, mensaje,estado) VALUES (?, ?, ?,'activo')");
    $stmt->execute([$emisor_id, $receptor_id, $mensaje]);

    echo json_encode(['success' => true]);
    exit();
}
?>