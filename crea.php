<?php
include 'config/db.php';

$nombre = "Administrador";
$email = "admin@example.com";
$contrasena = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (nombre, email, contrasena) VALUES (?, ?, ?)");
$stmt->execute([$nombre, $email, $contrasena]);

echo "Administrador creado con éxito.";
?>