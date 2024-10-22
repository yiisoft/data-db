test-all: vendor/bin/phpunit --testsuite Sqlite,Mysql,Pgsql,Mssql,Oracle
test-oracle: vendor/bin/phpunit --testsuite Oracle
