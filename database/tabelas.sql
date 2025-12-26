-- É fundamental incluir esta linha no início do script SQLite para ativar as Foreign Keys.
PRAGMA foreign_keys = ON;

----------------------------------------------------
-- 1. TABELAS BASE E TIPOS (Entidades e Domínios)
----------------------------------------------------

-- País (nome)
CREATE TABLE Pais (
    nome TEXT PRIMARY KEY
);

-- Tipo_atividade (tipo atividade)
CREATE TABLE Tipo_atividade (
    tipo_atividade TEXT PRIMARY KEY,
    CHECK (tipo_atividade IN ('Atração', 'Restauração', 'Experiência'))
);

-- Tipo_alojamento (tipo alojamento)
CREATE TABLE Tipo_alojamento (
    tipo_alojamento TEXT PRIMARY KEY,
    CHECK (tipo_alojamento IN ('Hostel', 'Hotel', 'Alojamento Local', 'Outro'))
);

-- Utilizador (nome de utilizador (PK), nome, e-mail(UNIQUE), país_de_origem (FK), preferência_de_viagem, foto_de_perfil)
CREATE TABLE Utilizador (
    nome_de_utilizador TEXT PRIMARY KEY,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    pais_de_origem TEXT NOT NULL,
    preferencia_de_viagem TEXT NOT NULL,
    foto_de_perfil TEXT,
    palavra_passe TEXT NOT NULL,

    FOREIGN KEY (pais_de_origem) REFERENCES Pais(nome)
);

-- Destino (id (PK), cidade_local, país (FK))
CREATE TABLE Destino (
    id INTEGER PRIMARY KEY,
    cidade_local TEXT NOT NULL,
    pais TEXT NOT NULL,

    FOREIGN KEY (pais) REFERENCES Pais(nome)
);

-- TraveJournals (id (PK), descrição, avaliação)
CREATE TABLE TravelJournals (
    id INTEGER PRIMARY KEY,
    viagem_id INTEGER UNIQUE NOT NULL,
    descricao TEXT NOT NULL,
    avaliacao INTEGER,

    FOREIGN KEY (viagem_id) REFERENCES Viagens(id)
);

-- WishList (id (PK), utilizador (FK/UNIQUE))
CREATE TABLE WishList (
    id INTEGER PRIMARY KEY,
    utilizador TEXT NOT NULL UNIQUE,

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador)
);

-- Feedback (id (PK), rating, preços, comentário)
CREATE TABLE Feedback (
    id INTEGER PRIMARY KEY,
    rating INTEGER NOT NULL CHECK (rating >= 0 AND rating <= 5),
    precos REAL,
    comentario TEXT
);

-- Media (id (PK), data, path, TravelJournal (FK), Feedback (FK))
CREATE TABLE Media (
    id INTEGER PRIMARY KEY,
    data TEXT NOT NULL,
    path TEXT NOT NULL,
    
    Viagem INTEGER,
    Feedback INTEGER,

    FOREIGN KEY (TravelJournal) REFERENCES TravelJournals(id),
    FOREIGN KEY (Feedback) REFERENCES Feedback(id)
);


----------------------------------------------------
-- 2. TABELAS DE DETALHES (Herança de Detalhes)
----------------------------------------------------

-- Detalhes (id (PK), nome, localização, avg_rating)
CREATE TABLE Detalhes (
    id INTEGER PRIMARY KEY,
    nome TEXT NOT NULL,
    localizacao TEXT NOT NULL,
    avg_rating REAL CHECK (avg_rating >= 0 AND avg_rating <= 5)
);

-- Detalhes_atividade (id (PK/FK), tipo (FK))
CREATE TABLE Detalhes_atividade (
    id INTEGER PRIMARY KEY,
    tipo TEXT NOT NULL, 

    FOREIGN KEY (id) REFERENCES Detalhes(id),
    FOREIGN KEY (tipo) REFERENCES Tipo_atividade(tipo_atividade)
);

-- Detalhes_alojamento (id (PK/FK), tipo (FK))
CREATE TABLE Detalhes_alojamento (
    id INTEGER PRIMARY KEY,
    tipo TEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES Detalhes(id),
    FOREIGN KEY (tipo) REFERENCES Tipo_alojamento(tipo_alojamento)
);


----------------------------------------------------
-- 3. VIAGENS, ATIVIDADES E ALOJAMENTOS
----------------------------------------------------

