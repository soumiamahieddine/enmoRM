#!/bin/bash

libnames=(
	"apache2"
	"php"
	"php-xml"
	"php-pgsql"
	"php-gd"
	"php-mbstring"
	"php-opcache"
	"libapache2-mod-php"
	"openssl"
	"p7zip-full"
	"default-jre"
	"git")

libnamesoptional=(
	"php-mcrypt"
	"php-pdo-pgsql")

moduleapache=(
	"env_module"
	"rewrite_module"
	)

echo "
----- REQUIRED ------
"

for i in "${libnames[@]}"
do
	dpkg -s $i &> /dev/null
	dpkg=$?

	command -v $i &> /dev/null
	command=$?

	if [ $dpkg -eq 0 ] || [ $command -eq 0 ]; then
    	echo -e "Package \e[92m\e[1m$i\e[0m is installed!"
	else
	    echo -e "Package \e[91m\e[1m$i\e[0m is NOT installed!"
	fi
done

echo "
----- OPTIONAL ------
"

for i in "${libnamesoptional[@]}"
do
	dpkg -s $i &> /dev/null
	dpkg=$?

	command -v $i &> /dev/null
	command=$?
	if [ $dpkg -eq 0 ] || [ $command -eq 0 ]; then
    	echo -e "Package \e[92m\e[1m$i\e[0m is installed!"
	else
	    echo -e "Package \e[91m\e[1m$i\e[0m is NOT installed!"
	fi
done

echo "
----- MODULE APACHE ------
"
for i in "${moduleapache[@]}"
do
	apache2ctl -M | grep $i &> /dev/null
	if [ $? -eq 0 ]; then
    	echo -e "Package \e[92m\e[1m$i\e[0m is installed and activated !"
	else
	    echo -e "Package \e[91m\e[1m$i\e[0m is NOT installed or NOT activated !"
	fi
done
