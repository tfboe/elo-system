#!/bin/bash

#set environment default values
CODE_COVERAGE="${CODE_COVERAGE:-0}"
SEND_TO_CODECOV="${SEND_TO_CODECOV:-1}"
INTEGRATION="${INTEGRATION:-0}"
UNIT="${UNIT:-0}"

if [ "$UNIT" = '1' ]; then
    #run unit tests
    echo "Run unit tests"
    vendor/bin/phpunit --stderr --configuration phpunit-unit.xml --coverage-php=unit.cov
fi

if [ "$INTEGRATION" = '1' ]; then
    #run integration tests
    echo "Run integration tests"
    vendor/bin/phpunit --stderr --configuration phpunit-integration.xml --coverage-php=integration.cov
fi

if [ "$CODE_COVERAGE" = "1" ]; then
    #merge all reports to one code coverage report
    vendor/bin/phpcov -n merge --clover=coverage.xml .
    if [ "$SEND_TO_CODECOV" = '1' ]; then
        if [ $? -eq 0 ]; then bash <(curl -s https://codecov.io/bash); fi
    else
        #output coverage file
        cat coverage.xml
    fi
fi