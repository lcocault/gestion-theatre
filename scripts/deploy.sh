#!/usr/bin/env bash
# =============================================================================
# Script de déploiement manuel – Gestion Théâtre
# Usage : ./scripts/deploy.sh [production|dev]
# =============================================================================

set -euo pipefail

ENVIRONMENT="${1:-dev}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

# Chargement des variables d'environnement locales si disponibles
if [ -f "$ROOT_DIR/.env.deploy" ]; then
    # shellcheck disable=SC1091
    source "$ROOT_DIR/.env.deploy"
fi

# Validation des variables requises
: "${SSH_HOST:?Variable SSH_HOST non définie}"
: "${SSH_USER:?Variable SSH_USER non définie}"
: "${SSH_KEY:?Variable SSH_KEY non définie (chemin vers la clé privée)}"

if [ "$ENVIRONMENT" = "production" ]; then
    : "${DEPLOY_PATH_PROD:?Variable DEPLOY_PATH_PROD non définie}"
    DEPLOY_PATH="$DEPLOY_PATH_PROD"
    echo "🚀 Déploiement en PRODUCTION..."
else
    : "${DEPLOY_PATH_DEV:?Variable DEPLOY_PATH_DEV non définie}"
    DEPLOY_PATH="$DEPLOY_PATH_DEV"
    echo "🧪 Déploiement en environnement de TEST..."
fi

echo "  Hôte    : $SSH_HOST"
echo "  Chemin  : $DEPLOY_PATH"
echo "  Branche : $(git rev-parse --abbrev-ref HEAD)"

# Confirmation pour la production
if [ "$ENVIRONMENT" = "production" ]; then
    read -r -p "⚠️  Confirmer le déploiement en production ? [y/N] " confirm
    if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
        echo "Annulé."
        exit 0
    fi
fi

# Déploiement via rsync
rsync -avz --delete \
    --exclude='.git/' \
    --exclude='.github/' \
    --exclude='*.env' \
    --exclude='.env*' \
    --exclude='.env.deploy' \
    --exclude='config/.admin_password' \
    --exclude='node_modules/' \
    -e "ssh -i $SSH_KEY -o StrictHostKeyChecking=no" \
    "$ROOT_DIR/" \
    "$SSH_USER@$SSH_HOST:$DEPLOY_PATH"

echo "✅ Déploiement terminé !"
