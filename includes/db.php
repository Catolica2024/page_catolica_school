<?php
// Configuración de la base de datos
$host = 'localhost';
$db   = 'lacatoli_noticias_db';
$user = 'lacatoli_admin_user';
$pass = 'Ek,KCa#A,NCgHt%1';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // En producción, no mostrar el error detallado
     die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
