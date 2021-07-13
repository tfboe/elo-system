set ssl:verify-certificate no
mirror --verbose=1 -c -e -R --no-symlinks --upload-older ./doc /doc
exit
