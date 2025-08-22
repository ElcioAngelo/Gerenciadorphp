// Global variables
let computers = JSON.parse(localStorage.getItem('computers')) || [];
let sectors = JSON.parse(localStorage.getItem('sectors')) || [
    { id: 1, name: 'Administração', description: 'Setor administrativo', responsible: 'NOME A DEFINIR' },
    { id: 2, name: 'Financeiro', description: 'Setor financeiro', responsible: 'NOME A DEFINIR' },
    { id: 3, name: 'Recursos Humanos', description: 'Setor de RH', responsible: 'NOME A DEFINIR' },
    { id: 4, name: 'TI', description: 'Setor de tecnologia', responsible: 'NOME A DEFINIR' }
];
let currentEditingComputer = null;

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    loadSectors();
    updateDashboard();
    loadComputers();
    loadSectorsTree();
    
    // Setup form handlers
    setupFormHandlers();
});

// Tab management
function initializeTabs() {
    const navBtns = document.querySelectorAll('.nav-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    navBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Remove active class from all buttons and tabs
            navBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked button and corresponding tab
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
}

// Dashboard functions
function updateDashboard() {
    const totalComputers = computers.length;
    const totalValue = computers.reduce((sum, comp) => sum + parseFloat(comp.currentPrice || comp.initialPrice), 0);
    const totalSectors = sectors.length;
    const monthlyMaintenances = getMonthlyMaintenances();
    
    document.getElementById('totalComputers').textContent = totalComputers;
    document.getElementById('totalValue').textContent = formatCurrency(totalValue);
    document.getElementById('totalSectors').textContent = totalSectors;
    document.getElementById('monthlyMaintenances').textContent = monthlyMaintenances;
    
    loadRecentComputers();
}

function getMonthlyMaintenances() {
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();
    
    return computers.reduce((count, computer) => {
        if (computer.maintenances) {
            return count + computer.maintenances.filter(maintenance => {
                const maintenanceDate = new Date(maintenance.date);
                return maintenanceDate.getMonth() === currentMonth && 
                       maintenanceDate.getFullYear() === currentYear;
            }).length;
        }
        return count;
    }, 0);
}

function loadRecentComputers() {
    const recentComputers = computers
        .sort((a, b) => new Date(b.acquisitionDate) - new Date(a.acquisitionDate))
        .slice(0, 5);
    
    const container = document.getElementById('recentComputers');
    
    if (recentComputers.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #64748b; padding: 2rem;">Nenhum computador cadastrado ainda.</p>';
        return;
    }
    
    container.innerHTML = recentComputers.map(computer => `
        <div class="recent-item">
            <div class="recent-info">
                <h4>${computer.cabinetBrand} - ${computer.monitorBrand}</h4>
                <p>${computer.sector} • ${formatDate(computer.acquisitionDate)}</p>
            </div>
            <div class="recent-price">${formatCurrency(computer.currentPrice || computer.initialPrice)}</div>
        </div>
    `).join('');
}

// Computer management
function loadComputers() {
    const container = document.getElementById('computersList');
    
    if (computers.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #64748b; padding: 2rem;">Nenhum computador cadastrado. Clique em "Novo Computador" para começar.</p>';
        return;
    }
    
    container.innerHTML = computers.map(computer => `
        <div class="computer-card" onclick="openComputerDetails(${computer.id})">
            <div class="computer-photo">
                ${computer.photo ? `<img src="${computer.photo}" alt="Foto do computador">` : '💻'}
            </div>
            <div class="computer-info">
                <h3>${computer.cabinetBrand} - ${computer.monitorBrand}</h3>
                <p><strong>Setor:</strong> ${computer.sector}</p>
                <p><strong>Sistema:</strong> ${computer.operatingSystem}</p>
                <p><strong>Processador:</strong> ${computer.processor}</p>
                <p><strong>Memória:</strong> ${computer.ram}</p>
                <p><strong>Data de Aquisição:</strong> ${formatDate(computer.acquisitionDate)}</p>
            </div>
            <div class="computer-price">
                <div class="price-item">
                    <span>Preço Inicial</span>
                    <strong>${formatCurrency(computer.initialPrice)}</strong>
                </div>
                <div class="price-item">
                    <span>Preço Atual</span>
                    <strong style="color: #059669">${formatCurrency(computer.currentPrice || computer.initialPrice)}</strong>
                </div>
            </div>
            <div class="computer-actions" onclick="event.stopPropagation()">
                <button class="btn-success" onclick="openMaintenanceModal(${computer.id})">+ Manutenção</button>
                <button class="btn-secondary" onclick="editComputer(${computer.id})">✏️ Editar</button>
                <button class="btn-danger" onclick="deleteComputer(${computer.id})">🗑️ Excluir</button>
            </div>
        </div>
    `).join('');
}

function openComputerModal() {
    currentEditingComputer = null;
    document.getElementById('modalTitle').textContent = 'Cadastrar Novo Computador';
    document.getElementById('computerForm').reset();
    document.getElementById('photoPreview').innerHTML = '';
    loadSectorOptions();
    document.getElementById('computerModal').style.display = 'block';
}

function editComputer(id) {
    const computer = computers.find(c => c.id === id);
    if (!computer) return;
    
    currentEditingComputer = computer;
    document.getElementById('modalTitle').textContent = 'Editar Computador';
    
    // Fill form with computer data
    const form = document.getElementById('computerForm');
    Object.keys(computer).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
            input.value = computer[key] || '';
        }
    });
    
    // Show photo if exists
    if (computer.photo) {
        document.getElementById('photoPreview').innerHTML = `<img src="${computer.photo}" alt="Foto atual">`;
    }
    
    loadSectorOptions();
    document.getElementById('computerModal').style.display = 'block';
}

