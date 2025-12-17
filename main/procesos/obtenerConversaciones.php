<?php
session_start();
include '../../config/db.php';

$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("
    SELECT u.id_usuario, u.nombre, u.foto_perfil, m.mensaje, m.fecha_envio
    FROM usuarios u
    JOIN mensajes m ON (u.id_usuario = m.emisor_id OR u.id_usuario = m.receptor_id)
    WHERE (m.emisor_id = :uid OR m.receptor_id = :uid)
    AND u.id_usuario != :uid AND m.estado = 'activo'
    GROUP BY u.id_usuario
    ORDER BY MAX(m.fecha_envio) DESC
");
$stmt->execute(['uid' => $usuario_id]);
$conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($conversaciones);
