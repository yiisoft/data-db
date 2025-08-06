.PHONY: help
help: ## Show the list of available commands with description.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
.DEFAULT_GOAL := help

build: ## Build services
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml build
up: ## Start services
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml up -d --remove-orphans
build-up: # Build and start services
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml up -d --remove-orphans --build
ps: ## List running services
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml ps
stop: ## Stop running services
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml stop
down: ## Stop running services and remove all services (not defined services, containers, networks, volumes, images)
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml down \
	--remove-orphans \
	--volumes \
	--rmi all

run: ## Run arbitrary command
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml run \
	--rm \
	--entrypoint $(CMD) \
	php

test-all: test-sqlite \
	test-mysql \
	test-pgsql \
	test-mssql \
	test-oracle
test-sqlite: testsuite-Sqlite
test-mysql: testsuite-Mysql
test-pgsql: testsuite-Pgsql
test-mssql: testsuite-Mssql
test-oracle:
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml run \
	--rm \
	--entrypoint "bash -c -l 'vendor/bin/phpunit --testsuite Oracle $(RUN_ARGS)'" \
	php

testsuite-%:
	docker compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml run \
	--rm \
	--entrypoint "vendor/bin/phpunit --testsuite $(subst testsuite-,,$@) $(RUN_ARGS)" \
	php

psalm: CMD="vendor/bin/psalm --no-cache" ## Run static analysis using Psalm
psalm: run

mutation: CMD="\
vendor/bin/roave-infection-static-analysis-plugin \
--threads=2 \
--min-msi=0 \
--min-covered-msi=100 \
--ignore-msi-with-no-mutations \
--only-covered" ## Run mutation tests using Infection
mutation: run

composer-require-checker: CMD="vendor/bin/composer-require-checker" ## Check dependencies using Composer Require Checker
composer-require-checker: run

rector: CMD="vendor/bin/rector" ## Check code style using Rector
rector: run

shell: CMD="bash" ## Open interactive shell
shell: run
