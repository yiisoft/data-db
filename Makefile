run:
	docker compose run --rm --entrypoint $(CMD) php

test-all: CMD="vendor/bin/phpunit --testsuite Sqlite,Mysql,Pgsql,Mssql,Oracle"
test-all: run

test-sqlite: CMD="vendor/bin/phpunit --testsuite Sqlite"
test-sqlite: run

test-mysql: CMD="vendor/bin/phpunit --testsuite Mysql"
test-mysql: run

test-pgsql: CMD="vendor/bin/phpunit --testsuite Pgsql"
test-pgsql: run

test-mssql: CMD="vendor/bin/phpunit --testsuite Mssql"
test-mssql: run

test-oracle: CMD="vendor/bin/phpunit --testsuite Oracle --filter testOffset tests/Oracle/QueryDataReaderTest.php"
test-oracle: run

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
