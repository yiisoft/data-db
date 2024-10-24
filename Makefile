test-all: vendor/bin/phpunit --testsuite Sqlite,Mysql,Pgsql,Mssql,Oracle
test-mysql: vendor/bin/phpunit --testsuite Mysql
test-pgsql: vendor/bin/phpunit --testsuite Pgsql
test-mssql: vendor/bin/phpunit --testsuite Mssql
test-oracle: vendor/bin/phpunit --testsuite Oracle
test-temp: vendor/bin/phpunit --testsuite Mysql --filter testOffset tests/Mysql/QueryDataReaderTest.php
