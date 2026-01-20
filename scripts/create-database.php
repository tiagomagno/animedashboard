<?php
// Script para criar o banco de dados MySQL
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS animedashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "✓ Banco de dados 'animedashboard' criado com sucesso!\n";
} catch (PDOException $e) {
    echo "✗ Erro ao criar banco de dados: " . $e->getMessage() . "\n";
    exit(1);
}
