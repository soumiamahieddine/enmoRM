Description
===========

MaarchRM is a open source software that allows you to store, find and display
your digital resources in compliance with international regulation ISO. It’s a
full featured PHP PostgresSQL software.

Licence
=======

MaarchRM is released under the GPL v3 (or later) license, see
[LICENCE.txt](https://labs.maarch.org/maarch/maarchRM/blob/master/LICENCE.txt)

Requirements
============

-   Server Apache 2.4 (or greater)

    -   Module Apache rewrite_module

    -   Module Apache env_module

-   PHP 5.4 (or greater)

    -   Extension PHP fileinfo

    -   Extension PHP mcrypt

    -   Extension PHP pdo

    -   Extension PHP pdo_pgsql

    -   Extension PHP xsl

-   Application 7z

-   JAVA 1.7 (JRE)

Install
=======

-   Upload MaarchRM to your webserver.

-   Check your time zone configuration in php.ini

-   Create the repository *\<MaarchRM_Path\>/web/public/dependency*.

-   Create a symbolic link of *\<MaarchRM_Path\>/dependency/html/public* named
    *html* in that folder.

-   For linux users : Create a symbolic link of 7zip executable named *7z* in
    that same folder.

-   In *\<MaarchRM_Path\>/data/maarchRM/conf*, copy the files
    configuration.ini.default and confvar.ini.default as configuration.ini and
    confvar.ini

-   For Windows users : Uncomment the “zipExecutable” line of the configuration
    file
    *\<MaarchRM_Path\>/data/maarchRM/conf/conf.d/dependency\#fileSystem.ini* and
    write the right path of your 7zip executable.

-   Configure MaarchRM to connect to your database in the file
    *\<MaarchRM_Path\>/data/maarchRM/conf/confvars.ini* .

-   The structure of the database can be setup by the script file
    *\<MaarchRM\>/data/maarchRM/batch/pgsql/schema.sh* and the
    *\<MaarchRM\>/data/maarchRM/batch/pgsql/data.min.sh* will give you the data
    to start with.

-   A default virtual host to run the application can be find in
    *\<MaarchRM\>/data/maarchRM/conf/vhost.conf.default* (vhost.win.conf.default
    for windows user). Copy that file and include the virtual host to your
    apache server.

-   Login to the application throw your web browser (default admin user/password
    is “superadmin”/”superadmin”).

-   You are ready to go !

