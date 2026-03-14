-- Migration 003 : Données initiales (admin par défaut)
-- Date : 2024-01-03
-- Mot de passe par défaut : 'admin' (à changer immédiatement en production)
-- Hash généré avec password_hash('admin', PASSWORD_BCRYPT)

INSERT INTO admin_user (username, password_hash)
VALUES ('admin', '$2y$12$YKX.NtJMiXj7yL8T/c/R7.Cx/1nAmWJvTkOSm3hNm9IFBwdXJhHt6')
ON CONFLICT (username) DO NOTHING;
