Installation procedure
======================

This procedure will guide you step by step through the installation of MaarchRM.

The software is packaged with default configuration and data that allow you to
run the archiving system in a “demo” mode. For a custom installation, please
read the docs at <http://wiki.maarch.org/Maarch_RM>.

This procedure is made for a Debian GNU Linux8 64bit system with a
PostgreSQL 9.4 database. Most of the command lines will require administrator
rights.


Prerequisite
------------

### Application server

-   Server Apache 2.4 (or higher)
-   Module Apache rewrite_module
    ```
    a2enmod rewrite
    ```
-   Module Apache env_module
    ```
    a2enmod env
    ```
-   PHP 5.4 (or higher)
    ```
    apt-get install php5
    ```
-   Extension PHP fileinfo
-   Extension PHP mcrypt
    ```
    apt-get install php5-mcrypt
    ```
-   Extension PHP pdo
    ```
    apt-get install php5-pgsql
    ```
-   Extension PHP pdo_pgsql
-   Extension PHP xsl
    ```
    apt-get install php5-xsl
    ```
-   Application 7z
    ```
    apt-get install p7zip-full
    ```
-   JAVA 1.7 (JRE)
    ```
    apt-get install default-jre
    ```
-   git
    ```
    apt-get install git
    ```

We recommend to have a minimum of 1Gb free disk space (it depends on the 
amount of data you have to store on the system)

### System user for the software

You have to create a user “maarch” in the group “www-data” with a “home”
directory.

```
useradd -m -g www-data maarch
```

To give the user the ability to launch batches, the user must launch the Apache2
server. In order to ensure this, you have to edit the Apache2 configuration file
(*/etc/apache2/envvars*). Change the file with the following value :

```
export APACHE_RUN_USER=maarch
```

You can test the Apache configuration with one of the following commands :

```
apache2ctl configtest
```

### Database server

Server PostgreSQL 9.4 (or higher)

```
apt-get install postgresql-9.4
```

### Client

Any HTML5 / CSS3 web browser.

Installation
------------

The software must be cloned in your Apache web directory (*/var/www/*  by
default) in a folder named “laabs”.
```
    cd /var/www
    git clone https://labs.maarch.org/maarch/maarchRM.git laabs
```
### Symbolic links

Then you have to create symbolic link of html dependency from the public
directory (*/var/www/laabs/web/public/dependency*).

```
mkdir -p /var/www/laabs/web/public/dependency
cd /var/www/laabs/web/public/dependency
ln -s ../../../dependency/html/public/ html
```

You also need to create the symbolic link of 7z binaries file in fileSystem
dependency (*/var/www/laabs/dependency/fileSystem/plugins/zip/bin/*).

```
 cd /var/www/laabs/web/public/dependency
 ln -s /usr/bin/7z 7z
```

### Rights modification

The rights of the entire */var/www/laabs* folder must be changed.

```
cd /var/www
chown -R maarch:www-data laabs/
chmod -R 775 laabs/
```

PostgreSQL administration
-------------------------

### User creation (not needed if you already have a user to administrate the database)

Connect to the database with postgres user and execute the following command
lines :

```
 psql
 CREATE USER maarch;
 ALTER ROLE maarch WITH CREATEDB;
 ALTER ROLE maarch WITH SUPERUSER;
 ALTER USER maarch WITH ENCRYPTED PASSWORD 'maarch';
 CREATE DATABASE "maarchRM" WITH OWNER maarch;
 \q
 exit
```

### Demo structure and data

In the folder */var/www/laabs/data/maarchRM/batch*/psql, you can find scripts for
postgreSQL installation. Execute the following command with root privileges :

```
cd /var/www/laabs/data/maarchRM/batch/psql
./schema.sh -u=maarch -h=5432 -d="maarchRM" -h=127.0.0.1
./data.sh -u=maarch -h=5432 -d="maarchRM" -h=127.0.0.1
```

Configuration
-------------

### Publication

The publication of a MaarchRM instance requires to add some environment
variables.

#### Apache

The instances are published by http server under named virtual hosts form, a
specific configuration and environment variables for execution.

Edit the Apache sites available configuration (generally in
*/etc/apache2/sites-available/000-default.conf*) to include the virtual host of
the software :

```
# Application Maarch RM 
Include /var/www/laabs/data/maarchRM/conf/vhost.conf
```

Edit the configuration file */var/www/laabs/data/maarchRM/conf/vhost.conf* and
define the default values.

```
# Path to the public web folder of MaarchRM
DocumentRoot /var/www/laabs/web/
# Name of the vhost (same as the name associated to the IP address in the host file)
ServerName maarchrm
```

This file also contain all the environment variables of the published instance :

-   The path to the software configuration file

-   The secret encryption key of the security token

-   The cache management

-   etc.

You can find the complete description of [those variables](http://wiki.maarch.org/Maarch_RM/Configuration).

After those configuration changes, you can restart Apache :

```
service apache2 restart
```

#### MaarchRM configuration

The configuration *(configuration.ini)* can be find in a main file. This file’s
name is defined for the execution environment :

-   In the configuration of virtual host(s)
-   In each script of the application to use it with command lines (batch, sh)

The configuration files can initially be found in configuration folder of
MaarchRM :

```
 cd /var/www/laabs/data/maarchRM/conf
```

Edit the *confvars.ini* file that contains variable for database connection :

```
@var.dsn = "pgsql:host=localhost;dbname=maarchRM;port=5432" 
@var.username = maarch 
@var.password = maarch
```

#### Connection to software

In order to connect to the right virtual host, you have to add a line in your
host file (*/etc/hosts*)

```
127.0.0.1  maarchrm
```

You can now use the application with a web browser by entering the url : *http://maarchrm*

 

You can connect with the administrator user ‘superadmin’ with the password
‘superadmin’.

The other default users have the password ‘maarch’.

 

Enjoy ;) !
