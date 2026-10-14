.PHONY: help up down restart build logs migrate seed fresh bash test

COMPOSE := $(shell which docker-compose 2>/dev/null || echo "docker compose")

help: ## Afficher l'aide des commandes disponibles
	@echo "Cypress Pay Côte d'Ivoire - Commandes utiles"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

up: ## Démarrer tous les services en arrière-plan
	$(COMPOSE) up -d

down: ## Arrêter tous les services
	$(COMPOSE) down

restart: down up ## Redémarrer l'ensemble des conteneurs

build: ## Reconstruire les images Docker
	$(COMPOSE) build

logs: ## Afficher les logs en temps réel
	$(COMPOSE) logs -f

migrate: ## Exécuter les migrations de base de données
	$(COMPOSE) exec app php artisan migrate

seed: ## Exécuter les seeders (produits, formules, kits)
	$(COMPOSE) exec app php artisan db:seed

fresh: ## Réinitialiser la base de données et recharger tous les seeders
	$(COMPOSE) exec app php artisan migrate:fresh --seed

bash: ## Ouvrir un terminal bash dans le conteneur PHP
	$(COMPOSE) exec app sh

test: ## Exécuter la suite de tests
	$(COMPOSE) exec app php artisan test
