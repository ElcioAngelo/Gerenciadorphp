<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$page_title = "Gerenciamento de computadores";
$current_page = "home";

include 'includes/header.php';

// Conexão com o banco de dados.
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


<link rel="stylesheet" href="style.css">
<body>
    
    <div class="app">
        

        <main class="main-content">
            <!-- Dashboard -->
            <div id="dashboard" class="tab-content active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">💻</div>
                        <div class="stat-info">
                            <h3>Total de Computadores</h3>
                            <p class="stat-number"><?= $totalComputers ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-info">
                            <h3>Valor Total Atual</h3>
                            <p class="stat-number">R$ <?= number_format($totalValue, 2, ',', '.') ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🏢</div>
                        <div class="stat-info">
                            <h3>Total de Setores</h3>
                            <p class="stat-number"><?= $totalSectors ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🔧</div>
                        <div class="stat-info">
                            <h3>Manutenções este Mês</h3>
                            <p class="stat-number"><?= $monthlyMaintenances ?></p>
                        </div>
                    </div>
                </div>
                <div class="recent-section">
                    <h2>Últimos Computadores Cadastrados</h2>
                    <div id="recentComputers" class="recent-list">
                        <?php foreach ($recentComputers as $pc): ?>
                            <div><?= htmlspecialchars($pc['setor'])?> - <?= htmlspecialchars($pc['patrimonio']) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Computers Tab -->
            <div id="computers" class="tab-content">
                <div class="section-header">
                    <h2>Gerenciamento de Computadores</h2>
                    <button class="btn-primary" onclick="document.getElementById('computerModal').style.display='block'">+ Novo Computador</button>
                </div>
                <div id="computersList" class="computers-grid">
                    <?php
                    $computers = $pdo->query("SELECT * FROM Computador")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($computers as $c) {
                        echo "<div> - {$c['patrimonio']}</div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Sectors Tab -->
            <div id="sectors" class="tab-content">
                <div class="section-header">
                    <h2>Setores</h2>
                    <button class="btn-primary" onclick="document.getElementById('sectorModal').style.display='block'">+ Novo Setor</button>
                </div>
                <div id="sectorsTree">
                    <?php foreach ($sectors as $s): ?>
                        <div><?= htmlspecialchars($s['nome']) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Cadastrar Computador -->
    <div id="computerModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2>Cadastrar Novo Computador</h2>
                <span class="close" onclick="document.getElementById('computerModal').style.display='none'">&times;</span>
            </div>
            <form method="POST">
                <label>Marca do Gabinete*</label>
                <input type="text" name="cabinetBrand" required>
                <label>Marca do Monitor*</label>
                <input type="text" name="monitorBrand" required>
                <label>Setor*</label>
                <select name="sector" required>
                    <?php foreach ($sectors as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Sistema Operacional*</label>
                <input type="text" name="operatingSystem" required>
                <label>Número do Patrimônio*</label>
                <input type="text" name="patrimony" required>
                <label>Processador*</label>
                <input type="text" name="processor" required>
                <label>Armazenamento*</label>
                <input type="text" name="storage" required>
                <label>Memória RAM*</label>
                <input type="text" name="ram" required>
                <label>Placa Mãe*</label>
                <input type="text" name="motherboard" required>
                <label>Placa de Vídeo</label>
                <input type="text" name="videoCard">
                <label>Preço Inicial*</label>
                <input type="number" step="0.01" name="initialPrice" required>
                <label>Data de Aquisição*</label>
                <input type="date" name="acquisitionDate" required>
                <label>Nota Fiscal</label>
                <input type="text" name="invoice">
                <button type="submit" class="btn-primary">Salvar</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
