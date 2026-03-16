-- =============================================================================
-- Migration initiale : version 1.0.0
-- Projet : Gestion Théâtre Haute-Garonne
-- Crée toutes les tables, index et données initiales.
-- =============================================================================

-- Extension UUID
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ─── Lieux ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS lieu (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    plan_acces TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── Troupes ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS troupe (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email_contact VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── Pièces ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS piece (
    id SERIAL PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(255),
    synopsis TEXT,
    troupe_id INTEGER REFERENCES troupe(id) ON DELETE SET NULL,
    type VARCHAR(100),
    duree_minutes INTEGER,
    age_minimum INTEGER DEFAULT 0,
    affiche_vignette VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── Représentations ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS representation (
    id SERIAL PRIMARY KEY,
    piece_id INTEGER NOT NULL REFERENCES piece(id) ON DELETE CASCADE,
    lieu_id INTEGER REFERENCES lieu(id) ON DELETE SET NULL,
    date_debut TIMESTAMP NOT NULL,
    max_spectateurs INTEGER NOT NULL DEFAULT 100,
    date_limite_reservation TIMESTAMP,
    gratuit BOOLEAN NOT NULL DEFAULT FALSE,
    annulee BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── Tarification ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS representation_prix (
    id SERIAL PRIMARY KEY,
    representation_id INTEGER NOT NULL REFERENCES representation(id) ON DELETE CASCADE,
    categorie VARCHAR(100) NOT NULL,
    prix NUMERIC(8, 2) NOT NULL DEFAULT 0.00
);

-- ─── Modes de paiement ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS representation_paiement (
    id SERIAL PRIMARY KEY,
    representation_id INTEGER NOT NULL REFERENCES representation(id) ON DELETE CASCADE,
    mode VARCHAR(50) NOT NULL CHECK (mode IN ('en_ligne', 'cb', 'cheque', 'especes'))
);

-- ─── Programmations ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS programmation (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    affiche_vignette VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── Association programmation ↔ représentation ───────────────────────────────
CREATE TABLE IF NOT EXISTS programmation_representation (
    programmation_id INTEGER NOT NULL REFERENCES programmation(id) ON DELETE CASCADE,
    representation_id INTEGER NOT NULL REFERENCES representation(id) ON DELETE CASCADE,
    PRIMARY KEY (programmation_id, representation_id)
);

-- ─── Réservations ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reservation (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    representation_id INTEGER NOT NULL REFERENCES representation(id) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    telephone VARCHAR(30),
    email VARCHAR(255) NOT NULL,
    source_decouverte VARCHAR(255),
    handicap_visuel_auditif BOOLEAN NOT NULL DEFAULT FALSE,
    handicap_moteur BOOLEAN NOT NULL DEFAULT FALSE,
    statut VARCHAR(20) NOT NULL DEFAULT 'reserve' CHECK (statut IN ('reserve', 'confirme', 'annule')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── Places réservées ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reservation_places (
    id SERIAL PRIMARY KEY,
    reservation_id UUID NOT NULL REFERENCES reservation(id) ON DELETE CASCADE,
    categorie VARCHAR(100) NOT NULL,
    quantite INTEGER NOT NULL DEFAULT 1,
    prix_unitaire NUMERIC(8, 2) NOT NULL DEFAULT 0.00
);

-- ─── Commentaires spectateurs ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS commentaire (
    id SERIAL PRIMARY KEY,
    representation_id INTEGER NOT NULL REFERENCES representation(id) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    note INTEGER CHECK (note >= 1 AND note <= 5),
    commentaire TEXT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valide BOOLEAN NOT NULL DEFAULT FALSE
);

-- ─── Utilisateurs admin ───────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin_user (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── Index ────────────────────────────────────────────────────────────────────
CREATE INDEX IF NOT EXISTS idx_representation_piece        ON representation(piece_id);
CREATE INDEX IF NOT EXISTS idx_representation_lieu         ON representation(lieu_id);
CREATE INDEX IF NOT EXISTS idx_representation_date         ON representation(date_debut);
CREATE INDEX IF NOT EXISTS idx_reservation_representation  ON reservation(representation_id);
CREATE INDEX IF NOT EXISTS idx_reservation_email           ON reservation(email);
CREATE INDEX IF NOT EXISTS idx_piece_troupe                ON piece(troupe_id);
CREATE INDEX IF NOT EXISTS idx_commentaire_representation  ON commentaire(representation_id);

-- ─── Données initiales ────────────────────────────────────────────────────────
-- Compte admin par défaut : admin / admin
-- ⚠️  Changer ce mot de passe immédiatement en production !
-- Hash généré avec password_hash('admin', PASSWORD_BCRYPT)
INSERT INTO admin_user (username, password_hash)
VALUES ('admin', '$2y$12$YKX.NtJMiXj7yL8T/c/R7.Cx/1nAmWJvTkOSm3hNm9IFBwdXJhHt6')
ON CONFLICT (username) DO NOTHING;
