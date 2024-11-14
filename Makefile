run:
	docker compose run --rm --entrypoint $(CMD) php

test-all: testsuite-Sqlite \
	testsuite-Mysql \
	testsuite-Pgsql \
	testsuite-Mssql

testsuite-%:
	docker compose run \
	--rm \
	--entrypoint "vendor/bin/phpunit --testsuite $(subst testsuite-,,$@)" \
	php

#test-oracle: CMD="vendor/bin/phpunit --testsuite Oracle --filter testOffset tests/Oracle/QueryDataReaderTest.php"
#test-oracle: run

static-analysis: CMD="vendor/bin/psalm --no-cache"
static-analysis: run

mutation: CMD="\
vendor/bin/roave-infection-static-analysis-plugin \
--threads=2 \
--min-msi=0 \
--min-covered-msi=100 \
--ignore-msi-with-no-mutations \
--only-covered"
mutation: run

composer-require-checker: CMD="vendor/bin/composer-require-checker"
composer-require-checker: run

shell: CMD="bash"
shell: run
