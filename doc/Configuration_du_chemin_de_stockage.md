Configuration du stockage
=========================
L'opérateur du système d'archivage configure le stockage en adaptant des valeurs de directives de configuration du module de gestion archivistique Records Management. Ces directives sont placées dans la section correspondant au module dans le fichier de configuration :

    [recordsManagement]
    ... directives de configuration de la gestion de l'archive ...

## Principes
Maarch RM assure la sécurité et la confidentialité des données selon plusieurs mécanismes, 
au niveau physique et logique. 
Le service de dépôt des données sur les supports utilise un chemin de stockage qui autorise 
des variables, afin notamment d'adapter celui-ci en fonction des données et métadonnées d'archive. 
Par exemple, un chemin qui inclut l'identifiant de l'organisme producteur permet de matérialiser 
sur les supports l'étanchéité entre les différents clients de l'application. 

Le service de stockage utilise la règle suivante pour déterminer le modèle de chemin de stockage à utiliser :
  * il est fourni à l'appel du service par l'appelant,
  * sinon, il est récupéré de la directive de configuration "storePath",
  * sinon, la valeur par défaut est utilisée

La directive "storePath" indique le modèle de chemin à utiliser par défaut pour le stockage des 
données sur les supports.

    storePath = "<instance>/<originatorOwnerOrgId>/<originatorOrgRegNumber>/"

Les variables acceptées dans le chemin ont deux origines : 
  
  * les variables systèmes 
  * les propriétés des entités mises en oeuvre dans le processus de versement. 

Pour cette seconde catégorie, on utilise les métadonnées des archives versées, et les bordereaux de versement lorsque l'extension Third Party Archiving est activée.

Le chemin est résolu, c'est-à-dire les variables évaluées, pour chaque archive et objet de données stocké, 
au moment de son stockage. 
Les variables qui ne peuvent être évaluées parce que la valeur est absente au moment de la résolution du 
chemin sont remplacées par leur nom de variable.

Quel que soit le mode de détermination du chemin de stockage et les variables qu'il inclut, 
le nom du conteneur de données créé sur les supports de stockage est toujours l'identifiant unique de l'archive 
dans le système d'archivage et le nom des contenus numériques est toujours l'identifiant unique des ressources 
numériques dans le système d'archivage.

Par exemple, pour une archive déposée avec le chemin configuré comme dans l'exemple ci-avant, 
le chemin complet des contenus numériques sera le suivant :

    instance/id_organisme/id_producteur/id_archive/id_ressource

## Variables système
Les valeurs de variables ci-après sont fournies par le système d'archivage : 

**app** : Nom de l'application, tel que défini dans la configuration de l'instance.

**instance** : Nom de l'instance de l'application publiée, tel que défini dans la configuration de l'instance.

**date(format)** : Date ou portion de date, dont le format est défini par l'argument unique indiqué entre les parenthèses, par exemple 

    date(Y-m-d)

Pour une description des formats de date acceptés, voir la documentation php à l'adresse http://php.net/manual/function.date.php

## Variables des données versées
Les valeurs de variables ci-après sont des métadonnées fournies par la description des unités d'archives versées.

**originatorOwnerOrgRegNumber** : Identifiant de l'organisme d'appartenance du service producteur.

**originatorOrgRegNumber** : Identifiant du service producteur.

**archiverOrgRegNumber** : Identifiant du service d'archives.

**depositorOrgRegNumber** : Identifiant du service versant.

**archivalProfileReference** : Identifiant du profil d'archive.

## Variables de paquet versé
Les valeurs de variables ci-après sont des métadonnées fournies par le paquet d'information versé, le bordereau MEDONA ou SEDA par exemple.

**messageId** : Identifiant unique du paquet d'information versé.

**archivalAgreementReference** : Identifiant de l'accord de versement utilisé pour le transfert.


