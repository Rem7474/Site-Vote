-- Schéma SQL pour la plateforme de vote en ligne (PostgreSQL)

-- Table des organisateurs
CREATE TABLE organisateurs (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL
);

-- Table des événements
CREATE TABLE evenements (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    univ VARCHAR(255) NOT NULL,
    reforga INTEGER NOT NULL REFERENCES organisateurs(id) ON DELETE CASCADE
);

-- Table des listes/candidats
CREATE TABLE listes (
    id SERIAL PRIMARY KEY,
    refevent INTEGER NOT NULL REFERENCES evenements(id) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    photo VARCHAR(255),
    description TEXT
);

-- Table des membres d'une liste
CREATE TABLE membres (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    fonction VARCHAR(100),
    refliste INTEGER NOT NULL REFERENCES listes(id) ON DELETE CASCADE
);

-- Table des participants (hashs de vote)
CREATE TABLE participants (
    id SERIAL PRIMARY KEY,
    hash VARCHAR(64) UNIQUE NOT NULL,
    refevent INTEGER NOT NULL REFERENCES evenements(id) ON DELETE CASCADE
);

-- Table des utilisateurs (votants)
CREATE TABLE utilisateurs (
    id SERIAL PRIMARY KEY,
    login VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    refevent INTEGER NOT NULL REFERENCES evenements(id) ON DELETE CASCADE
);

-- Table des votes
CREATE TABLE votes (
    id SERIAL PRIMARY KEY,
    refliste INTEGER NOT NULL REFERENCES listes(id) ON DELETE CASCADE,
    hash VARCHAR(64) NOT NULL REFERENCES participants(hash) ON DELETE CASCADE,
    refevent INTEGER NOT NULL REFERENCES evenements(id) ON DELETE CASCADE,
    datevote TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour accélérer les recherches
CREATE INDEX idx_votes_refevent ON votes(refevent);
CREATE INDEX idx_votes_refliste ON votes(refliste);
CREATE INDEX idx_votes_hash ON votes(hash);
CREATE INDEX idx_participants_refevent ON participants(refevent);
CREATE INDEX idx_utilisateurs_refevent ON utilisateurs(refevent);

-- Contraintes supplémentaires
ALTER TABLE utilisateurs ADD CONSTRAINT unique_user_event UNIQUE (login, refevent);
