#!/usr/bin/env bash

if [ $UID -ne 0 ]
then
  echo "Must be root in order to run this script"
  exit
fi


# find . | grep -v svn | xargs chown www-data:admin

find . -type d | xargs chmod 777
find . -type f | grep -v svn | xargs chmod 666
find Applications -type f | grep -v svn | xargs chmod 464
find System/Applications -type f | grep -v svn | xargs chmod 464