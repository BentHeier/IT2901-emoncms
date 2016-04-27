# Emoncms 8 CoSSMic

This is the emoncms as we intend to use it in CoSSMic.

It bases on the emoncms version from 01.10.2014.

See the [Wiki](https://bitbucket.org/cossmic/emoncms/wiki) for details!

### What is this repository for? ###

* This repository hosts the extended version of Emoncms that serves as mediator in the CoSSMic framework
* version 0.1

### How it extends Emoncms ###
* Its add a new Emoncms Module called Driver. This module allow to install, instantiate and use different drivers to control heterogeneous real and virtual devices supported by CoSSMic.


### How do I get set up? ###

* Install EmonCMS version 8.0 
* Overwrite the current directory in /var/www/emoncms. As an alternative you can just add the driver Module, before to start the first time the web inteface.
* Database configuration  (still manually performed by importing driver.sql records)
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Salvatore Venticinque: salvatore.venticinque@gmail.com
* Matthias Noebels: matthias.noebels@isc-konstanz.de
* the CoSSMic team