function deleteComputer(id) {
    if (confirm('Tem certeza que deseja excluir este computador?')) {
        computers = computers.filter(c => c.id !== id);
        localStorage.setItem('computers', JSON.stringify(computers));
        loadComputers();
        updateDashboard();
        loadSectorsTree();
    }
}

function openComputerDetails(id) {
    const computer = computers.find(c => c.id === id);
    if (!computer) return;
    
    const detailsContainer = document.getElementById('computerDetails');
    
    detailsContainer.innerHTML = `
        <div class="details-grid">
            <div class="details-section">
                <h3>Informações Básicas</h3>
                <div class="details-item">
                    <label>Marca do Gabinete:</label>
                    <span>${computer.cabinetBrand}</span>
                </div>
                <div class="details-item">
                    <label>Marca do Monitor:</label>
                    <span>${computer.monitorBrand}</span>
                </div>
                <div class="details-item">
                    <label>Setor:</label>
                    <span>${computer.sector}</span>
                </div>
                <div class="details-item">
                    <label>Sistema Operacional:</label>
                    <span>${computer.operatingSystem}</span>
                </div>
                ${computer.photo ? `
                <div class="details-item">
                    <label>Foto:</label>
                    <div style="margin-top: 0.5rem;">
                        <img src="${computer.photo}" alt="Foto do computador" style="max-width: 200px; border-radius: 8px;">
                    </div>
                </div>
                ` : ''}
            </div>
            
            <div class="details-section">
                <h3>Componentes</h3>
                <div class="details-item">
                    <label>Processador:</label>
                    <span>${computer.processor}</span>
                </div>
                <div class="details-item">
                    <label>Armazenamento:</label>
                    <span>${computer.storage}</span>
                </div>
                <div class="details-item">
                    <label>Memória RAM:</label>
                    <span>${computer.ram}</span>
                </div>
                <div class="details-item">
                    <label>Placa Mãe:</label>
                    <span>${computer.motherboard}</span>
                </div>
                <div class="details-item">
                    <label>Placa de Vídeo:</label>
                    <span>${computer.videoCard || 'Integrada'}</span>
                </div>
            </div>
            
            <div class="details-section">
                <h3>Informações Financeiras</h3>
                <div class="details-item">
                    <label>Preço Inicial:</label>
                    <span style="color: #3b82f6; font-weight: 600;">${formatCurrency(computer.initialPrice)}</span>
                </div>
                <div class="details-item">
                    <label>Preço Atual:</label>
                    <span style="color: #059669; font-weight: 600;">${formatCurrency(computer.currentPrice || computer.initialPrice)}</span>
                </div>
                <div class="details-item">
                    <label>Data de Aquisição:</label>
                    <span>${formatDate(computer.acquisitionDate)}</span>
                </div>
                <div class="details-item">
                    <label>Nota Fiscal:</label>
                    <span>${computer.invoice || 'Não informado'}</span>
                </div>
            </div>
        </div>
        
        <div class="maintenance-history">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Histórico de Manutenções</h3>
                <button class="btn-primary" onclick="openMaintenanceModal(${computer.id})">+ Nova Manutenção</button>
            </div>
            ${computer.maintenances && computer.maintenances.length > 0 ? 
                computer.maintenances.map(maintenance => `
                    <div class="maintenance-item">
                        <div class="maintenance-header">
                            <span class="maintenance-date">${formatDate(maintenance.date)}</span>
                            ${maintenance.cost ? `<span class="maintenance-cost">+${formatCurrency(maintenance.cost)}</span>` : ''}
                        </div>
                        <div class="maintenance-description">${maintenance.description}</div>
                        ${maintenance.partsReplaced ? `<div class="maintenance-parts">Peças trocadas: ${maintenance.partsReplaced}</div>` : ''}
                    </div>
                `).join('') : 
                '<p style="text-align: center; color: #64748b; padding: 2rem;">Nenhuma manutenção registrada.</p>'
            }
        </div>
    `;
    
    document.getElementById('computerDetailsModal').style.display = 'block';
}

