Gestion du référentiel des formats
==================================
La pérennisation du contenu informationnel des documents numériques s'appuie sur l'identification stricte 
et la caractérisation des formats des contenus conservés. 

## Principes
Maarch RM s'appuie sur le référentiel **PRONOM**, qui fournit à la fois les spécifications des formats et des 
moyens d'identification. 
Il est produit et maintenu par le département _Digital Preservation_ des Archives Nationales du Royaume-Uni. 

> Pour de plus amples informations, 
> voir le site officiel à l'adresse http://apps.nationalarchives.gov.uk/PRONOM

On décrit ci-dessous les informations contenues dans le référentiel pour chaque format:

**PUID** : Identifiant unique PRONOM utilisé comme référence pour les documents numériques archivés par Maarch RM.

**Nom** : Nom usuel du format.

**Version** : Version du format (facultatif)

**Type MIME** : Multipurpose Internet Mail Extension, identifiant de format largement utilisé dans les technologies internet (facultatif)

**Extensions de fichier** : Extensions possibles pour les fichiers utilisant le format (facultatif)

**Signature interne** : Identifiant des signatures internes au format de fichier qui permettent sa détermination par une analyse du contenu

**Priorité sur format** : Identifiant des formats sur lesquels le format aura la priorité lors de la détermination du format de contenu (facultatif)

## Fichiers de référence
Le référentiel des formats PRONOM est livré sous la forme de fichiers XML dans la dépendance de gestion du 
système de fichiers, accompagnés d'un outil de détermination des formats qui utilise le système de signature 
interne.

Deux fichiers sont livrés :

  * le référentiel des formats et des signatures internes,
  * le référentiel des formats conteneurs (zip, OLE et dérivés)

Le référentiel principal est nommé <code>DROID_SignatureFile_Vx.xml</code>, 
où x représente le numéro de version. 
Il est aussi possible de connaître la version et la date du référentiel en affichant les données XML 
ou dans l'écran de consultation des formats de l'application, 
dans le menu d'administration **Formats > Référentiel des formats**.

En-tête du référentiel principal :

    <?xml version="1.0" encoding="UTF-8"?>
    <FFSignatureFile DateCreated="2017-07-25T12:17:59" Version="91" xmlns="http://www.nationalarchives.gov.uk/pronom/SignatureFile">
      <InternalSignatureCollection>
        <InternalSignature ID="9" Specificity="Specific">
          <ByteSequence Reference="BOFoffset">
    ...

Le référentiel des formats conteneurs est nommé <code>container-signature-20170330.xml</code>, 
où YYYYMMDD est la date de production au format année, mois et jours.

En-tête du référentiel des formats conteneurs:

    <?xml version="1.0" encoding="UTF-8"?>
    <ContainerSignatureMapping schemaVersion="1.0" signatureVersion="20">
      <ContainerSignatures>
         <ContainerSignature Id="1000" ContainerType="OLE2">
           <Description>Microsoft Word 6.0/95 OLE2</Description>
             <Files>
               <File>
                 <Path>WordDocument</Path>
               </File>
    ...

## Configuration et mise à jour
L'opérateur du système d'archivage définit le chemin vers les référentiels de format à utiliser 
dans l'application grâce à des directives de configuration de la dépendance de gestion des fichiers 
"fileSystem". Les directives sont placées dans la section correspondante de la configuration :

    [dependency.fileSystem]
    ... Directives de configuration de la dépendance ...

La directive "signatureFile" définit le chemin vers le fichier de signature principal 
et la directive "containerSignatureFile" définit le chemin vers le fichier des formats conteneurs :

    signatureFile = "/etc/droidSignatureFiles/DROID_SignatureFile_V91.xml"
    containerSignatureFile = "/etc/droidSignatureFiles/container-signature-20170330.xml"

Le référentiel est périodiquement augmenté et mis à jour par des contributions 
ou le département Digital Preservation des Archives Nationales du Royaume-Uni. 
Il est librement téléchargeable sur le site officiel et peut ainsi être mis à jour dans l'application.

L'opérateur DOIT régulièrement vérifier que le référentiel implanté dans le système d'archivage est à jour, 
en comparant la version implantée avec la dernière disponible 
sur le site http://www.nationalarchives.gov.uk/aboutapps/pronom/droid-signature-files.htm
