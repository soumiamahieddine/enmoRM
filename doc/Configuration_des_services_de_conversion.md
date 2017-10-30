Configuration des services de conversion
========================================

La pérennisation des contenus d'information conservés dans le système d'archivage pour toute la durée du cycle de vie 
peut nécessiter des conversion des documents, pour aller de formats devenus non pérennes vers de nouveaux formats pérennes.

L'opérateur du système d'archivage définit les outils implantés dans le système et qui peuvent être utilisés pour réaliser 
les conversions de formats dans le cadre de la planification de la préservation grâce à des directives de configuration 
de la dépendance de gestion des fichiers "fileSystem". Les directives sont placées dans la section correspondante de la configuration :

    [dependency.fileSystem]
    ... Directives de configuration de la dépendance ...

La directive "conversionService" est une structure complexe qui décrit les services disponibles pour la conversion :

    conversionServices = "[
      {
        'serviceName'         : 'dependency/fileSystem/plugins/libreOffice',
        'softwareName'        : 'LibreOffice',
        'softwareVersion'   : '5.4.2.0',
        'inputFormats'        : ['fmt/412', 'fmt/291', 'fmt/293'],
        'outputFormats'     : {
          'fmt/95' : {
            'extension' : 'pdf',
            'filter' : 'writer_pdf_Export',
            'options': 'SelectPdfVersion=1'
          },
          'fmt/18' : {
            'extension' : 'pdf'
          }
        }
      }
    ]"

Chaque service déclaré possède les propriétés suivantes :

**serviceName** : 
Nom du service de conversion, qui sera exécuté par l'application. 
Il s'agit de toute URI de service disponible pour l'application, dans les dépendances ou les paquets métier.


**softwareName** : Nom du logiciel de conversion, qui sera utilisé dans les traces du journal du cycle de vie de l'archive.


**softwareVersion** : Version du logiciel de conversion, qui sera utilisé dans les traces du journal du cycle de vie de l'archive.


**inputFormats** : Tableau des identifiants de format du référentiel PRONOM (PUID) acceptés en entrée par le logiciel de conversion.


**outputFormats** : Liste des descriptions de format en sortie. 
Pour chaque format, on fournit en clé l'identifiant de format du référentiel PRONOM (PUID) et en valeur les propriétés suivantes :
    
  * **extension** : l'extension de fichier attendue,
  * **filter** : le filtre de conversion à transmettre au logiciel, qui dfinit le format de sortie
  * **options** : les options de conversion à transmettre au logiciel
