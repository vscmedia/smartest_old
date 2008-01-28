#!/usr/bin/env bash

if [ $UID -ne 0 ]
then
  echo "You must run this script as root."
  exit
fi

echo "Emptying cache..."
find System/Cache/Data -type f | grep -v svn | xargs rm -rf
find System/Cache/Includes -type f | grep -v svn | xargs rm -rf

echo "Deleting files..."
rm -rf Configuration/controller.xml
rm -rf Configuration/database.ini
rm -rf Configuration/general.ini
rm -rf Configuration/options.ini
rm -rf Configuration/smarty.ini
rm -rf Configuration/system.ini
rm -rf Configuration/units.ini
rm -rf Presentation/Masters/default.tpl
rm -rf Public/.htaccess