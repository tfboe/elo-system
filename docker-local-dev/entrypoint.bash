echo "##################################"
echo "####### ENTRYPOINT.bash ##########"
echo "##################################"

#configure git
su $USER -c "git config --global user.name \"$GIT_NAME\" && git config --global user.email \"$GIT_EMAIL\""

echo "# serve workspace on port 8000"
su $USER -c "php -S 0.0.0.0:8000 -t /workspace/public" &
echo "# start vscodium as $USER"
su $USER -c "/usr/bin/codium -w --user-data-dir /userdata /workspace"
