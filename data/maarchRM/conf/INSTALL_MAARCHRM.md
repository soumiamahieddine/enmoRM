## Installation Maarch RM

---

### Informations
Installation réalisée sur un système GNU Linux Ubuntu 14.04 LTS avec posgreSQL 9.3.6
La plupart des commandes qui seront exécutées nécessiteront potentiellement des droits
administrateur sur le système.

---

### Prérequis

 - Navigateur internet HTML5 / CSS3
 - Version PHP  5.4 minimum
 - Extension PHP file info 
 - Extension PHP mcrypt
 - Extension PHP pdo
 - Extension PHP pdo_pgsql
 - Extension PHP xsl
 - Module Apache rewrite_module
 - Module Apache env_module
 - Postgres 9.3 ou supérieur
 - Application 7z
 - Environnement d'exécution JAVA 1.7 (Optionnel : Pour le plugin jhove afin de valider le format des fichiers)

---

### Installation

#### Récupération des sources
##### Framework Laabs
À partir du répertoire web (habituellement */var/www*), récupérer les sources du framework Laabs. Le dossier laabs se créé automatiquement :

    $ svn checkout http://svn.laabs-framework.in/framework/trunk laabs  

##### Application Maarch RM
À partir du répertoire contenant les applications développées pour le framework (*/var/www/laabs/data/*), récupérer les sources de l'application Maarch RM. Le répertoire maarchRM se créera automatiquement.

    $ svn checkout http://svn.laabs-framework.in/data_maarchRM/trunk/ maarchRM

##### Dépendances, bundles et présentation
À partir du répertoire contenant les scripts développées pour l'application Maarch RM (*/var/www/laabs/data/maarchRM/batch), exécuter *checkout.linux.sh*.

    $ sh checkout.linux.sh

#### Accès et droits
##### Le Lien symbolique
À partir du répertoire public des dépendances (*/var/www/laabs/web/public/dependency*), créer un lien symbolique de la dépendance html

    $ ln -s ../../../dependency/html/public/ html


##### Changement des droits
À partir de la zone web (*/var/www*), remettre si nécessaire les droits et autorisation sur l'ensemble de la zone :

    $ chown -R www-data. laabs/
    $ chmod -R 775 laabs/

---

### Administration de PostgreSQL
#### Création d'un utilisateur
(Optionnel si vous possédez déjà un utilisateur avec les droits pour gérer la base de données de l'application)

Se connecter en tant qu'utilisateur postgres (depuis l'utilisateur administrateur, aucune mot de passe est nécessaire) et executer la commande suivante :

    $ psql

#### Créer un utilisateur

    CREATE USER maarch;
    ALTER ROLE maarch WITH CREATEDB;
    ALTER ROLE maarch WITH SUPERUSER;
    ALTER USER maarch WITH ENCRYPTED PASSWORD 'mon mot de passe';
    CREATE DATABASE "nom de la BDD" WITH OWNER maarch;
    \q
    exit

#### Création des tables et schémas pour l'application Maarch RM
À partir du répertoire contenant les scripts développées pour l'application Maarch RM (*/var/www/laabs/data/maarchRM/batch), exécuter *checkout.linux.sh*.

    $ /bin/bash sql.linux.sh

---

### Configuration

#### Système

##### Fichier hosts

Ajouter dans le fichier hosts du système :
Pour l'exemple, l'adresse IP est en localhost. Le nom qui suit doit correspondre au paramètre "ServerName" du vhost. (voir le fichier "vhost.conf" dans le dossier de configuration de l'application Maarch RM */var/www/laabs/data/maarchRM/conf*)

    127.0.0.1  maarchrm

##### Système d'exploitation

Cette partie est optionnel dans le cas ou aucune conversion ne sera réaliser.

Afin de pouvoir convertir des fichiers à l'aide du logiciel LibreOffice, il est nécessaire de disposer d'un utilisateur système ayant un répertoire "home" et étant dans le groupe "www-date".
Pour créer un utilisateur ayant un répertoire "home" avec le groupe "www-data" éxécutez la commande suivante :

    $ useradd -m -g www-data <nom de l'utilisateur>

Si l'utilisateur existe déjà, rajoutez simplement lui le groupe "www-data" :

    $ usermod -a -G www-data <nom de l'utilisateur>

Afin que cette utilisateur soit utilisé pour réaliser les conversions, il est nécessaire que le serveur Apache2 soit lancé par lui. Pour cela il faut editer le fichier de configuration d'Apache2 (*/etc/apache2/envvars*).
Modifier le fichier avec la valeur :

    export APACHE_RUN_USER=<nom de l'utilisateur>

Il est possible de tester la configuration du serveur Apache avec une des deux commandes suivante :

    $ apache2ctl configtest
    $ service apache2 configtest

Des droits sur certains dossier ou fichier devront être modifiés pour donner l'accès à l'utilisateur ou les membres du groupe "www-data".

#### Apache

Ajouter dans la configuration d'apache :

    # Application Maarch RM 
    Include /var/www/laabs/data/maarchRM/conf/vhost.conf

Relancer Apache :

    service apache2 restart

#### Application Maarch RM

Aller dans le répertoire de configuration de l'application Maarch RM

    cd /var/www/laabs/data/maarchRM/conf

Éditer le fichier de configuration "confvars.ini" et remplacer les paramètres par les bonnes valeurs

    @var.dsn = "pgsql:host=<adresse de la BDD>;dbname=<nom de la BDD>;port=<port de la BDD>" 
    @var.username = <utilisateur BDD> 
    @var.password = <mot de passe de l'utilisateur>
    @var.laabsDirectory = "<chemin absolu du framework Laabs>"

Éditer le fichier de configuration "vhost.conf" et remplacer les paramètres par les bonnes valeurs

    # Chemin vers le répertoire public web de Laabs
    DocumentRoot /var/www/laabs/web/
    # Nom du vhost (identique au nom associé à l'adresse IP dans le fichier host)
    ServerName maarchrm

Il ne faut pas oublié de rensigner les diverses autres valeurs si nécessaire.

---

### Connexion à l'application

Une fois toutes les opérations précédentes terminées, l'application est accessible depuis le navigateur internet.

    http://<Nom mis dans le fichier hosts et dans l'attribut ServerName du fichier vhost.conf>/

Avec notre exemple :

    http://maarchrm/

L'administrateur fonctionnel est 'superadmin', mot de passe 'superadmin'.

Tous les autres utilisateurs livrés dans les données d'exemple ont pour mot de passe par défaut 'maarch'.