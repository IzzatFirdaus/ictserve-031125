.PHONY: help setup build up stop restart composer npm artisan migrate seed test logs shell legacy

help:
	@echo "ICTServe Docker Commands (using compose.yaml):"
	@echo "  make setup          - Build and start containers, install dependencies"
	@echo "  make build          - Build Docker images"
	@echo "  make up             - Start containers"
	@echo "  make stop           - Stop containers"
	@echo "  make restart        - Restart containers"
	@echo "  make composer       - Run composer install"
	@echo "  make npm            - Run npm install"
	@echo "  make artisan cmd=X  - Run artisan command (e.g., make artisan cmd=migrate)"
	@echo "  make migrate        - Run database migrations"
	@echo "  make seed           - Run database seeders"
	@echo "  make test           - Run PHPUnit tests"
	@echo "  make logs           - Show container logs"
	@echo "  make shell          - Open shell in app container"
	@echo "  make legacy         - Use legacy docker-compose.yml"

setup:
	@make build
	@make up
	@make composer
	@make npm
	@echo "Setup complete! Run 'make migrate' to initialize database."

build:
	docker compose -f compose.yaml build --no-cache --force-rm

up:
	docker compose -f compose.yaml up -d

stop:
	docker compose -f compose.yaml stop

restart:
	@make stop
	@make up

composer:
	docker compose -f compose.yaml exec app composer install --no-interaction

npm:
	docker compose -f compose.yaml exec app npm ci

artisan:
	docker compose -f compose.yaml exec app php artisan $(cmd)

migrate:
	docker compose -f compose.yaml exec app php artisan migrate

seed:
	docker compose -f compose.yaml exec app php artisan db:seed

test:
	docker compose -f compose.yaml exec app php artisan test

logs:
	docker compose -f compose.yaml logs -f app

shell:
	docker compose -f compose.yaml exec app sh

legacy:
	@echo "Using legacy docker-compose.yml..."
	docker compose -f docker-compose.yml up -d
