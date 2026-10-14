#!/usr/bin/env bash
# ==============================================================================
# Cypress Pay Côte d'Ivoire - Démarrage Tout-en-Un Docker & Laravel
# ==============================================================================

set -e

echo "🚀 [Cypress Pay CI] Initialisation de la plateforme E-commerce BNPL..."

# 1. Vérification du fichier .env
if [ ! -f .env ]; then
    echo "📋 Création du fichier .env à partir de .env.example..."
    cp .env.example .env
fi

# 2. Vérification de Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Erreur: Docker n'est pas installé sur ce système."
    exit 1
fi

if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ Erreur: docker-compose n'est pas disponible."
    exit 1
fi

COMPOSE_CMD="docker compose"
if ! docker compose version &> /dev/null; then
    COMPOSE_CMD="docker-compose"
fi

# 3. Démarrage des conteneurs (Nginx, PHP-FPM 8.3, MySQL 8.0, Redis 7.0, Queue Worker)
echo "🐳 Lancement des conteneurs Docker..."
$COMPOSE_CMD up -d --build

# 4. Attente de la disponibilité de MySQL
echo "⏳ Attente de la disponibilité de MySQL 8.0..."
until $COMPOSE_CMD exec -T db mysqladmin ping -h "localhost" -u"root" -p"root_cypress_password" --silent; do
    echo "   En attente de la base de données..."
    sleep 2
done
echo "✅ MySQL est prêt !"

# 5. Préparation des répertoires de stockage & permissions
echo "📁 Préparation des répertoires storage et cache..."
$COMPOSE_CMD exec -T app mkdir -p storage/app/public \
                                   storage/framework/cache/data \
                                   storage/framework/sessions \
                                   storage/framework/views \
                                   storage/logs \
                                   bootstrap/cache
$COMPOSE_CMD exec -T app chmod -R 777 storage bootstrap/cache

# 6. Dépendances PHP & Génération de Clé d'application
echo "📦 Installation / vérification des dépendances Composer..."
$COMPOSE_CMD exec -T app composer install --optimize-autoloader --no-interaction

echo "🔑 Génération de la clé d'application Laravel..."
$COMPOSE_CMD exec -T app php artisan key:generate --force

# 6. Exécution des migrations & seeders (Kits, Formules BNPL, Produits, Utilisateurs)
echo "🗄️ Exécution des migrations et chargement du catalogue CI..."
$COMPOSE_CMD exec -T app php artisan migrate:fresh --seed --force

# 7. Lien de stockage
$COMPOSE_CMD exec -T app php artisan storage:link || true

echo "=============================================================================="
echo "🎉 Cypress Pay CI est en ligne avec succès !"
echo "🌐 URL Principale      : http://localhost:8080"
echo "🛒 Catalogue & Kits    : http://localhost:8080/catalogue"
echo "⚡ Ventes Flash (-7%)  : http://localhost:8080/flash-ventes"
echo "🧮 Simulateur BNPL     : http://localhost:8080/simulateur"
echo "🔍 Vérificateur Reçus  : http://localhost:8080/verifier"
echo "👤 Espace Client       : http://localhost:8080/client/dashboard"
echo "💼 Espace Commercial   : http://localhost:8080/commercial/dashboard"
echo "🏪 Espace Caisse       : http://localhost:8080/caisse/dashboard"
echo "=============================================================================="
