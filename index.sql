-- Tabelas ajustadas para MySQL/MariaDB

CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    password VARCHAR(200) NOT NULL
);

CREATE TABLE IF NOT EXISTS processador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_modelo VARCHAR(200) NOT NULL,
    geracao SMALLINT NOT NULL CHECK (geracao > 0),
    qntd_nucleos SMALLINT NOT NULL CHECK (qntd_nucleos > 0),
    qntd_threads SMALLINT NOT NULL CHECK (qntd_threads >= qntd_nucleos),
    frequencia FLOAT NOT NULL CHECK (frequencia > 0),
    arquitetura ENUM('x86','x64','Arm') NOT NULL,
    soquete VARCHAR(100) NOT NULL,
    potencia_termal FLOAT NOT NULL CHECK (potencia_termal > 0)
);

CREATE TABLE IF NOT EXISTS memoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_modelo VARCHAR(200) NOT NULL,
    geracao SMALLINT NOT NULL CHECK (geracao > 0),
    qntd SMALLINT NOT NULL CHECK (qntd > 0)
);

CREATE TABLE IF NOT EXISTS armazenamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_modelo VARCHAR(200) NOT NULL,
    qntd SMALLINT NOT NULL CHECK (qntd > 0),
    tipo ENUM('HDD','SSD(SATA)','SSD(NVME)') NOT NULL
);

CREATE TABLE IF NOT EXISTS historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_aquisicao DATE NOT NULL,
    preco DECIMAL(14,2) NOT NULL CHECK (preco > 0)
);

CREATE TABLE IF NOT EXISTS patrimonio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setor VARCHAR(200) NOT NULL,
    patrimonio INT NOT NULL CHECK (patrimonio > 0)
);

CREATE TABLE IF NOT EXISTS impressora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(200) NOT NULL,
    tipo ENUM('laser','tinta') NOT NULL,
    historico_id INT NOT NULL,
    patrimonio_id INT NOT NULL,
    FOREIGN KEY (historico_id) REFERENCES historico(id),
    FOREIGN KEY (patrimonio_id) REFERENCES patrimonio(id)
);

CREATE TABLE IF NOT EXISTS computador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    processador_id INT NOT NULL,
    memoria_id INT NOT NULL,
    armazenamento_id INT NOT NULL,
    patrimonio_id INT NOT NULL,
    historico_id INT NOT NULL,
    FOREIGN KEY (processador_id) REFERENCES processador(id),
    FOREIGN KEY (memoria_id) REFERENCES memoria(id),
    FOREIGN KEY (armazenamento_id) REFERENCES armazenamento(id),
    FOREIGN KEY (patrimonio_id) REFERENCES patrimonio(id),
    FOREIGN KEY (historico_id) REFERENCES historico(id)
);

CREATE TABLE IF NOT EXISTS estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_do_item VARCHAR(100) NOT NULL,
    item_id INT NOT NULL,
    qntd INT NOT NULL DEFAULT 0,
    UNIQUE(tipo_do_item, item_id)
);

-- =========================================
-- Inserts de teste
-- =========================================

INSERT INTO historico (data_aquisicao, preco)
VALUES (NOW(), 100.00);

INSERT INTO patrimonio (setor, patrimonio)
VALUES ('TI', 12345);

INSERT INTO processador (
    nome_modelo, geracao, qntd_nucleos, qntd_threads,
    frequencia, arquitetura, soquete, potencia_termal
) VALUES (
    'Intel Core i5-10400', 10, 6, 12,
    2.9, 'x64', 'LGA1200', 65
);

INSERT INTO memoria (nome_modelo, geracao, qntd)
VALUES ('Kingston Fury Beast', 5, 8);

INSERT INTO armazenamento (nome_modelo, qntd, tipo)
VALUES ('Samsung 970 EVO', 1, 'SSD(NVME)');

INSERT INTO impressora (modelo, tipo, historico_id, patrimonio_id)
VALUES ('HP LaserJet 1020', 'laser', 1, 1);

SELECT * FROM historico;
