ifndef APP_ENV
	include .env
endif

user := $(shell id -u)
group := $(shell id -g)

DOCKER_COMPOSE := USER_ID=$(user) GROUP_ID=$(group) docker-compose -f docker-compose.yml

EXEC_DB        := $(DOCKER_COMPOSE) exec db mysql
EXEC_PHP       := $(DOCKER_COMPOSE) exec php


.DEFAULT_GOAL := help
.PHONY: help
help: ## Affiche cette aide
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z0-9_-]+:.*?##/ { printf "  \033[36m%-27s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

dev: start seed

.PHONY: start
start: ## Lance le serveur de développement
	$(DOCKER_COMPOSE) up -d --remove-orphans --build
	yarn dev-server


.PHONY: down
down: ## destroy images and containers created
	$(info Make: Destroy images and containers created.)
	$(DOCKER_COMPOSE) down --remove-orphans --volume

restart: down start

.PHONY: build
build: ## Download the latest versions of the pre-built images & Building images in without cache
	$(info Make: Building images in without cache.)
	$(DOCKER_COMPOSE) pull --ignore-pull-failures
	$(DOCKER_COMPOSE) build --force-rm


.PHONY: ps
ps: ## Lists containers
	$(info Make: Lists containers.)
	$(DOCKER_COMPOSE) ps


.PHONY: logs
logs: ## Displays log output from services
	$(info Make: Displays log output from services.)
	$(DOCKER_COMPOSE) logs


.PHONY: kill
kill: ## Forces running containers to stop
	$(info Make: Forces running containers to stop.)
	$(DOCKER_COMPOSE) kill -s SIGKILL


.PHONY: remove
remove: ## Removes stopped service containers
	$(info Make: Removes stopped service containers.)
	$(DOCKER_COMPOSE) rm --stop --force

.PHONY: db
db: ## exec nginx in bash mode
	$(info Make: Exec mysql db in bash mode.)
	$(EXEC_DB) -u ${MYSQL_ROOT_PASSWORD} -p

.PHONY: php
php: ## connect into the container
	$(EXEC_PHP) bash

.PHONY: seed
seed:
	$(EXEC_PHP) php bin/console doctrine:database:drop --if-exists --force
	$(EXEC_PHP) php bin/console doctrine:database:create
	$(EXEC_PHP) php bin/console doctrine:migrations:migrate -q
	${EXEC_PHP} php bin/console doctrine:schema:validate -q
	#$(EXEC_PHP) php bin/console hautelook:fixtures:load -q --no-bundles

test:
	$(EXEC_PHP) vendor/bin/simple-phpunit