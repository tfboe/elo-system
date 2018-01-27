#!/bin/bash

#set environment default values
CODE_COVERAGE="${CODE_COVERAGE:-0}"
# by default no mysql root password
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-}"
INTEGRATION="${INTEGRATION:-0}"
GITHUB_OAUTH="${GITHUB_OAUTH:-}"
WITH_LOCK="${WITH_LOCK:-0}"

if [ "$WITH_LOCK" == "0" ]; then
    rm composer.lock
fi

if [ "$GITHUB_OAUTH" != "" ]; then
    echo "using github OAUTH"
    composer config --global -g github-oauth.github.com ${GITHUB_OAUTH}
fi

# see https://github.com/composer/composer/issues/1314
composer install --prefer-dist --no-interaction

if [ "$INTEGRATION" = '1' ]; then
    # create test database
    if [ "$MYSQL_ROOT_PASSWORD" = "" ]; then
        # don't use a password
        mysql -h localhost -u root -e "CREATE DATABASE \`elo-system-test\`"
    else
        mysql -h localhost -u root -p${MYSQL_ROOT_PASSWORD} -e "CREATE DATABASE \`elo-system-test\`"
    fi

    #create testing environment
    cp .env.test .env

    # generate doctrine tables and proxies
    php artisan doctrine:migrations:migrate
    php artisan doctrine:generate:proxies
fi