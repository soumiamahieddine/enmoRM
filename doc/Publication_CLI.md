Publication CLI
===============
L'opérateur du système d'archivage accède aux services de l'application à partir du système hôte 
par des appels en ligne de commande.

Maarch RM est livré avec un exemple de configuration de client ligne de commande sous la forme 
d'un fichier de configuration dans les données de base. 
Il est fortement recommandé que l'opérateur réalise une copie des fichiers d'exemple vers un 
répertoire dédié à la configuration de son environnement.

Dans le répertoire d'installation, le fichier d'exemple est le suivant :

    /data/maarchRM/batch/0-config.sh

Le début du contenu est le suivant :

    #!/bin/bash

    cd $SCRIPT_PATH

    # Set environment/server variables
    export LAABS_APP="maarchRM"
    export LAABS_INSTANCE_NAME="maarchRM"
    export LAABS_CONFIGURATION="../data/maarchRM/conf/configuration.ini"
    ...

## Déclaration de la configuration
La déclaration de de la configuration dans un appel par ligne de commande est faite par la en incluant 
le fichier de configuration par la commande "source", par exemple :

    source 0-config.sh

## Répertoire racine
La commande cd définit le répertoire racine d'exécution de l'application. 
Elle DOIT être modifiée pour correspondre au répertoire frontal de l'application, 
qui se trouve dans le répertoire d'installation et qui est nommé "web" :

    cd /var/www/laabs/web/

## Configurer l'application
Cette étape adapte la configuration pour définir le comportement de l'application sous la forme de 
définition de variables d'environnement du système par la commande export, par exemple :

    export LAABS_INSTANCE_NAME = maarchRM

Les variables d'environnement sont détaillées dans le document [**Variables d'environnement**](Variables_d_environnement.md).
On détaille ci-dessous celles qui requièrent une modification lors de l'installation d'un nouvel environnement.

**LAABS_INSTANCE_NAME** : Nom de l'instance d'application. 
Cette valeur DOIT être modifiée afin d'identifier l'instance publiée. Le nom doit être identique pour toutes les instances qui partagent les mêmes données persistantes, notamment les données de sécurité, les données d'archive et les traces du journal du cycle de vie de l'archive.

**LAABS_BUNDLES** : Liste des noms des paquets métier utilisés par l'application, séparés par un point-virgule.
Cette valeur NE DOIT PAS être modifiée, à l'exception des mises à jour du logiciel, lorsque de nouveaux paquets sont livrés et utilisés par l'application.

**LAABS_DEPENDENCIES** : Liste noms des dépendances techniques utilisées par l'application, séparés par un point-virgule.
Cette valeur NE DOIT PAS être modifiée, à l'exception des mises à jour du logiciel, lorsque de nouvelles dépendances sont livrées et utilisées par l'application.

**LAABS_PRESENTATION** : Nom de la couche de présentation utilisée.
Cette valeur NE DOIT PAS être incluse. Le système doit uniquement présenter l'API de service REST pour les clients de service.

**LAABS_EXTENSIONS** : Liste des noms des extensions de code source utilisées par l'application, séparées par un point-virgule.
Cette valeur NE DOIT PAS être modifiée, à l'exception du déploiement de nouvelles extensions utilisées par l'application.

**LAABS_CONFIGURATION** : Chemin du fichier de configuration des paquets métier et des dépendances.
Cette valeur DOIT être définie pour nommer le fichier principal de configuration adapté à l'application publiée.

**LAABS_CRYPT_KEY** : Clé de chiffrement des données.
Cette valeur DOIT être modifiée pour fournir une phrase secrète, qui DOIT être identique pour toutes les instances utilisant les mêmes données, dans les architectures réparties (plusieurs système publiant la même instance) et pour les différents hôtes publiés sur un même système (mode présentation IHM et mode service REST) et pour les appels en ligne de commande.




