<?php
// Configurações do Banco de Dados no XAMPP
$host = 'localhost';
$dbname = 'reservativa_db'; // Nome do banco de dados que criará no phpMyAdmin
$user = 'root';             // Usuário padrão do XAMPP
$pass = '';                 // Senha padrão do XAMPP (geralmente vazia)

try {
    // Instância do PDO com tratamento de UTF-8 e erros ativados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Se o banco não estiver rodando hoje, captura o erro sem travar o código com erros genéricos
    // Amanhã, quando ligar o MySQL no XAMPP, isso se conectará automaticamente.
    $pdo = null;
}