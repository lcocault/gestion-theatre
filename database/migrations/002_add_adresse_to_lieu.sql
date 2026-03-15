-- =============================================================================
-- Migration 002 : ajout de l'adresse aux lieux
-- =============================================================================

ALTER TABLE lieu ADD COLUMN IF NOT EXISTS adresse TEXT;
