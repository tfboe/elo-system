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
read -s tfbw_password
echo

tfbw_user="ftp3521666_bene"
tfbw_host="ftp://www94.world4you.com"

echo -n TFBÖ FTP-Password:
read -s tfboe_password
echo

tfboe_user="199089-elo"
tfboe_host="ftp.tfboe.org"

git describe --tags > git-version/current

php artisan doctrine:generate:proxies

lftp <<EOF
open -u $tfbw_user,$tfbw_password -p 21 $tfbw_host
$(cat upload-code-tfboe.x)
EOF

apidoc -i routes/

lftp <<EOF
open -u $tfboe_user,$tfboe_password -p 21 $tfboe_host
$(cat upload-doc-tfboe.x)
EOF

./prepare-tfboe-vendor.sh

lftp <<EOF
open -u $tfbw_user,$tfbw_password -p 21 $tfbw_host
$(cat upload-vendor-tfboe.x)
EOF