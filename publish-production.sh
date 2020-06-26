#!/bin/bash
set -e

if [ "$(git rev-parse --abbrev-ref HEAD)" != "master" ]; then
  echo 1>&2 "Publishing Production is only allowed on master branch!"
  exit 1
fi

if [ ! -z "$(git status --porcelain)" ]; then
   echo 1>&2 "Uncommited git changes are not allowed!"
   exit 1
fi

echo -n FTP-Password:
read -s password
echo

user="ftp3521666_bene"
host="ftp://www94.world4you.com"

git describe --tags > git-version/current

lftp <<EOF
open -u $user,$password -p 21 $host
$(cat upload-code-production.x)
EOF

./prepare-production-vendor.sh

lftp <<EOF
open -u $user,$password -p 21 $host
$(cat upload-vendor-production.x)
EOF