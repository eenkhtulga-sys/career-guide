<?php
// db.php
$host = 'mysql-37dc3805-ikhzasag-2a09.d.aivencloud.com'; //
$db   = 'defaultdb'; // Aiven-ийн үндсэн датабаазын нэр хэвээрээ байна
$user = 'avnadmin';
$pass = 'AVNS_Y46KyuHT9cudJFWcInl'; 
$port = '21272'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Aiven MySQL-д холбогдоход заавал SSL шаардлагатай:
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>