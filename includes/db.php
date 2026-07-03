<?php
// Auto-detectar entorno
$is_localhost = in_array(
    explode(":", $_SERVER["HTTP_HOST"] ?? "")[0],
    ["localhost", "127.0.0.1", "::1"]
) || PHP_SAPI === "cli";

if ($is_localhost) {
    $host = "localhost"; $port = "3307";
    $db = "lacatoli_noticias_db"; $user = "root"; $pass = "";
} else {
    $host = "localhost"; $port = "";
    $db = "lacatoli_noticias_db"; $user = "lacatoli_admin_user"; $pass = "Ek,KCa#A,NCgHt%1";
}
$charset = "utf8mb4";
$dsn = "mysql:host=$host;" . ($port ? "port=$port;" : "") . "dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];
try { $pdo = new PDO($dsn, $user, $pass, $options); }
catch (\PDOException $e) { die("Error BD: " . $e->getMessage()); }
