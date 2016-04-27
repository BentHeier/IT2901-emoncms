 #!/usr/bin/env bash

# the db installation part of this provisioning file assumes that you have the following mapping on your vagrant file
# config.vm.synced_folder "./data", "/var/www", group: www-data, owner: www-data 
# and that you have both the virtualDevices and cossmic emoncms projects on your /data folder in the
# respective directories "virtualDevices" and "emoncms"

echo "updating the packages"
apt-get update

# differently from the raspberry pi installation, this bootstrap scriot does not
# install java, gradle, JSVC , RXTX  or the logging strategy used in the pi version


# for the physical devices driver
#echo "Installing java"
#apt-get install openjdk-7-jre librxtx-java -y

echo "Installing mecurial"
apt-get install mercurial build-essential ntp -y

# for the webserver itself
echo "Installing web server related packages"
apt-get install build-essential pwgen -y
MYSQL_PASSWORD=`pwgen -c -n -1 12`
echo mysql root password: $MYSQL_PASSWORD
echo $MYSQL_PASSWORD > /vagrant/mysql-root-pw.txt
echo "mysql-server mysql-server/root_password password $MYSQL_PASSWORD" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password $MYSQL_PASSWORD" | debconf-set-selections
apt-get install lighttpd php5 php5-cgi mysql-server mysql-client php5-mysql php5-curl php-pear php5-dev php5-mcrypt php5-json redis-server -y 


# packaged used in the docker installation for managing multiple processes
#apt-get install  -y supervisor

# installing the python packages
echo "Installing python"
apt-get install python-dev python-pip -y 
pip install --upgrade numpy
pip install pandas
sudo pip install pvlib
sudo pip install PyEphem
sudo pip install numexpr --upgrade
sudo pip install PyXML --upgrade
 
 echo "Installing utils"
# for fetching some packages
apt-get install wget unzip -y 

# handy but optional packages for the vm management
apt-get install vim less -y 

echo "Redis config"
# for redis
pear channel-discover pear.swiftmailer.org
pecl install channel://pecl.php.net/dio-0.0.6 redis swift/swift

#configuring the redis and php 
sh -c 'echo "extension=dio.so" > /etc/php5/cgi/conf.d/20-dio.ini'
sh -c 'echo "extension=dio.so" > /etc/php5/cli/conf.d/20-dio.ini'
sh -c 'echo "extension=redis.so" > /etc/php5/cgi/conf.d/20-redis.ini'
sh -c 'echo "extension=redis.so" > /etc/php5/cli/conf.d/20-redis.ini'

#enabling emoncms logging
pear channel-discover pear.apache.org/log4php
pear install log4php/Apache_log4php

echo "Lighttpd config"
# editing the lighttpd.conf
sed -i '/mod_rewrite/s/^#//' /etc/lighttpd/lighttpd.conf
# appending some settings
echo 'url.rewrite-if-not-file = (' >> /etc/lighttpd/lighttpd.conf
echo ' "^/emoncms/([^?]*)\?(.*)$" => "/emoncms/index.php?q=$1&$2",' >> /etc/lighttpd/lighttpd.conf
echo '"^/emoncms/([^?]*)$" => "/emoncms/index.php?q=$1",' >> /etc/lighttpd/lighttpd.conf
echo ')' >> /etc/lighttpd/lighttpd.conf

lighty-enable-mod fastcgi
lighty-enable-mod fastcgi-php

mkdir /var/run/lighttpd/
chown www-data /var/run/lighttpd/
# end of configuring lighttpd

## Create data repositories for emoncms feed engine's
echo "Emoncms config"
mkdir /var/lib/phpfiwa
mkdir /var/lib/phpfina
mkdir /var/lib/phptimeseries
mkdir /var/lib/phptimestore

chown www-data:root /var/lib/phpfiwa
chown www-data:root /var/lib/phpfina
chown www-data:root /var/lib/phptimeseries
chown www-data:root /var/lib/phptimestore


# dropbox installation
#echo "Dropbox installation"
#mkdir /home/www-data
#wget -O /home/www-data/dropbox.tar.gz "https://www.dropbox.com/download?plat=lnx.x86_64" 
#tar xvzf  /home/www-data/dropbox.tar.gz -C /home/www-data/
#sed -i -e '2iexport HOME=/home/www-data\' /home/www-data/.dropbox-dist/dropboxd
#mkdir /home/www-data/.dropbox
#mkdir /home/www-data/Dropbox
#chown -R www-data /home/www-data/

# db installation
echo "Db installation"
#mysql_install_db
#/usr/bin/mysqld_safe > /dev/null 2>&1 &
service mysql restart

RET=1
while [[ RET -ne 0 ]]; do
    echo "=> Waiting for confirmation of MySQL service startup"
    sleep 5
    mysql -uroot -p$MYSQL_PASSWORD  -e "status" > /dev/null 2>&1
    RET=$?
done


EMONCMS_DB_PASSWORD=`pwgen -c -n -1 12`
#This is so the passwords show up in logs, and are stored in the volume
echo emoncms db  password: $EMONCMS_DB_PASSWORD
echo $EMONCMS_DB_PASSWORD > /vagrant/mysql-emoncms-pw.txt

mysql -uroot -p$MYSQL_PASSWORD -e "CREATE USER 'emoncms'@'%' IDENTIFIED BY '$EMONCMS_DB_PASSWORD'"
mysql -uroot -p$MYSQL_PASSWORD -e "CREATE DATABASE emoncms;" 
mysql -uroot -p$MYSQL_PASSWORD -e "GRANT ALL PRIVILEGES ON emoncms.* TO 'emoncms'@'%' WITH GRANT OPTION;FLUSH PRIVILEGES;"

mysql -uroot -p$MYSQL_PASSWORD -e "CREATE DATABASE trials;"
mysql -uroot -p$MYSQL_PASSWORD -e "GRANT ALL PRIVILEGES ON trials.* TO 'emoncms'@'%' WITH GRANT OPTION;FLUSH PRIVILEGES;"

#it assumes that the mapping is done beforehand
unzip -p /var/www/virtualDevices/new_trials.sql.zip | mysql -uroot -p$MYSQL_PASSWORD  trials
mysqladmin -p$MYSQL_PASSWORD shutdown



