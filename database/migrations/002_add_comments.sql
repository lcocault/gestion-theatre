-- Migration 002 : Ajout de la table commentaires
-- Date : 2024-01-02

CREATE TABLE IF NOT EXISTS commentaire (
    id SERIAL PRIMARY KEY,
    representation_id INTEGER NOT NULL REFERENCES representation(id) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    note INTEGER CHECK (note >= 1 AND note <= 5),
    commentaire TEXT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valide BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE INDEX IF NOT EXISTS idx_commentaire_representation ON commentaire(representation_id);
