echo "This will set the file permissions for the game."
echo "It will effect all subdirectories."
read -p "Are you sure you wish to continue?"
if [ "$REPLY" != "yes" ]; then
   exit
fi

find . -exec sudo chown :www-data {} \;
find . -exec sudo chmod g+rw {} \;
find . -exec sudo chown root {} \;
