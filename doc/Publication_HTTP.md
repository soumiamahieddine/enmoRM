Publication HTTP
================
L'opérateur du système d'archivage publie l'application pour les clients internet 
et les clients de service au travers d'un ou plusieurs hôtes virtuels du serveur Http. 
La documentation ci-après est valable pour un serveur Http Apache en version 2.4.

Maarch RM est livré avec un exemple de configuration d'hôte virtuel sous la forme d'un fichier 
dans les données de base. 
L'opérateur DOIT réaliser une copie du fichier d'exemple vers un répertoire dédié à la configuration 
de son environnement.

Dans le répertoire d'installation, le fichier d'exemple est le suivant :

    /data/maarchRM/conf/vhost.conf.default

Le début du contenu est le suivant :

    <VirtualHost *:80>
        # Set document root in Laabs public web directory
        DocumentRoot /var/www/laabs/web/

        # Set server name
        ServerName maarchrm
        
        Options -Indexes
        Options FollowSymLinks
        
        # DirectoryIndex dynamic.php
        
        # Rewrite URLs to route to frontal scripts
        # when target is not an existing public resource
        RewriteEngine On
        
        RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} -f [OR]
        RewriteCond %{REQUEST_URI} ^/public [NC]
        RewriteRule .* - [QSA,L]
        
        # Rewrite to dynamic frontal if no file extension
        # input: /route?args...
        # output : http.php/route?args...
        RewriteRule ^(.*)$ /http.php [QSA,L]
        
        # Set environment variables for the application instance
        SetEnv LAABS_INSTANCE_NAME maarchRM
        SetEnv LAABS_APP maarchRM
    ...

## Déclaration de l'hôte virtuel
La déclaration de l'hôte est faite par la directive `VirtualHost`. 
Chaque serveur virtuel doit correspondre à une adresse IP, un port ou un nom d'hôte. 
Pour Maarch RM, il est fortement recommandé d'utiliser un hôte virtuel basé sur le nom. 

> Pour de plus amples informations sur la directive VirtualHost du serveur Http Apache, 
la documentation officielle est disponible sur le site de l'éditeur à l'adresse suivante : 
https://httpd.apache.org/docs/current/mod/core.html#virtualhost

La déclaration de l'hôte virtuel Http pour la publication de l'application Maarch RM 
peut être réalisée directement dans le fichier de configuration principal du serveur 
Apache ou bien par inclusion d'un fichier de configuration externe tel que celui livré 
à titre d'exemple grâce à la directive Include, par exemple 

    Include /var/www/laabs/data/maarchRM/conf/vhost.conf

> Pour de plus amples informations sur la directive Include du serveur Http Apache, 
la documentation officielle est disponible sur le site de l'éditeur à l'adresse suivante : 
https://httpd.apache.org/docs/current/mod/core.html#include

## Répertoire racine
La directive `DocumentRoot` définit le répertoire racine de l'hôte virtuel. 
Elle DOIT être modifiée pour correspondre au répertoire frontal de l'application, 
qui se trouve dans le répertoire d'installation et qui est nommé `web` :

    # Set document root in Laabs public web directory
    DocumentRoot /var/www/laabs/web/

>Pour de plus amples informations sur la directive DocumentRoot du serveur Http Apache, 
la documentation officielle est disponible sur le site de l'éditeur à l'adresse suivante : 
https://httpd.apache.org/docs/current/mod/core.html#documentroot

## Nom de l'hôte
La directive `ServerName` définit le nom de l'hôte virtuel, qui sera utilisé dans les URL 
d'appel aux fonctionnalités du logiciel. 
Elle DOIT être modifiée pour identifier l'hôte publié pour les clients internet et les clients de service.
Le nom ainsi défini doit potentiellement être pris en compte dans les services DNS 
ou les fichiers de configuration locale des postes clients (fichiers `host`).

    # Set server name
    ServerName maarchrm

> Pour de plus amples informations sur la directive ServerName du serveur Http Apache, 
la documentation officielle est disponible sur le site de l'éditeur à l'adresse suivante : 
https://httpd.apache.org/docs/current/mod/core.html#servername

## Réécritures
Maarch RM utilise un système de réécriture des URLs pour adresser les services applicatifs. 
Les directives suivantes NE DOIVENT PAS être modifiées par l'opérateur:

    # Rewrite URLs to route to frontal scripts
    # when target is not an existing public resource
    RewriteEngine On

    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_URI} ^/public [NC]
    RewriteRule .* - [QSA,L]

    # Rewrite to dynamic frontal if no file extension
    # input: /route?args...
    # output : http.php/route?args...
    RewriteRule ^(.*)$ /http.php [QSA,L]

## Configurer l'application
Cette étape adapte la configuration pour définir le comportement de l'application 
sous la forme de définition de variables d'environnement de l'hôte virtuel par la 
directive `SetEnv`, par exemple :

    SetEnv LAABS_INSTANCE_NAME maarchRM

> Pour de plus amples informations sur la directive SetEnv du serveur Http Apache, 
la documentation officielle est disponible sur le site de l'éditeur à l'adresse suivante : 
https://httpd.apache.org/docs/current/mod/mod_env.html#setenv

Les variables d'environnement sont détaillées dans le document [**Variables d'environnement**](Variables_d_environnement.md).
On détaille ci-dessous celles qui requièrent une modification lors de l'installation d'un nouvel environnement.

**LAABS_INSTANCE_NAME**: Nom de l'instance d'application. 
Cette valeur DOIT être modifiée afin d'identifier l'instance publiée. Le nom doit être identique pour toutes les instances qui partagent les mêmes données persistantes, notamment les données de sécurité, les données d'archive et les traces du journal du cycle de vie de l'archive.

**LAABS_BUNDLES**: 
Liste des noms des paquets métier utilisés par l'application, séparés par un point-virgule.
Cette valeur NE DOIT PAS être modifiée, à l'exception des mises à jour du logiciel, lorsque de nouveaux paquets sont livrés et utilisés par l'application.

**LAABS_DEPENDENCIES**: Liste noms des dépendances techniques utilisées par l'application, 
séparés par un point-virgule.
Cette valeur NE DOIT PAS être modifiée, à l'exception des mises à jour du logiciel, lorsque de nouvelles dépendances sont livrées et utilisées par l'application.

**LAABS_PRESENTATION**: Nom de la couche de présentation utilisée.
Cette valeur PEUT être supprimée ou incluse. Si elle est omise, l'hôte virtuel présente l'API de service REST pour les clients de service. Si elle est présente, elle NE DOIT PAS être modifiée et avoir la valeur "maarchRM".

**LAABS_EXTENSIONS**: Liste des noms des extensions de code source utilisées par l'application, 
séparées par un point-virgule.
Cette valeur NE DOIT PAS être modifiée, à l'exception du déploiement de nouvelles extensions utilisées par l'application.

**LAABS_CONFIGURATION**: Chemin du fichier de configuration des paquets métier et des dépendances.
Cette valeur DOIT être définie pour nommer le fichier principal de configuration adapté à l'application publiée.

**LAABS_CRYPT_KEY**: Clé de chiffrement des données.
Cette valeur DOIT être modifiée pour fournir une phrase secrète, qui DOIT être identique pour toutes les instances utilisant les mêmes données, dans les architectures réparties (plusieurs système publiant la même instance) et pour les différents hôtes publiés sur un même système (mode présentation IHM et mode service REST) et pour les appels en ligne de commande..