-- Viagens (id (PK), título, data_ida, data_volta, utilizador (FK), destino (FK), travel_journal (FK/UNIQUE))
CREATE TABLE Viagens (
    id INTEGER PRIMARY KEY,
    titulo TEXT NOT NULL,
    data_ida TEXT NOT NULL,
    data_volta TEXT,

    utilizador TEXT NOT NULL,
    destino INTEGER NOT NULL,

    -- Restrição de datas: data_volta deve ser > data_ida
    CHECK (data_volta IS NULL OR julianday(data_volta) > julianday(data_ida)),

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (destino) REFERENCES Destino(id)
);

-- Atividade (id (PK), data, viagem (FK), detalhes (FK))
CREATE TABLE Atividade (
    id INTEGER PRIMARY KEY,
    data TEXT NOT NULL,
    viagem INTEGER NOT NULL,
    detalhes INTEGER NOT NULL,

    FOREIGN KEY (viagem) REFERENCES Viagens(id),
    FOREIGN KEY (detalhes) REFERENCES Detalhes_atividade(id)
);

-- Alojamento (id (PK), data_início, data_fim, viagem (FK), detalhes (FK))
CREATE TABLE Alojamento (
    id INTEGER PRIMARY KEY,
    data_inicio TEXT NOT NULL,
    data_fim TEXT,

    viagem INTEGER NOT NULL,
    detalhes INTEGER NOT NULL,

    -- Restrição de datas: data_fim deve ser > data_início
    CHECK (data_fim IS NULL OR julianday(data_fim) > julianday(data_inicio)),

    FOREIGN KEY (viagem) REFERENCES Viagens(id),
    FOREIGN KEY (detalhes) REFERENCES Detalhes_alojamento(id)
);


----------------------------------------------------
-- 4. RELACIONAMENTOS (Associações N:M, 1:N)
----------------------------------------------------

-- Seguir (utilizador1 (PK/FK), utilizador2 (PK/FK), data)
CREATE TABLE Seguir (
    utilizador1 TEXT, 
    utilizador2 TEXT, 
    data TEXT NOT NULL,

    PRIMARY KEY (utilizador1, utilizador2),

    FOREIGN KEY (utilizador1) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (utilizador2) REFERENCES Utilizador(nome_de_utilizador),
    CHECK (utilizador1 <> utilizador2) 
);

-- Comentário (id (PK), utilizador (FK), viagem (FK), texto, data, hora)
CREATE TABLE Comentario (
    id INTEGER PRIMARY KEY,
    utilizador TEXT NOT NULL,
    viagem INTEGER NOT NULL,
    texto TEXT NOT NULL,
    data TEXT NOT NULL,
    hora TEXT NOT NULL,

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (viagem) REFERENCES Viagens(id)
);

-- Like (utilizador (PK/FK), viagem (PK/FK), data)
CREATE TABLE Like_Viagem ( 
    utilizador TEXT,
    viagem INTEGER,
    data TEXT NOT NULL,

    PRIMARY KEY (utilizador, viagem),

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (viagem) REFERENCES Viagens(id)
);

-- Guardar_publicação (utilizador (PK/FK), viagem (PK/FK), data)
CREATE TABLE Guardar_publicacao (
    utilizador TEXT,
    viagem INTEGER,
    data TEXT NOT NULL,

    PRIMARY KEY (utilizador, viagem),

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (viagem) REFERENCES Viagens(id)
);

-- Adicionar_wishlist (wishlist (PK/FK), destino (PK/FK))
CREATE TABLE Adicionar_wishlist (
    wishlist INTEGER,
    destino INTEGER,

    PRIMARY KEY (wishlist, destino),

    FOREIGN KEY (wishlist) REFERENCES WishList(id),
    FOREIGN KEY (destino) REFERENCES Destino(id)
);

----------------------------------------------------
-- 5. FEEDBACKS (Relação 1:1 via Chave)
----------------------------------------------------

-- Feedback_alojamento (id (PK/FK), alojamento (FK/UNIQUE))
CREATE TABLE Feedback_alojamento (
    id INTEGER PRIMARY KEY, 
    alojamento INTEGER NOT NULL UNIQUE, 

    FOREIGN KEY (id) REFERENCES Feedback(id),
    FOREIGN KEY (alojamento) REFERENCES Alojamento(id)
);

-- Feedback_atividade (id (PK/FK), atividade (FK/UNIQUE))
CREATE TABLE Feedback_atividade (
    id INTEGER PRIMARY KEY, 
    atividade INTEGER NOT NULL UNIQUE, 

    FOREIGN KEY (id) REFERENCES Feedback(id),
    FOREIGN KEY (atividade) REFERENCES Atividade(id)
);