#!/bin/sh

# app_admin 
# Usage: ./app_admin { start | stop | restart }
# app_admin is a management script for gunicorn_django.
# It is designed to work on the WebFaction platform with minimal effort.
# The script requires gunicorn installed and enabled within your apps INSTALLED_APPS setting. 
# See http://gunicorn.org/ for instructions on gunicorn_django's use and installtion.

# The servers IP adderss, this should be 127.0.0.1. 

export DROPBOXDIR=/home/www-data/Dropbox/cossmicprototype

python /var/www/emoncms/Modules/mas/bin/dropboxcp.py $1
