<!DOCTYPE html>
<html lang="pt-BR" class="h-100"  data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Computadores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app">
        <!-- Header -->
       <header class="header">
    <div class="header-content">
        <img class="logo" src="img/3.png" alt="">
        <h1> Sistema de Gerenciamento de Computadores</h1>
        <nav class="nav">
            <button class="nav-btn active" data-tab="dashboard">Home</button>
            <button class="nav-btn" data-tab="computers">Computadores</button>
            <button class="nav-btn" data-tab="sectors">Setores</button>
        </nav>
        
        <!---------------------VVVVVV  DROPDOWN MODO ESCURO AQUI   VVVVVV---------------------------------------->

        <div class="theme-selector">
            <button class="dropdown-button" id="themeButton">
                <span class="theme-icon" id="currentThemeIcon">☀️</span>
                <span id="currentThemeText">Claro</span>
                <span class="dropdown-icon">▼</span>
            </button>
            
            <div class="dropdown-content" id="dropdownContent">
                <div class="dropdown-item" data-theme="light">
                    <span class="theme-icon">☀️</span>
                    <span>Tema Claro</span>
                </div>
                <div class="dropdown-item" data-theme="dark">
                    <span class="theme-icon">🌙</span>
                    <span>Tema Escuro</span>
                </div>
                <div class="dropdown-item" data-theme="RGB">
                    <span class="theme-icon">&#127912;</span>
                    <span>Tema Escuro com RGB</span>
                </div>
                <div class="dropdown-item" data-theme="auto">
                    <span class="theme-icon">🔄</span>
                    <span>Automático</span>
                </div>
            </div>
        </div>
    </div>
