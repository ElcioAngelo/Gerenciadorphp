create type cpu_arquitetura as enum('x86','x64','Arm');
create type tipo_impressora as enum('laser','tinta');
create type tipo_armazenamento as enum('HDD','SSD(SATA)','SSD(NVME)');

create table if not exists user (
    id serial primary key,
    name text not null,
    email text not null,
    password text not null,
);

create table if not exists processador (
    id serial primary key,
    nome_modelo text not null,
    geracao smallint not null check(geracao > 0),
    qntd_nucleos smallint not null check(qntd_nucleos > 0),
    qntd_threads smallint not null check(qntd_threads >= qntd_nucleos),
    frequencia float not null check(frequencia > 0),
    arquitetura cpu_arquitetura not null,
    soquete text not null,
    potencia_termal float not null check( potencia_termal > 0)
);

create table if not exists memoria (
    id serial primary key,
    nome_modelo text not null,
    geracao SMALLINT not null check (geracao > 0),
    qntd smallint not null check (qntd > 0)
);

create table if not exists armazenamento (
    id serial primary key,
    nome_modelo text not null,
    qntd smallint not null check (qntd > 0),
    tipo tipo_armazenamento not null
);

create table if not exists historico (
    id serial primary key,
    data_aquisicao date not null,
    preco decimal(14,2) not null check (preco > 0)
);

create table if not exists patrimonio(
    id serial primary key,
    setor text not null,
    patrimonio integer not null (check > 0)
);

create table if not exists impressora (
    id serial primary key,
    modelo text not null,
    tipo tipo_impressora not null,
    historico_id int not null,
    patrimonio_id int not null,
    foreign key (historico_id) references historico(id),
    foreign key (patrimonio_id) references patrimonio(id)
);

create table if not exists computador (
    id serial primary key,
    processador_id int not null,
    memoria_id int not null,
    armazenamento_id int not null,
    patrimonio_id int not null,
    historico_id int not null,
    FOREIGN KEY (processador_id) references processador(id),
    FOREIGN KEY (memoria_id) references memoria(id),
    FOREIGN KEY (armazenamento_id) references armazenamento(id),
    FOREIGN KEY (patrimonio_id) references patrimonio(id),
    FOREIGN KEY (historico_id) references historico(id)
);

create table if not exists estoque (
    id serial primary key,
    tipo_do_item text not null,
    item_id integer not null,
    qntd integer not null default 0,

    UNIQUE(tipo_do_item, item_id)
);




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

insert into historico (data_aquisicao, preco)
values (now(),100.00);

insert into patrimonio (patrimonio, setor)
values ()

select * from historico;







