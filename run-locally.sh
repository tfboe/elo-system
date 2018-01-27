#!/bin/bash

#use code coverage by default
export CODE_COVERAGE="${CODE_COVERAGE:-1}"
export SEND_TO_CODECOV="0"
export MYSQL_ROOT_PASSWORD="root"
export UNIT="1"
export INTEGRATION="1"
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

phpINIPath=~/.phpenv/versions/$(phpenv version-name)/etc
mkdir -p ${phpINIPath}
ln -s /etc/php/7.1/cli/php.ini ${phpINIPath}/php.ini

mv .env.test.local .env.test

(
./travis_before_script.sh
)
(
./travis.sh
)