# Gestion Théâtre

Système de gestion des réservations pour des représentations de théâtre amateur en **Haute-Garonne (France)**.

## Fonctionnalités

### Espace spectateur (public)
- Accueil avec carrousel des prochaines représentations
- Consultation des programmations et détail d'une programmation
- Fiches pièces avec synopsis, représentations associées et commentaires spectateurs
- Liste des lieux avec représentations passées et à venir
- Formulaire de réservation avec sélection des places par catégorie
- Confirmation par email avec lien d'annulation
- Annulation de réservation via lien unique

### Espace administrateur (accès restreint `/admin`)
- Tableau de bord avec vue d'ensemble
- CRUD complet : Lieux, Troupes, Pièces, Représentations, Programmations
- Gestion des réservations par représentation (confirmer / annuler)
- Annulation d'une représentation avec notification email de tous les spectateurs
- Protection CSRF sur tous les formulaires

---

## Architecture

```
/public            → Point d'entrée web (index.php, router.php, assets)
/src
    Controllers/   → Contrôleurs MVC (+ sous-dossier Admin/)
    Models/        → Modèles de données
    Repositories/  → Accès base de données (PDO)
    Services/      → Logique métier (EmailService, ReservationService)
    Views/         → Templates PHP
    Middleware/    → AuthMiddleware, CsrfMiddleware
/config            → config.php, database.php
/database
    migrations/    → Scripts SQL numérotés
/scripts           → deploy.sh
/.github/workflows → deploy.yml (GitHub Actions)
```

---

## Prérequis

- **PHP 8.1+** avec extensions : `pdo_pgsql`, `mbstring`, `openssl`
- **PostgreSQL 13+**
- Serveur web **Apache** avec `mod_rewrite` activé (ou Nginx équivalent)

---

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/lcocault/gestion-theatre.git
cd gestion-theatre
```

### 2. Configuration

Copier le fichier d'exemple et éditer les valeurs :

```bash
cp .env.example .env
# Éditer .env avec vos paramètres (base de données, email, URL)
```

> ⚠️ Les variables d'environnement sont chargées via `getenv()`. Configurez-les dans votre serveur web ou via votre hébergeur.

### 3. Créer la base de données

```sql
CREATE DATABASE gestion_theatre;
CREATE USER theatre_user WITH PASSWORD 'votre_mdp';
GRANT ALL PRIVILEGES ON DATABASE gestion_theatre TO theatre_user;
```

---

## Migrations

Les migrations se trouvent dans `/database/migrations/`. Elles doivent être exécutées **dans l'ordre numérique** :

```bash
psql -U theatre_user -d gestion_theatre -f database/migrations/001_create_tables.sql
psql -U theatre_user -d gestion_theatre -f database/migrations/002_add_comments.sql
psql -U theatre_user -d gestion_theatre -f database/migrations/003_seed_admin.sql
```

Ou en une seule commande :

```bash
for f in database/migrations/*.sql; do
    echo "Applying $f..."
    psql -U theatre_user -d gestion_theatre -f "$f"
done
```

---

## Compte administrateur par défaut

Après la migration `003_seed_admin.sql` :

- **Identifiant** : `admin`
- **Mot de passe** : `admin`

> ⚠️ **Changez immédiatement ce mot de passe en production !**

Pour changer le mot de passe, générez un nouveau hash en PHP :

```php
echo password_hash('votre_nouveau_mot_de_passe', PASSWORD_BCRYPT);
```

Puis mettez à jour la base :

```sql
UPDATE admin_user SET password_hash = '...' WHERE username = 'admin';
```

---

## Configuration du serveur web

### Apache

Le fichier `public/.htaccess` est inclus. Assurez-vous que :
- `mod_rewrite` est activé
- `AllowOverride All` est configuré pour le dossier `public/`

La racine du virtual host doit pointer vers le dossier **`public/`** :

```apache
DocumentRoot /var/www/gestion-theatre/public
<Directory /var/www/gestion-theatre/public>
    AllowOverride All
    Require all granted
</Directory>
```

### Variables d'environnement (Apache)

Dans votre virtual host ou `.htaccess` :

```apache
SetEnv APP_ENV production
SetEnv APP_URL https://votre-domaine.fr
SetEnv DB_HOST localhost
SetEnv DB_NAME gestion_theatre
SetEnv DB_USER theatre_user
SetEnv DB_PASS votre_mdp
SetEnv MAIL_FROM noreply@votre-domaine.fr
```

---

## Déploiement

### Automatique (GitHub Actions)

Le workflow `.github/workflows/deploy.yml` se déclenche automatiquement :
- Push sur `main` → déploiement **production**
- Push sur `develop` → déploiement **test**

Configurer les **secrets GitHub** suivants dans votre dépôt :

| Secret | Description |
|--------|-------------|
| `SSH_HOST` | Adresse du serveur |
| `SSH_USER` | Utilisateur SSH |
| `SSH_KEY` | Clé privée SSH (contenu complet) |
| `DEPLOY_PATH_PROD` | Chemin absolu en production |
| `DEPLOY_PATH_DEV` | Chemin absolu en test/dev |

### Manuel

```bash
# Copier .env.deploy.example vers .env.deploy et remplir les valeurs
cp .env.example .env.deploy
# Éditer .env.deploy avec SSH_HOST, SSH_USER, SSH_KEY, DEPLOY_PATH_PROD/DEV

# Déploiement en test
./scripts/deploy.sh dev

# Déploiement en production
./scripts/deploy.sh production
```

---

## Sécurité

- ✅ Requêtes préparées PDO (protection SQL injection)
- ✅ `htmlspecialchars()` systématique dans les vues (protection XSS)
- ✅ Protection CSRF pour tous les formulaires admin
- ✅ Validation des formulaires côté serveur
- ✅ Hachage des mots de passe avec `password_hash()`/`password_verify()`
- ✅ Régénération de l'ID de session à la connexion
- ✅ Headers de sécurité HTTP (`.htaccess`)

---

## Structure des emails

Les emails sont envoyés avec la fonction PHP `mail()` via `Services/EmailService.php` :

- **Confirmation de réservation** : envoyé au spectateur avec le récapitulatif et le lien d'annulation
- **Annulation de réservation** : confirmation de l'annulation
- **Annulation de représentation** : notification à tous les spectateurs réservés

Pour utiliser un service SMTP tiers, modifiez `EmailService::sendMail()`.

---

## Développement

```bash
# Serveur de développement PHP intégré (depuis le dossier public/)
cd public && php -S localhost:8080

# Accès
# Site public : http://localhost:8080
# Admin       : http://localhost:8080/admin
```

---

## Licence

Projet privé – Tous droits réservés.