</header>
 <!---------------------------- ^^^^^^   DROPDOWN MODO ESCURO AQUI ^^^^^^^  ---------------------------------------->

    <div class="linha-colorida-animada"></div>



        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Tab -->
            <div id="dashboard" class="tab-content active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">💻</div>
                        <div class="stat-info">
                            <h3>Total de Computadores</h3>
                            <p class="stat-number" id="totalComputers">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-info">
                            <h3>Valor Total Atual</h3>
                            <p class="stat-number" id="totalValue">R$ 0,00</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🏢</div>
                        <div class="stat-info">
                            <h3>Total de Setores</h3>
                            <p class="stat-number" id="totalSectors">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🔧</div>
                        <div class="stat-info">
                            <h3>Manutenções este Mês</h3>
                            <p class="stat-number" id="monthlyMaintenances">0</p>
                        </div>
                    </div>
                </div>
                
                <div class="recent-section">
                    <h2>Últimos Computadores Cadastrados</h2>
                    <div id="recentComputers" class="recent-list"></div>
                </div>
            </div>

            <!-- Computers Tab -->
            <div id="computers" class="tab-content">
                <div class="section-header">
                    <h2>Gerenciamento de Computadores</h2>
                    <button class="btn-primary" onclick="openComputerModal()">+ Novo Computador</button>
                </div>
                
                <div class="search-bar">
                    <input type="text" id="searchComputers" placeholder="Buscar computadores..." onkeyup="searchComputers()">
                    <select id="filterSector" onchange="filterComputers()">
                        <option value="">Todos os setores</option>
                    </select>
                </div>

                <div id="computersList" class="computers-grid"></div>
            </div>

            <!-- Sectors Tab -->
            <div id="sectors" class="tab-content">
                <div class="section-header">
                    <h2>Árvore Industrial - Setores</h2>
                    <button class="btn-primary" onclick="openSectorModal()">+ Novo Setor</button>
                </div>
                <div id="sectorsTree" class="sectors-tree"></div>
            </div>
        </main>
    </div>

    <!-- Computer Modal -->
    <div id="computerModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2 id="modalTitle">Cadastrar Novo Computador</h2>
                <span class="close" onclick="closeModal('computerModal')">&times;</span>
            </div>
            <form id="computerForm" class="computer-form">
                <div class="form-grid">
                    <div class="form-section">
                        <h3>Informações Básicas</h3>
                        <div class="form-group">
                            <label>Marca do Gabinete*</label>
                            <input type="text" name="cabinetBrand" required>
                        </div>
                        <div class="form-group">
                            <label>Marca do Monitor*</label>
                            <input type="text" name="monitorBrand" required>
                        </div>
                        <div class="form-group">
                            <label>Setor*</label>
                            <select name="sector" id="sectorSelect" required></select>
                        </div>
                        <div class="form-group">
                            <label>Sistema Operacional*</label>
                            <input type="text" name="operatingSystem" required>
                        </div>
                        <div class="form-group">
                            <label>Número do Patrimônio*</label>
                            <input type="text" name="patrimony" placeholder="Ex: PAT001234" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Componentes</h3>
                        <div class="form-group">
                            <label>Processador*</label>
                            <input type="text" name="processor" required>
                        </div>
                        <div class="form-group">
                            <label>Armazenamento*</label>
                            <input type="text" name="storage" placeholder="Ex: SSD 256GB, HD 1TB" required>
                        </div>
                        <div class="form-group">
                            <label>Memória RAM*</label>
                            <input type="text" name="ram" placeholder="Ex: 8GB DDR4" required>
                        </div>
                        <div class="form-group">
                            <label>Placa Mãe*</label>
                            <input type="text" name="motherboard" required>
                        </div>
                        <div class="form-group">
                            <label>Placa de Vídeo</label>
                            <input type="text" name="videoCard" placeholder="Deixe em branco se integrada">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Informações Financeiras</h3>
                        <div class="form-group">
                            <label>Preço Inicial*</label>
                            <input type="number" name="initialPrice" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Data de Aquisição*</label>
                            <input type="date" name="acquisitionDate" required>
                        </div>
                        <div class="form-group">
                            <label>Nota Fiscal</label>
                            <input type="text" name="invoice" placeholder="Número da nota fiscal">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Foto do Equipamento</h3>
                        <div class="form-group">
                            <label>Upload da Foto</label>
                            <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this)">
                            <div id="photoPreview" class="photo-preview"></div>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3>Foto da Nota Fiscal</h3>
                        <div class="form-group">
                            <label>Upload da Foto</label>
                            <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this)">
                            <div id="photoPreview" class="photo-preview"></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('computerModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Salvar Computador</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Computer Details Modal -->
    <div id="computerDetailsModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2>Detalhes do Computador</h2>
                <span class="close" onclick="closeModal('computerDetailsModal')">&times;</span>
            </div>
            <div id="computerDetails" class="computer-details"></div>
        </div>
    </div>

    <!-- Maintenance Modal -->
    <div id="maintenanceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Adicionar Manutenção</h2>
                <span class="close" onclick="closeModal('maintenanceModal')">&times;</span>
            </div>
            <form id="maintenanceForm">
                <input type="hidden" name="computerId">
                <div class="form-group">
                    <label>Data da Manutenção*</label>
                    <input type="date" name="date" required>
                </div>
                <div class="form-group">
                    <label>Descrição da Manutenção*</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Peças Trocadas</label>
                    <input type="text" name="partsReplaced" placeholder="Ex: Memória RAM, HD">
                </div>
                <div class="form-group">
                    <label>Custo da Manutenção</label>
                    <input type="number" name="cost" step="0.01" min="0">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('maintenanceModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Adicionar Manutenção</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sector Modal -->
    <div id="sectorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Cadastrar Novo Setor</h2>
                <span class="close" onclick="closeModal('sectorModal')">&times;</span>
            </div>
            <form id="sectorForm">
                <div class="form-group">
                    <label>Nome do Setor*</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="description" rows="3" placeholder="Descrição opcional do setor"></textarea>
                </div>
                <div class="form-group">
                    <label>Responsável</label>
                    <input type="text" name="responsible" placeholder="Nome do responsável pelo setor">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('sectorModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Cadastrar Setor</button>
                </div>
            </form>
        </div>
    </div>
<!-- Escolha o tema-->



    <script src="script.js"></script>
</body>
</html>