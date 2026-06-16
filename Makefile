.DEFAULT_GOAL := help

help: ## lista os comandos disponíveis
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

start: up wait-back test migrate ## sobe a aplicação, roda os testes e aplica as migrations
	@echo "aplicação no ar: http://localhost:5173"

up: ## sobe a aplicação (builda na primeira vez)
	docker compose up -d --build

install-local: ## instala o node_modules do frontend no host (só para o editor/IDE; opcional)
	cd frontend && npm install
	@echo "dependências locais do frontend instaladas (suporte ao editor)"

wait-back: ## aguarda o backend instalar as dependências do Composer
	@echo "aguardando o backend instalar dependências..."
	@until docker compose exec -T backend test -f .composer-ready 2>/dev/null; do sleep 2; done
	@echo "backend pronto"

stop: ## pausa os containers
	docker compose stop

down: ## remove os containers (dados do banco ficam)
	docker compose down

clean: ## remove tudo, inclusive os dados do banco
	docker compose down -v

ps: ## status dos containers
	docker compose ps

logs: ## logs do backend em tempo real
	docker compose logs -f backend

test: test-back test-front ## roda todas as suítes (backend + frontend)

test-back: ## roda os testes do backend (PHPUnit)
	docker compose exec backend php bin/phpunit

test-front: ## roda os testes do frontend (Vitest)
	docker compose exec frontend npm run test

testdox: ## testes do backend com saída detalhada
	docker compose exec backend php bin/phpunit --testdox

migrate: ## aplica as migrations
	docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

db-validate: ## confere se entidades e banco estão em sincronia
	docker compose exec backend php bin/console doctrine:schema:validate

db-reset: ## recria o banco do zero (apaga todos os dados)
	docker compose exec backend php bin/console doctrine:schema:drop --full-database --force
	docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

psql: ## abre o psql no banco
	docker compose exec database psql -U app -d app
