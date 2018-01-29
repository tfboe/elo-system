#!/bin/bash
set -e
#set environment default values
CODE_COVERAGE="${CODE_COVERAGE:-0}"
SEND_TO_CODECOV="${SEND_TO_CODECOV:-1}"
INTEGRATION="${INTEGRATION:-0}"
UNIT="${UNIT:-0}"
HTML_COVERAGE="${HTML_COVERAGE:0}"

if [ "$UNIT" = '1' ]; then
    #run unit tests
    echo "Run unit tests"
    args=""
    if [ "$HTML_COVERAGE" = '1' ]; then
        args=--coverage-html=coverage-unit
    fi
    vendor/bin/phpunit --stderr --configuration phpunit-unit.xml --coverage-php=unit.cov ${args}
fi

if [ "$INTEGRATION" = '1' ]; then
    #run integration tests
    echo "Run integration tests"
    args=""
    if [ "$HTML_COVERAGE" = '1' ]; then
        args=--coverage-html=coverage-integration
    fi
    vendor/bin/phpunit --stderr --configuration phpunit-integration.xml --coverage-php=integration.cov ${args}
fi

if [ "$CODE_COVERAGE" = "1" ]; then
    #merge all reports to one code coverage report
    args=""
    if [ "$HTML_COVERAGE" = '1' ]; then
        args=--html=coverage-merged
    fi
    vendor/bin/phpcov -n merge --clover=coverage.xml ${args} .
    if [ "$SEND_TO_CODECOV" = '1' ]; then
        if [ $? -eq 0 ]; then bash <(curl -s https://codecov.io/bash); fi
    else
        #output coverage file
        cat coverage.xml
    fi
fi