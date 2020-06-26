mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./app /elo/backend/app
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./bootstrap /elo/backend/bootstrap
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./config /elo/backend/config
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./git-version /elo/backend/git-version
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./public /elo/backend/public
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./resources /elo/backend/resources
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./routes /elo/backend/routes
mirror --verbose=1 -c -e -R --no-symlinks --upload-older -f ./composer.json -O /elo/backend
mirror --verbose=1 -c -e -R --no-symlinks --upload-older -f ./composer.lock -O /elo/backend
exit