// Maintenance functions
function openMaintenanceModal(computerId) {
    document.getElementById('maintenanceForm').reset();
    document.querySelector('input[name="computerId"]').value = computerId;
    document.querySelector('input[name="date"]').value = new Date().toISOString().split('T')[0];
    document.getElementById('maintenanceModal').style.display = 'block';
}

// Sector management
function loadSectors() {
    localStorage.setItem('sectors', JSON.stringify(sectors));
}

function loadSectorOptions() {
    const sectorSelect = document.getElementById('sectorSelect');
    const filterSector = document.getElementById('filterSector');
    
    const options = sectors.map(sector => `<option value="${sector.name}">${sector.name}</option>`).join('');
    
    sectorSelect.innerHTML = '<option value="">Selecione um setor</option>' + options;
    if (filterSector) {
        filterSector.innerHTML = '<option value="">Todos os setores</option>' + options;
    }
}

function loadSectorsTree() {
    const container = document.getElementById('sectorsTree');
    
    container.innerHTML = sectors.map(sector => {
        const sectorComputers = computers.filter(c => c.sector === sector.name);
        const sectorValue = sectorComputers.reduce((sum, c) => sum + parseFloat(c.currentPrice || c.initialPrice), 0);
        
        return `
            <div class="sector-item">
                <div class="sector-header" onclick="toggleSectorComputers(${sector.id})">
                    <div class="sector-info">
                        <h3>${sector.name}</h3>
                        <p>${sector.description || 'Sem descrição'} • Responsável: ${sector.responsible || 'Não informado'}</p>
                    </div>
                    <div class="sector-stats">
                        <span class="sector-stat">${sectorComputers.length} equipamentos</span>
                        <span class="sector-stat">${formatCurrency(sectorValue)}</span>
                        <span id="toggle-${sector.id}">▼</span>
                    </div>
                </div>
                <div id="computers-${sector.id}" class="sector-computers" style="display: none;">
                    ${sectorComputers.length > 0 ? 
                        sectorComputers.map(computer => `
                            <div class="sector-computer" onclick="openComputerDetails(${computer.id})">
                                <div>
                                    <strong>${computer.cabinetBrand} - ${computer.monitorBrand}</strong>
                                    <br>
                                    <small>${computer.processor} • ${computer.ram}</small>
                                </div>
                                <div style="color: #059669; font-weight: 600;">
                                    ${formatCurrency(computer.currentPrice || computer.initialPrice)}
                                </div>
                            </div>
                        `).join('') : 
                        '<p style="text-align: center; color: #64748b; padding: 1rem;">Nenhum equipamento neste setor</p>'
                    }
                </div>
            </div>
        `;
    }).join('');
}

function toggleSectorComputers(sectorId) {
    const computersDiv = document.getElementById(`computers-${sectorId}`);
    const toggleIcon = document.getElementById(`toggle-${sectorId}`);
    
    if (computersDiv.style.display === 'none') {
        computersDiv.style.display = 'block';
        toggleIcon.textContent = '▲';
    } else {
        computersDiv.style.display = 'none';
        toggleIcon.textContent = '▼';
    }
}

function openSectorModal() {
    document.getElementById('sectorForm').reset();
    document.getElementById('sectorModal').style.display = 'block';
}

// Search and filter functions
function searchComputers() {
    const searchTerm = document.getElementById('searchComputers').value.toLowerCase();
    const filteredComputers = computers.filter(computer => 
        computer.cabinetBrand.toLowerCase().includes(searchTerm) ||
        computer.monitorBrand.toLowerCase().includes(searchTerm) ||
        computer.sector.toLowerCase().includes(searchTerm) ||
        computer.processor.toLowerCase().includes(searchTerm) ||
        computer.operatingSystem.toLowerCase().includes(searchTerm)
    );
    
    displayFilteredComputers(filteredComputers);
}

function filterComputers() {
    const selectedSector = document.getElementById('filterSector').value;
    const filteredComputers = selectedSector ? 
        computers.filter(computer => computer.sector === selectedSector) : 
        computers;
    
    displayFilteredComputers(filteredComputers);
}

