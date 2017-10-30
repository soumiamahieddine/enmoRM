Variables d'environnement
=========================

## LAABS_INSTANCE_NAME
Nom de l'instance d'application. 
Cette valeur définit un espace de nom utilisé pour le stockage d'informations et assurer le cloisonnement des données conservées par l'application. Il est utilisé notamment pour préfixer les identifiants générés par l'application pour les propriétés des objets utilisant le type "id" du modèle de données, mécanisme qui permet de renforcer l'unicité des identifiants, notamment dans le cas de consolidation des données de plusieurs instances dans un même référentiel.
Ce paramètre est aussi obligatoire lorsque le cache mémoire est utilisé, car il préfixe systématiquement le nom de la donnée à inscrire dans le cache, là encore pour éviter les collisions de nom dans le cas où le serveur de cache mémoire serait partagé entre plusieurs instance. S'il est omis, la gestion du cache mémoire ne sera pas activée.
La valeur est aussi utilisée pour identifier le domaine de validité des jetons générés par le framework. Un jeton n'est valable que pour un domaine donc un nom d'instance afin de sécuriser les informations et d'éviter qu'un jeton ne puisse être utilisé dans un autre domaine s'il venait à être volé.
Un même nom d'instance peut être utilisé par plusieurs serveurs d'application, ce qui présente même un avantage lorsque les instances des différents serveurs publient une même application, dans le cas d'une architecture distribuée par exemple. Les instances publiées peuvent ainsi partager les mêmes entrées du cache mémoire et valider les mêmes jetons – il faut alors évidemment qu'ils utilisent une même clé de chiffrement (voir LAABS_CRYPT_KEY).
Le nom de l'instance publiée DEVRAIT être modifié.

## LAABS_APP
Nom de l'application publiée. 
Cette valeur est utilisée pour identifier l'application en cours d'exécution, notamment dans les traces techniques. La valeur par défaut "MaarchRM" NE DEVRAIT PAS être modifiée. 

## LAABS_BUNDLES
Liste des noms des paquets métier utilisés par l'application, séparés par un point-virgule.
Cette valeur est utilisée pour déterminer les noms des répertoires de bundles, de base et éventuellement étendus (voir LAABS_EXTENSION) à utiliser parmi les paquets installés ainsi que les espaces de noms associés dans le code applicatif. Elle NE DOIT PAS être modifiée, à l'exception des mises à jour du logiciel, lorsque de nouveaux paquets sont livrés et utilisés par l'application.

## LAABS_DEPENDENCIES
Liste noms des dépendances techniques utilisées par l'application, séparés par un point-virgule.
Cette valeur est utilisée pour déterminer les noms des répertoires de dépendance à utiliser parmi les dépendances installées ainsi que les espaces de noms associés dans le code applicatif. Elle NE DOIT PAS être modifiée, à l'exception des mises à jour du logiciel, lorsque de nouvelles dépendances sont livrées et utilisées par l'application.

## LAABS_PRESENTATION
Nom de la couche de présentation utilisée. Cette valeur est utilisée pour déterminer le nom du répertoire de présentation à utiliser parmi les IHMs installées ainsi que les espaces de nom associés dans le code applicatif.
Elle permet au frontal de déterminer quel noyau utiliser: La présence d'une valeur a pour conséquence l'utilisation du noyau de présentation pour le traitement des requêtes qui sont alors des commandes utilisateur. En l'absence de valeur, c'est le noyau de service qui est invoqué et effectue les traitements en considérant la requête comme un appel à service.

## LAABS_EXTENSIONS
Liste des noms des extensions de code source utilisées par l'application, séparées par un point-virgule.
Cette valeur est utilisée pour déterminer les noms des répertoires d'extension à utiliser parmi les extensions installées ainsi que les espaces de noms associés dans le code applicatif. L'ordre des répertoires définit la hiérarchie des extensions, du niveau le plus haut (le dernier enfant en premier) au plus bas (le premier ancêtre en dernier). Elle NE DOIT PAS être modifiée, à l'exception du déploiement de nouvelles extensions utilisées par l'application.

## LAABS_PHP_INI
Chemin du fichier qui contient les directives d'initialisation de PHP qui seront définies à l'exécution.
Ce fichier ne peut contenir que des directives dont la valeur est modifiable par l'utilisateur dans un script PHP par la commande ini_set(). Les directives qu'il contient sont toutes définies à l'initialisation du cœur du framework. Si cette variable est omise, aucune configuration à l'exécution n'est réalisée.

> Voir la documentation http://php.net/manual/en/configuration.changes.modes.php

## LAABS_CONFIGURATION
Chemin du fichier de configuration des paquets métier et des dépendances.
Ceci permet de publier une ou plusieurs instances d'applications différentes ou de la même application sur un seul serveur applicatif, chaque instance démarrant avec un fichier de configuration qui lui est propre. A l'inverse, deux instances publiées par deux serveurs applicatifs peuvent utiliser un même fichier de configuration (soit partagé, soit maintenu à l'identique localement sur les deux serveurs) et ainsi publier la même application avec la même configuration. Laabs permettant de développer des applications RESTful donc "stateless" et orientées service, ceci permet de déployer facilement des architectures distribuées pour la répartition de charge et la haute-disponibilité.
Cette variable DOIT être définie pour nommer le fichier principal de configuration adapté à l'application publiée.

## LAABS_CRYPT_CIPHER
Méthode de chiffrement des données.
Cette valeur est utilisée par l'application pour déterminer l'algorithme de chiffrement des données, notamment pour les jetons de sécurité échangés entre les clients et le serveur applicatif. Par défaut l'algorithme utilisé est Blowfish. Cette valeur PEUT être modifiée avec les valeurs disponibles pour le module mcrypt de PHP 5.4 et celles du module openSSL de PHP 7 ou supérieur.

## LAABS_CRYPT_KEY
Clé de chiffrement des données.
Cette valeur est utilisée par l'application comme phrase secrète pour le chiffrement des données, notamment pour les jetons de sécurité échangés entre les clients et le serveur applicatif. Elle DOIT être modifiée pour fournir une phrase secrète, qui DOIT être identique pour toutes les instances utilisant les mêmes données, dans les architectures réparties (plusieurs système publiant la même instance) et pour les différents hôtes publiés sur un même système (mode présentation IHM et mode service REST).

## LAABS_XML_NS
Mappage des espaces de nom du domaine XML avec les espaces de nom de l'application.
Chaque espace de nom Laabs est suivi de deux points puis de la liste des espaces de nom XML correspondants séparés par une virgule, par exemple:

    NsLaabs1:ns_xml1,ns_xml2,…;nsLaabs2:ns_xml3,ns_xml4;…

Cette directive permet à certains composants techniques et métier de déterminer quels autres composants adresser dans le traitement de contenus XML, en faisant le lien entre l'espace de nom du nœud XML et le paquet métier à utiliser. C'est le cas dans les échanges normalisés selon la NF Z44-022 MEDONA et les standards d'échange 
