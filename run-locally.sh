#!/bin/bash
set -e

DOCKER_CACHE_COMPOSER="${DOCKER_CACHE_COMPOSER:-0}"

#use code coverage by default
export CODE_COVERAGE="${CODE_COVERAGE:-1}"
export SEND_TO_CODECOV="0"
export MYSQL_ROOT_PASSWORD="root"
export UNIT="1"
export INTEGRATION="1"
export HTML_COVERAGE="1"
rm -rf /tmp/full-src
rm -rf /tmp/test-src
cp -r $(dirname "$0") /tmp/full-src
cd /tmp/full-src
git checkout -b "testing-branch"
git add .
git config user.email "automatic@testing.com"
git config user.name "Automatic Testing"
git commit -am "automatic commit"
cd /tmp
git clone full-src test-src
cd test-src
service mysql start

#phpINIPath=~/.phpenv/versions/$(phpenv version-name)/etc
#mkdir -p ${phpINIPath}
#ln -s /etc/php/7.1/cli/php.ini ${phpINIPath}/php.ini

mv .env.test.local .env.test

if [ "$DOCKER_CACHE_COMPOSER" != '0' ]; then
    #caching composer cache directory
    composer_cache="`composer config --absolute cache-dir`/files"
    mkdir -p ${DOCKER_CACHE_COMPOSER}
    tar_file=${DOCKER_CACHE_COMPOSER}/elo-system-test-cache.tar
    if [ -f ${tar_file} ]; then
        echo "using composer cache"
        tar -xf ${tar_file} -C ${composer_cache}
    fi
fi


(
./travis_before_script.sh
)
(
./travis.sh
)

rm -rf /opt/project-storage/*
mv coverage.xml coverage-merged.xml
mv coverage-unit unit.cov coverage-integration integration.cov coverage-merged coverage-merged.xml /opt/project-storage/

if [ "$DOCKER_CACHE_COMPOSER" != '0' ]; then
    (
    #store vendor files
    cd ${composer_cache}
    if [ -f ${tar_file} ]; then
        mv ${tar_file} ${tar_file}.old
    fi
    tar -cf ${tar_file} .
    )
fi