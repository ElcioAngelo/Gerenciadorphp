<?php
// Caminho para o autoload do Composer
require __DIR__ . '/../vendor/autoload.php';

// Carrega as variáveis de ambiente do arquivo .env na raiz do projeto
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Lê as variáveis do .env
$dbHost     = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbPort     = $_ENV['DB_PORT'] ?? '3306';
$dbName     = $_ENV['DB_DATABASE'] ?? 'gerenciamento';
$dbUser     = $_ENV['DB_USERNAME'] ?? 'root';
$dbPassword = $_ENV['DB_PASSWORD'] ?? '';

try {
    // Cria a conexão PDO com MariaDB/MySQL
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPassword);

    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conexão com MariaDB realizada com sucesso!";
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage();
}
