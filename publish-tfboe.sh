#!/bin/bash
set -e

if [ "$(git rev-parse --abbrev-ref HEAD)" != "tfboe" ]; then
  echo 1>&2 "Publishing TFBOE is only allowed on tfboe branch!"
  exit 1
fi

if [ ! -z "$(git status --porcelain)" ]; then
   echo 1>&2 "Uncommited git changes are not allowed!"
   exit 1
fi

echo -n TFBW FTP-Password:
read -s password
echo

user="ftp3521666_bene"
host="ftp://www94.world4you.com"

git describe --tags > git-version/current

php artisan doctrine:generate:proxies

lftp <<EOF
open -u $user,$password -p 21 $host
$(cat upload-code-tfboe.x)
EOF

./prepare-tfboe-vendor.sh

lftp <<EOF
open -u $user,$password -p 21 $host
$(cat upload-vendor-tfboe.x)
EOF

echo TFBÖ FTP-Password:
read -s password
echo

user="ftp.tfboe.org"
host="199089-elo"

apidoc -i routes/

lftp <<EOF
open -u $user,$password -p 21 $host
$(cat upload-doc-tfboe.x)
EOF