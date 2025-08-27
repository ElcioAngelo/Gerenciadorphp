<?php
    // Require do composer, necessário para extensões.
    require __DIR__ . '/vendor/autoload.php';

    use Dotenv\Dotenv;
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Conexão do banco de dados.
    $host = $_ENV['DATABASE_HOST'];
    $port = $_ENV['DATABASE_PORT'];
    $dbname = $_ENV['DATABASE_NAME'];
    $user = $_ENV['DATABASE_USER'];
    $password = $_ENV['DATABASE_PASSWORD'];

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;options='-c search_path=public'", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
?>