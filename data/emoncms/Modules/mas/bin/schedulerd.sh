#!/bin/sh

# app_admin 
# Usage: ./app_admin { start | stop | restart }
# app_admin is a management script for gunicorn_django.
# It is designed to work on the WebFaction platform with minimal effort.
# The script requires gunicorn installed and enabled within your apps INSTALLED_APPS setting. 
# See http://gunicorn.org/ for instructions on gunicorn_django's use and installtion.



# Activate virtual environment. These 2 lines may be disabled/deleted if you do not use virtualenv.
#export APIKEY="e7b6a0cf74718df54d98357499cae63e"
#export USERNAME="salvatore"

#if started from mas it should be $3
export APIKEY=$3


#if started from mas it should be $2 
export USERNAME=$2
export DROPBOXDIR="/home/www-data/Dropbox/cossmicprototype"

python /var/www/emoncms/Modules/mas/bin/schedulerd.py $1 