function displayFilteredComputers(filteredComputers) {
    const container = document.getElementById('computersList');
    
    if (filteredComputers.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #64748b; padding: 2rem;">Nenhum computador encontrado.</p>';
        return;
    }
    
    container.innerHTML = filteredComputers.map(computer => `
        <div class="computer-card" onclick="openComputerDetails(${computer.id})">
            <div class="computer-photo">
                ${computer.photo ? `<img src="${computer.photo}" alt="Foto do computador">` : '💻'}
            </div>
            <div class="computer-info">
                <h3>${computer.cabinetBrand} - ${computer.monitorBrand}</h3>
                <p><strong>Setor:</strong> ${computer.sector}</p>
                <p><strong>Sistema:</strong> ${computer.operatingSystem}</p>
                <p><strong>Processador:</strong> ${computer.processor}</p>
                <p><strong>Memória:</strong> ${computer.ram}</p>
                <p><strong>Data de Aquisição:</strong> ${formatDate(computer.acquisitionDate)}</p>
            </div>
            <div class="computer-price">
                <div class="price-item">
                    <span>Preço Inicial</span>
                    <strong>${formatCurrency(computer.initialPrice)}</strong>
                </div>
                <div class="price-item">
                    <span>Preço Atual</span>
                    <strong style="color: #059669">${formatCurrency(computer.currentPrice || computer.initialPrice)}</strong>
                </div>
            </div>
            <div class="computer-actions" onclick="event.stopPropagation()">
                <button class="btn-success" onclick="openMaintenanceModal(${computer.id})">+ Manutenção</button>
                <button class="btn-secondary" onclick="editComputer(${computer.id})">✏️ Editar</button>
                <button class="btn-danger" onclick="deleteComputer(${computer.id})">🗑️ Excluir</button>
            </div>
        </div>
    `).join('');
}

// Form handlers
function setupFormHandlers() {
    // Computer form
    document.getElementById('computerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const computerData = {};
        
        for (let [key, value] of formData.entries()) {
            if (key !== 'photo') {
                computerData[key] = value;
            }
        }
        
        // Handle photo
        const photoFile = formData.get('photo');
        if (photoFile && photoFile.size > 0) {
            const reader = new FileReader();
            reader.onload = function(e) {
                computerData.photo = e.target.result;
                saveComputer(computerData);
            };
            reader.readAsDataURL(photoFile);
        } else {
            if (currentEditingComputer && currentEditingComputer.photo) {
                computerData.photo = currentEditingComputer.photo;
            }
            saveComputer(computerData);
        }
    });
    
    // Maintenance form
    document.getElementById('maintenanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const maintenanceData = {};
        
        for (let [key, value] of formData.entries()) {
            maintenanceData[key] = value;
        }
        
        const computerId = parseInt(maintenanceData.computerId);
        const computer = computers.find(c => c.id === computerId);
        
        if (computer) {
            if (!computer.maintenances) {
                computer.maintenances = [];
            }
            
            computer.maintenances.push({
                date: maintenanceData.date,
                description: maintenanceData.description,
                partsReplaced: maintenanceData.partsReplaced,
                cost: parseFloat(maintenanceData.cost) || 0
            });
            
            // Update current price
            if (maintenanceData.cost) {
                computer.currentPrice = (parseFloat(computer.currentPrice) || parseFloat(computer.initialPrice)) + parseFloat(maintenanceData.cost);
            }
            
            localStorage.setItem('computers', JSON.stringify(computers));
            loadComputers();
            updateDashboard();
            loadSectorsTree();
            closeModal('maintenanceModal');
            
            // If computer details is open, refresh it
            const detailsModal = document.getElementById('computerDetailsModal');
            if (detailsModal.style.display === 'block') {
                openComputerDetails(computerId);
            }
        }
    });
    
    // Sector form
    document.getElementById('sectorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const sectorData = {
            id: Date.now(),
            name: formData.get('name'),
            description: formData.get('description'),
            responsible: formData.get('responsible')
        };
        
        sectors.push(sectorData);
        localStorage.setItem('sectors', JSON.stringify(sectors));
        loadSectorOptions();
        loadSectorsTree();
        updateDashboard();
        closeModal('sectorModal');
    });
}

function saveComputer(computerData) {
    if (currentEditingComputer) {
        // Update existing computer
        const index = computers.findIndex(c => c.id === currentEditingComputer.id);
        computers[index] = { ...currentEditingComputer, ...computerData };
    } else {
        // Create new computer
        computerData.id = Date.now();
        computerData.currentPrice = computerData.initialPrice;
        computerData.maintenances = [];
        computers.push(computerData);
    }
    
    localStorage.setItem('computers', JSON.stringify(computers));
    loadComputers();
    updateDashboard();
    loadSectorsTree();
    closeModal('computerModal');
}

// Photo preview
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview da foto">`;
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}

// Modal functions
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Click outside modal to close
window.addEventListener('click', function(e) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Utility functions
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR');
}

// Initialize sector options when page loads
setTimeout(() => {
    loadSectorOptions();
}
)