#!/usr/bin/env bash

if [ $UID -ne 0 ]
then
  echo "You must run this script as root."
  exit
fi

echo "1) Setting permissions..."
find . -type d | xargs chmod 777
find . -type f | grep -v svn | xargs chmod 666
find Applications -type f | grep -v svn | xargs chmod 664
find System/Applications -type f | grep -v svn | xargs chmod 664
echo "Permissions updated."

echo "2) Checking for smartest CLI tool..."
# find . | grep -v svn | xargs chown www-data:admin
chmod 777 ./System/Install/Scripts/addclitool.php
php ./System/Install/Scripts/addclitool.php
echo "Smartest CLI tool installed."

echo "3) Checking for configuration files..."
chmod 777 ./System/Install/Scripts/config.php
php ./System/Install/Scripts/config.php
echo "Set up configuration files."

echo "4) Next up: Database configuration."
chmod 777 ./System/Install/Scripts/database.php
php ./System/Install/Scripts/database.php
echo "Set up database."

find System/Cache/Data -type f | grep -v svn | xargs chmod 666
find System/Cache/Includes -type f | grep -v svn | xargs chmod 666