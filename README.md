Our system is based on an existing system called Emoncms, on their webpage https://emoncms.org/ it says:

_“Emoncms is a powerful open-source web-app for processing, logging and visualising energy, temperature and other environmental data.”_

This chapter describes how to install the Emoncms web application in a virtual environment. It also provides a list of all the files we've modified from the CoSSMic version of Emoncms.

The Emoncms program is made to run on Raspbian and thus it works on Debian. 
It is a lightweight web application made to run full time on a Raspberry Pi. 
We have only made minor adjustments to the Emoncms software, and are thus using the same installation procedure provided to us by SINTEF, running a Vagrant virtual machine powered by VirtualBox from Oracle.
Note that you can install the system on a Debian or Raspbian machine, real or virtual, just follow the steps in bootstrap.sh manually. 
For this tutorial we assume that you are using Ubuntu as the host operating system, but it should be possible to use any operating system that is supported by Vagrant as the host system. 
In our development we used Mac OSX 10.11 and Ubuntu 16.04 LTS as the host operating system for the Vagrant virtual machine, in particular we have never tested our version of Emoncms on any version of Windows. 

## Installation guide (Ubuntu 16.04 as host):

Lines starting with dollar signs like

``$this``

are console commands to be typed in a terminal emulator. 

First, make a directory and place all files from our Emoncms folder there. 
In this guide we assume that this directory is ~/emoncms

``($mkdir ~/emoncms)``

Install VirtualBox

``$sudo apt install dpkg-dev virtualbox-dkms``

Install Vagrant 

``$sudo apt install vagrant``

Change directory and start building the Virtual Machine

``$cd ~/emoncms && vagrant up ``

and wait until it finishes. 

This will use the Vagrantfile and bootstrap.sh to install a Debian 7.9 Vagrant virtual machine with no GUI and all the necessary programs and set most of the settings necessary settings for Emoncms to work. 
It will also link ~emoncms/data on the host system with /var/www in the Vagrant virtual machine. 

If you get an error message saying that your UID is different from the UID used to create the virtual machine open the  ~vagrant/.vagrant\machines\default\virtualbox\creator_uid file by typing 

``$nano ~/emoncms/.vagrant\machines\default\virtualbox\creator_uid``

and changing the UID to the appropriate UID, that is change it to your own, you can find it by typing 

``$id -u username``

where username is the username of the current user. 

You should now reboot the Vagrant VM by typing 

``$vagrant reload``

IMPORTANT! 
If you reboot the Vagrant VM with the $shutdown or $reboot commands from inside the VM it will not retain the files placed in ~/emoncms/data in /var/www and it will have to be reloaded. 

To get access to the Vagrant virtual machine you can either type 

``$vagrant ssh``

to get SSH access or open the VirtualBox GUI and double click on the running machine, this will open the machine as a shell, the username and password are both vagrant by default. Note that there is no GUI either way. 

For security reasons the password used in the mySQL database is generated for each installation and thus you need to change the password in the settings.php file.
Inside the Vagrant VM type 

``$sudo nano /var/www/emoncms/settings.php``

The $username variable should be set to emoncms, not root (should be emoncms by default).

Change the $password variable to the password set during setup. This password is stored in /vagrant/mysql-emoncms-pw.txt on the Vagrant virtual machine and can be viewed by typing 

``$nano /vagrant/mysql-emoncms-pw.txt``

On the host system open a web browser and go to localhost:4580/emoncms

If you get an error message saying 

__Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' (2)__

try reloading the Vagrant VM once more, if that doesn't do the trick you have to remove a configuration file, this can be done by typing
 
``$sudo mv /var/lib/mysql/my.cnf /var/lib/mysql/my.cnf.bak``

Now you must restart the webserver, do this by typing 

``$sudo /etc/init.d/lighttpd restart``

Now reload the webpage and it should work. 
If it still does not work try another reload of the virtual machine machine. 

For now the registration on the web site is only local and you can put in any valid email address and password. It will only be saved as a local hash. Since you likely don’t have any devices connected most of the application will not be very interesting to look at. The sensors and data gathering is outside of our scope for this project so we will not go into how to install those. 


## Modifications
Below is an exhaustive list of all the modifications we made to the emoncms repository. Both the files we modified, as well as the files we added.

#### Files Modified

- Modules/cossmiccontrol/Views/summary.php
- Modules/cossmiccontrol/Views/ranking.php
- Modules/user/profile/profile.php
- Modules/cossmicranking/cossmicranking_menu.php

#### Files added

- Modules/gamification/Artwork/Achievements/achievement1.jpg
- Modules/gamification/Artwork/Achievements/achievement2.jpg
- Modules/gamification/Artwork/Achievements/achievement3.jpg
- Modules/gamification/Artwork/Achievements/achievement4.jpg
- Modules/gamification/Artwork/Achievements/achievement5.jpg
- Modules/gamification/Artwork/Achievements/achievement6.jpg
- Modules/gamification/Artwork/Achievements/achievement7.jpg
- Modules/gamification/Artwork/Achievements/achievement8.jpg
- Modules/gamification/Artwork/Achievements/achievement9.jpg
- Modules/gamification/Artwork/Achievements/achievement10.jpg
- Modules/gamification/Artwork/Achievements/achievement11.jpg
- Modules/gamification/Artwork/Achievements/achievementIcons.jpg
- Modules/gamification/Artwork/Rank/rank1.png
- Modules/gamification/Artwork/Rank/rank2.png
- Modules/gamification/Artwork/Rank/rank3.png
- Modules/gamification/Artwork/Rank/rank4.png
- Modules/gamification/Artwork/Rank/rank5.png
- Modules/gamification/Artwork/Rank/rank6.png
- Modules/gamification/gamification-config.js
- Modules/gamification/Ranking/rank_progress.css
- Modules/gamification/Ranking/rank_progress.js
- Modules/gamification/Ranking/ranking.css
- Modules/gamification/Ranking/ranking.js
- Modules/gamification/Third-party/circle-progress.js
- Modules/gamification/Third-party/LICENSE
- Modules/gamification/Third-party/segmentedControl.css
- Modules/gamification/Widget/widget.css
- Modules/gamification/Widget/widget.js
