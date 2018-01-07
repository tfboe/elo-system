#!/bin/bash
set -e
cd /opt/project
cd /tmp
rm -rf project-all
rm -rf project-clone
cp -r /opt/project project-all
cd project-all
git add .
git config user.email "automatic@testing.com"
git config user.name "Automatic Testing"
git commit -am "automatic commit"
cd /tmp
git clone project-all project-clone
cp -r project-all/vendor project-clone/vendor
cd project-clone

sed '/script:/,$!d;/script:/d;/codecov/d;s/^\s*-\s*//g;s/`/\\`/g' bitbucket-pipelines.yml |
while read command; do
    echo ${command}
    eval ${command}
done


# run unit tests again with full code coverage report
# vendor/bin/phpunit -c phpunit-unit.xml --coverage-php=/opt/project/storage/docker-testing/coverage-unit.cov \
#   --coverage-html=/opt/project/storage/docker-testing/coverage-unit
# run additionally integration tests
# vendor/bin/phpunit -c phpunit-integration.xml \
#   --coverage-php=/opt/project/storage/docker-testing/coverage-integration.cov \
#   --coverage-html=/opt/project/storage/docker-testing/coverage-integration

#unify coverage report
vendor/bin/phpcov -n merge --html=coverage-merged .
mv coverage.xml coverage-merged.xml
rm -rf /opt/project/storage/docker-testing
mkdir /opt/project/storage/docker-testing
mv coverage-unit unit.cov coverage-inte integration.cov coverage-merged coverage-merged.xml \
    /opt/project/storage/docker-testing
echo "Pipeline-Test was successful!"