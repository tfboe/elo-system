mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./app /tfboe-elo/app
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./bootstrap /tfboe-elo/bootstrap
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./config /tfboe-elo/config
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./git-version /tfboe-elo/git-version
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./public /tfboe-elo/public
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./resources /tfboe-elo/resources
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./routes /tfboe-elo/routes
mirror --verbose=1 -c -e -R --no-symlinks --upload-older -f ./composer.json -O /tfboe-elo
mirror --verbose=1 -c -e -R --no-symlinks --upload-older -f ./composer.lock -O /tfboe-elo
exit
