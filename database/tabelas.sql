
PRAGMA foreign_keys = ON;


CREATE TABLE Pais (
    nome TEXT PRIMARY KEY
);


CREATE TABLE Tipo_atividade (
    tipo_atividade TEXT PRIMARY KEY,
    CHECK (tipo_atividade IN ('Atração', 'Restauração', 'Experiência'))
);


CREATE TABLE Tipo_alojamento (
    tipo_alojamento TEXT PRIMARY KEY,
    CHECK (tipo_alojamento IN ('Hostel', 'Hotel', 'Alojamento Local', 'Outro'))
);


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


CREATE TABLE Destino (
    id INTEGER PRIMARY KEY,
    cidade_local TEXT NOT NULL,
    pais TEXT NOT NULL,

    FOREIGN KEY (pais) REFERENCES Pais(nome)
);


CREATE TABLE TravelJournals (
    id INTEGER PRIMARY KEY,
    viagem_id INTEGER UNIQUE NOT NULL,
    descricao TEXT NOT NULL,
    avaliacao INTEGER,

    FOREIGN KEY (viagem_id) REFERENCES Viagens(id)
);


CREATE TABLE WishList (
    id INTEGER PRIMARY KEY,
    utilizador TEXT NOT NULL UNIQUE,

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador)
);


CREATE TABLE Feedback (
    id INTEGER PRIMARY KEY,
    rating INTEGER NOT NULL CHECK (rating >= 0 AND rating <= 5),
    precos REAL,
    comentario TEXT
);


CREATE TABLE Media (
    id INTEGER PRIMARY KEY,
    data TEXT NOT NULL,
    path TEXT NOT NULL,
    
    Viagem INTEGER,

    FOREIGN KEY (Viagem) REFERENCES Viagem(id),
);



CREATE TABLE Detalhes (
    id INTEGER PRIMARY KEY,
    nome TEXT NOT NULL,
    localizacao TEXT NOT NULL,
);


CREATE TABLE Detalhes_atividade (
    id INTEGER PRIMARY KEY,
    tipo TEXT NOT NULL, 

    FOREIGN KEY (id) REFERENCES Detalhes(id),
    FOREIGN KEY (tipo) REFERENCES Tipo_atividade(tipo_atividade)
);


CREATE TABLE Detalhes_alojamento (
    id INTEGER PRIMARY KEY,
    tipo TEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES Detalhes(id),
    FOREIGN KEY (tipo) REFERENCES Tipo_alojamento(tipo_alojamento)
);



CREATE TABLE Viagens (
    id INTEGER PRIMARY KEY,
    titulo TEXT NOT NULL,
    data_ida TEXT NOT NULL,
    data_volta TEXT,

    utilizador TEXT NOT NULL,
    destino INTEGER NOT NULL,

    CHECK (data_volta IS NULL OR julianday(data_volta) > julianday(data_ida)),

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (destino) REFERENCES Destino(id)
);


CREATE TABLE Atividade (
    id INTEGER PRIMARY KEY,
    data TEXT NOT NULL,
    viagem INTEGER NOT NULL,
    detalhes INTEGER NOT NULL,

    FOREIGN KEY (viagem) REFERENCES Viagens(id),
    FOREIGN KEY (detalhes) REFERENCES Detalhes_atividade(id)
);


CREATE TABLE Alojamento (
    id INTEGER PRIMARY KEY,
    data_inicio TEXT NOT NULL,
    data_fim TEXT,

    viagem INTEGER NOT NULL,
    detalhes INTEGER NOT NULL,


    CHECK (data_fim IS NULL OR julianday(data_fim) > julianday(data_inicio)),

    FOREIGN KEY (viagem) REFERENCES Viagens(id),
    FOREIGN KEY (detalhes) REFERENCES Detalhes_alojamento(id)
);



CREATE TABLE Seguir (
    utilizador1 TEXT, 
    utilizador2 TEXT, 
    data TEXT NOT NULL,

    PRIMARY KEY (utilizador1, utilizador2),

    FOREIGN KEY (utilizador1) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (utilizador2) REFERENCES Utilizador(nome_de_utilizador),
    CHECK (utilizador1 <> utilizador2) 
);


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


CREATE TABLE Like_Viagem ( 
    utilizador TEXT,
    viagem INTEGER,
    data TEXT NOT NULL,

    PRIMARY KEY (utilizador, viagem),

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (viagem) REFERENCES Viagens(id)
);


CREATE TABLE Guardar_publicacao (
    utilizador TEXT,
    viagem INTEGER,
    data TEXT NOT NULL,

    PRIMARY KEY (utilizador, viagem),

    FOREIGN KEY (utilizador) REFERENCES Utilizador(nome_de_utilizador),
    FOREIGN KEY (viagem) REFERENCES Viagens(id)
);


CREATE TABLE Adicionar_wishlist (
    wishlist INTEGER,
    destino INTEGER,

    PRIMARY KEY (wishlist, destino),

    FOREIGN KEY (wishlist) REFERENCES WishList(id),
    FOREIGN KEY (destino) REFERENCES Destino(id)
);


CREATE TABLE Feedback_alojamento (
    id INTEGER PRIMARY KEY, 
    alojamento INTEGER NOT NULL UNIQUE, 

    FOREIGN KEY (id) REFERENCES Feedback(id),
    FOREIGN KEY (alojamento) REFERENCES Alojamento(id)
);


CREATE TABLE Feedback_atividade (
    id INTEGER PRIMARY KEY, 
    atividade INTEGER NOT NULL UNIQUE, 

    FOREIGN KEY (id) REFERENCES Feedback(id),
    FOREIGN KEY (atividade) REFERENCES Atividade(id)
);