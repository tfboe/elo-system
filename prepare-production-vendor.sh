#!/bin/bash
set -e

if [ -d "vendor" ] && [ -d "vendor-dev" ] && [ ! -d "vendor-dist" ]; then
  mv vendor vendor-dist
  mv vendor-dev vendor
elif [ ! -d "vendor" ] || [ ! -d "vendor-dist" ]; then
  echo "vendor directory structure not fitting!"
  exit 1
fi

composer validate --no-check-all --strict

mv vendor vendor-dev
mv vendor-dist vendor
composer install --no-dev --no-autoloader
composer dump-autoload --classmap-authoritative
mv vendor vendor-dist
mv vendor-dev vendor