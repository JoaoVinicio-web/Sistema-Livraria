<?php

$host    = 'localhost';
$port    = '3306';
$dbname  = 'biblioteca'; 
$usuario = 'root';     
$senha   = '';            

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $usuario, $senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados no XAMPP: " . $e->getMessage());
}