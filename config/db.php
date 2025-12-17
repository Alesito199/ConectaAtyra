<?php
$host = getenv('DB_HOST') ?: 'localhost'; // Asegúrate de que coincida con el nombre del servicio en docker-compose.yml
$db = getenv('DB_NAME') ?: 'atyra_conecta'; // Nombre de la base de datos en docker-compose.yml
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: ''; // Contraseña configurada en docker-compose.yml

try {
    // Creando una nueva conexión PDO
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);

    // Estableciendo el modo de error PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Manejo de la excepción en caso de error en la conexión
    die("Error de conexión: " . $e->getMessage());
}
?>