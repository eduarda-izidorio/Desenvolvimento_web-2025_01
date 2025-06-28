<?php

$host = 'localhost'; // Seu host MySQL (geralmente localhost)
$dbname = 'academia_luta'; // O nome do banco de dados que você vai criar
$user = 'root'; // Seu nome de usuário do MySQL
$password = ''; // Sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

?>