<?php
include '../../config/db.php';

$emisor_id = $_GET['emisor_id'] ?? '';
$receptor_id = $_GET['receptor_id'] ?? '';

$stmt = $conn->prepare("
    SELECT * FROM mensajes
    WHERE (
        (emisor_id = :emisor AND receptor_id = :receptor) OR 
        (emisor_id = :receptor AND receptor_id = :emisor)
    )
    AND estado = 'activo'
    ORDER BY fecha_envio ASC
");
$stmt->execute([
    ':emisor' => $emisor_id,
    ':receptor' => $receptor_id
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
