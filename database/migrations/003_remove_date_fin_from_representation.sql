-- Migration 003: Suppression de la colonne date_fin de la table representation
-- Les représentations ne sont plus caractérisées par une date de fin.
ALTER TABLE representation DROP COLUMN IF EXISTS date_fin;
