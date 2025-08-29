<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$page_title = "Gerenciamento de computadores";
$current_page = "home";

include 'includes/header.php';
include('./database/db.php');

// Cadastro de computador
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cabinetBrand'])) {

    // 1️⃣ Inserir processador
    $stmtProc = $pdo->prepare("INSERT INTO processador (nome_modelo, geracao, qntd_nucleos, qntd_threads, frequencia, arquitetura, soquete, potencia_termal)
                               VALUES (:nome, :ger, :nuc, :threads, :freq, :arch, :sock, :pot) RETURNING id");
    $stmtProc->execute([
        ':nome' => $_POST['processorModel'],
        ':ger' => $_POST['processorGen'],
        ':nuc' => $_POST['processorCores'],
        ':threads' => $_POST['processorThreads'],
        ':freq' => $_POST['processorFreq'],
        ':arch' => $_POST['processorArch'], 
        ':sock' => $_POST['processorSocket'],
        ':pot' => $_POST['processorTDP']
    ]);
    $processador_id = $stmtProc->fetchColumn();

    // 2️⃣ Inserir memória
    $stmtMem = $pdo->prepare("INSERT INTO memoria (nome_modelo, geracao, qntd) VALUES (:nome, :ger, :qnt) RETURNING id");
    $stmtMem->execute([
        ':nome' => $_POST['ramModel'],
        ':ger' => $_POST['ramGen'],
        ':qnt' => $_POST['ramQty']
    ]);
    $memoria_id = $stmtMem->fetchColumn();

    // 3️⃣ Inserir armazenamento
    $stmtArma = $pdo->prepare("INSERT INTO armazenamento (nome_modelo, qntd, tipo) VALUES (:nome, :qnt, :tipo) RETURNING id");
    $stmtArma->execute([
        ':nome' => $_POST['storageModel'],
        ':qnt' => $_POST['storageQty'],
        ':tipo' => $_POST['storageType']
    ]);
    $armazenamento_id = $stmtArma->fetchColumn();

    // 4️⃣ Inserir histórico
    $stmtHist = $pdo->prepare("INSERT INTO historico (data_aquisicao, preco, despesas) VALUES (:data, :preco, 0) RETURNING id");
    $stmtHist->execute([
        ':data' => $_POST['acquisitionDate'],
        ':preco' => $_POST['initialPrice']
    ]);
    $historico_id = $stmtHist->fetchColumn();

    // 5️⃣ Inserir patrimônio
    $stmtPat = $pdo->prepare("INSERT INTO patrimonio (setor, patrimonio) VALUES (:setor, :patrimonio) RETURNING id");
    $stmtPat->execute([
        ':setor' => $_POST['sector'],
        ':patrimonio' => $_POST['asset']
    ]);
    $patrimonio_id = $stmtPat->fetchColumn();

    // 6️⃣ Inserir computador
    $stmtComp = $pdo->prepare("INSERT INTO computador (processador_id, memoria_id, armazenamento_id, patrimonio_id, historico_id)
                               VALUES (:proc, :mem, :arma, :pat, :hist)");
    $stmtComp->execute([
        ':proc' => $processador_id,
        ':mem' => $memoria_id,
        ':arma' => $armazenamento_id,
        ':pat' => $patrimonio_id,
        ':hist' => $historico_id
    ]);
}

// Estatísticas do Dashboard
$totalComputers = $pdo->query("SELECT COUNT(*) FROM computador")->fetchColumn();
$totalValue = $pdo->query("SELECT COALESCE(SUM(preco),0) FROM historico")->fetchColumn();
$totalSectors = $pdo->query("SELECT COUNT(*) FROM patrimonio")->fetchColumn();
$monthlyMaintenances = $pdo->query("SELECT COUNT(*) FROM historico WHERE EXTRACT(MONTH FROM data_aquisicao) = EXTRACT(MONTH FROM CURRENT_DATE)")->fetchColumn();

// Lista de últimos computadores
$recentComputers = $pdo->query("SELECT p.patrimonio AS patrimonio, c.id, p.setor
                                 FROM computador c
                                 JOIN patrimonio p ON c.patrimonio_id = p.id
                                 ORDER BY c.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Lista de setores
$sectors = $pdo->query("SELECT DISTINCT setor AS nome, id FROM patrimonio ORDER BY setor")->fetchAll(PDO::FETCH_ASSOC);

// Define o caminho do arquivo


?>


<?php
// index.php
// Aqui você poderia incluir conexão com o banco de dados
// include 'db.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="app">
     
       

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Tab -->
            <div id="dashboard" class="tab-content active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">💻</div>
                        <div class="stat-info">
                            <h3>Total de Computadores</h3>
                            <p class="stat-number" id="totalComputers">
                                <?php 
                                // Aqui você poderia buscar do banco
                                // echo $totalComputers; 
                                echo 0; 
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-info">
                            <h3>Valor Total Atual</h3>
                            <p class="stat-number" id="totalValue">
                                <?php 
                                // echo "R$ " . number_format($totalValue, 2, ',', '.'); 
                                echo "R$ 0,00"; 
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🏢</div>
                        <div class="stat-info">
                            <h3>Total de Setores</h3>
                            <p class="stat-number" id="totalSectors">
                                <?php echo 0; ?>
                            </p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🔧</div>
                        <div class="stat-info">
                            <h3>Manutenções este Mês</h3>
                            <p class="stat-number" id="monthlyMaintenances">
                                <?php echo 0; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="recent-section">
                    <h2>Últimos Computadores Cadastrados</h2>
                    <div id="recentComputers" class="recent-list">
                        <?php
                        // Exemplo de como listar dinamicamente computadores
                        /*
                        foreach($recentComputers as $computer) {
                            echo "<div class='recent-item'>{$computer['name']}</div>";
                        }
                        */
                        ?>
                    </div>
                </div>
            </div>

            <!-- Computers Tab -->
            
            <!-- Sectors Tab -->
            <div id="sectors" class="tab-content">
                <div class="section-header">
                    <h2>Árvore Industrial - Setores</h2>
                    <button class="btn-primary" onclick="openSectorModal()">+ Novo Setor</button>
                </div>
                <div id="sectorsTree" class="sectors-tree">
                    <?php
                    // Aqui poderia montar a árvore de setores dinamicamente
                    ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals e scripts continuam iguais -->
    <script src="scripts.js"></script>
</body>
</html>
