Configuration
=============

L'opérateur du système d'archivage adapte la configuration du système selon deux axes 

  * l'instance de l'application publiée par un hôte HTTP ou les commandes du système
  * les modules métier, les dépendances techniques et l'interface homme-machine

## Configuration de l'instance

L'application Maarch RM est publiée en une ou plusieurs instances, sur un ou plusieurs systèmes 
hôtes, pour fournir l'accès aux fonctionnalités métier.

**La publication d'un service Http** par un serveur Http (Apache, Ngnix, Microsoft IIS) 
permet les accès utilisateurs à l'interface homme-machine et les appels aux services 
REST pour les clients de service distants tels que les applications tierces.

**La publication en ligne de commande** du système hôte permet les appels aux services 
REST pour les clients de service locaux tels que les planificateurs de tâches ou 
les systèmes de surveillance locaux.

Chaque mode utilise des fichiers de configuration et une syntaxe propre au système de publication.
La partie de configuration spécifique à l'instance d'application Maarch RM se base sur la définition
de variables d'environnement.

Les documentations suivantes concernent la publication et les variables d'environnement utilisées :

  * [Publication HTTP](Publication_HTTP.md)
  * [Publication CLI](Publication_CLI.md)
  * [Variables d'environnement](Variables_d_environnement.md)

## Configuration de l'application
L'application publiée utilise une configuration pour les différents modules métier, dépendances techniques 
et l'interface homme-machine.

Dans la configuration de l'instance, la variable d'environnement **LAABS_CONFIGURATION** 
définit le chemin du fichier de configuration utilisé. 
Cette valeur DOIT être définie pour nommer le fichier principal de configuration adapté 
à l'application publiée par l'opérateur du système d'archivage.

Les documentations suivantes concernent la configuration métier et technique de l'application publiée :

  * [Syntaxe de la configuration](Configuration_syntaxe.md)
  * [Configuration de l'interface homme-machine](Configuration_de_l_interface_homme_machine.md)
  * [Configuration de la planification](Configuration_de_la_planification.md)
  * [Configuration de la sécurité](Configuration_de_la_sécurité.md)
  * [Configuration des notifications sur les événements de l'application](Configuration_des_notifications_sur_les_événements_de_l_application.md)
  * [Configuration des services de conversion](Configuration_des_services_de_conversion.md)
  * [Configuration du chemin de stockage](Configuration_du_chemin_de_stockage.md)
  * [Configuration du service de notification](Configuration_du_service_de_notification.md)
  * [Gestion de l'espace d'échange](Gestion_de_l_espace_d_échange.md)
  * [Gestion des comptes de service](Gestion_des_comptes_de_service.md)
  * [Gestion du référentiel des formats](Gestion_du_référentiel_des_formats.md)
  * [Gestion du stockage](Gestion_du_stockage.md)
