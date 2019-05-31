# CHANGELOG


## Version 2.5

### IHM
- `Added` Ajout et suppression d'une seule ressource au sein d'une archive désormais possible

## Version 2.4.3 

### Métadonnées

- `Changed` Le format interne _Name_ utilisé pour les codes de règles de conservation 
et de communicabilité accepte désormais les caractères numériques en première position, ainsi que les tirets
- `Changed` Les appels au service de modification de la règle de conservation des archives 
peuvent désormais n'envoyer que le code de la règle, le système utilisant le référentiel pour récupérer les durées et le sort final. Idem pour la règle de communicabilité et la durée qui y est associée.

### Exploitation 

- `Changed` Nouveau paramètre pour les notifications, `mailSMTPAutoTLS` pour activer/désactiver le mode TLS 
automatique, notamment pour les connexions sans authentification

## Version 2.4.2

### Script d'import

- `Fixed` Correction d'un bug lors de l'utilisation de cli 

## Version 2.4.1

### IHM

- `Fixed` Le bouton 'Import' restait parfois grisé en cas d'erreur lors d'un versement

### Métadonnées

- `Fixed` Rétablissement de la fonctionnalité de création, diffusion et modification d'un fichier de profil d'archive de type
- `Changed` Suppression du bouton de modification des métadonnées descriptives SEDA dans la modale de détail du socle : le bouton est propre à l'extension archivesPubliques

## Version 2.4

### IHM

- `Changed` Nouveau design de la modale de détails des informations de l'archive
- `Changed` Modification du moteur de recherche : à présent insensible aux caractères spéciaux, ajout du symbole * qui permet de rechercher une archive débutant / finissant / contenant un terme spécifique (exemple* cherchera une archive débutant par le terme exemple, *exemple2* cherchera une archive contenant le terme exemple2) 
- `Fixed` Blocage des imports multiples lors de clics répétés sur le bouton Importer
- `Fixed` Le bouton Annuler est également non cliquable durant l'execution d'un versement
- `Fixed` Le choix "sans profil" au versement d'une archive ne doit apparaître que si le dossier qui la receptionne l'autorise

### Métadonnées

- `Added` Une variable "actionWithoutRetentionRule" a été ajoutée dans le fichier de configuration pour permettre ou non l'élimination d'une archive n'ayant pas de règle (preserve : L'archive ne peut pas être supprimée si aucune règle n'a été définie OU dispose : L'archive peut-être éliminée si aucune règle n'a été définie)
- `Added` Mise à jour automatique de la date de dernière modification d'une archive

### Administration

- `Changed` Mise à jour dans la configuration des fichiers de signature Droid (v91 => v94)

### Accès

- `Changed` Les libellés des formats dans le tableau de documents indique désormais le nom du type de document au lieu de son identifiant
- `Fixed` Les routes d'évènements du journal de l'application ont été intégralement traduites en Anglais

## Extension

- `Added` L'extension Workflow permettant l'utilisation des flux de travail est désormais disponible

## Version 2.3

### Exploitation

- `Added` Compatibilité navigateur Microsoft (IE 11 / Edge)
- `Added` Compatibilité PHP 7.2
- `Fixed` Optimisation de l'utilisation de la mémoire pour la recherche dans les journaux volumineux

### IHM

- `Changed` Nouvau design de l'écran principal pour la navigation, la recherche et la consultation
- `Added` Personnalisation de la page de connexion avec une image de fonds et un style configurables
- `Fixed` Blocage des double-clics sur les boutons

### Administration

- `Fixed` Ajout d'un contact et/ou d'une adresse et/ou d'un moyen de communication aux organisations et services

## Version 2.2

### Exploitation

- `Added` Modification des règles de plusieurs archives sans modifier la date de départ
- `Changed` Gestion des jetons de compte de service
- `Changed` Ajout des routes disponibles pour les privilèges des comptes de service dans la configuration
- `Fixed` Uniformisation des formats de date

### Métadonnées

- `Added` Possibilité de mettre les métadonnées en lecture seule

### Classement

- `Added` Possibilité de définir un répertoire pour les archives des journaux

### IHM

- `Added` Ajout d'un message de confirmation avant la suppression
- `Added` Configuration d'un CSS spécifique
- `Added` Ajout d'un "À propos"
- `Changed` Amélioration de l'affichage des archives des journaux
- `Changed` Amélioration de l'affichage des événements de l'application

### Administration

- `Added` Modification rétroactive des règles de conservation

### Sécurité

- `Security` Amélioration de la protection contre le CSRF (Cross-Site Request Forgery)

## Version 2.1

### Exploitation
- `Added` Restriction des comptes de service disponibles pour la planification à ceux qui possèdent le privilège pour le service à exécuter
- `Changed` Liste des services disponibles pour la planification déplacée dans la configuration. Table `batchProcessing.task` supprimée
- `Fixed` Correction d'erreurs dans le calcul de la prochaine vacation après une en erreur

### Échéancier d'élimination
- `Added` Sort final non défini au versement ou à la modification désormais interprété comme *A définir ultérieurement*
- `Changed` Modification du statut final des unités d'archive en fonction de l'opération qui mène à leur ressortie : *Détruite*, *Restituée* et *Transférée*
- `Fixed` Interdiction de toute demande sur les unités d'archive gelées : élimination, restitution, modification, transfert

### Élimination
- `Changed` La demande n'est valide que si l'unité d'archive et toutes ses unités contenues sont éliminables (à terme de la DUA ou sans règle de conservation, sort final *Détuire* ou sans sort final

### Versement
- `Added` Ajout du choix d'un niveau de service dans la plan de classement, comme complément au couple producteur-profil, et pris en compte lors du versement

### Sécurité
- `Added` Nouvelle fonction de verrouillage des rôles fonctionnels
- `Added` Liste noire des cas d'usage dans la configuration, afin de ne plus autoriser leur utilisation dans les rôles
- `Added` Demande de confirmation du nouveau mot de passe lors du changement par l'utilisateur
- `Changed` Les rôles fonctionnels peuvent désormais être supprimés
- `Changed` Modification du format autorisé pour les noms d'utilisateurs, autorisant les caractères spéciaux pour permettre l'emploi des adresses électroniques
- `Changed` Désactivation de l'auto-complétion pour les champs de saisie des us de passe, à la connexion et à la modification par l'utilisateur
- `Changed` Chaque utilisateur doit être rattaché à un service organisationnel dès sa création
- `Changed` Liste blanche des cas d'usage déplacée dans la configuration. Table `auth.publicUserStory` supprimée
- `Security` Amélioration de la protection contre les injections SQL, le XSS (Cross-Site Scripting), le CSRF (Cross-Site Request Forgery) et le ClickJacking

### IHM
- `Changed` Amélioration des champs de choix de service avec saisie intuitive et auto-complétion en deux temps, l'organisme d'abord et le service ensuite
- `Changed` Harmonisation des titres de menu, de services et d'écrans. Harmonisation des icônes de menu et d'écran
- `Fixed` Correction d'une erreur lorsque aucun service d'organisation n'était choisi par défaut pour l'utilisateur

### Classement
- `Changed` Les unités organisationnelles ne peuvent désormais plus être déplacées vers un autre organisme

### Accès
- `Added` Filtre par défaut sur la date de dépôt dans les écrans et fonctions de recherche

### Stockage
- `Changed` Type de données pour la taille des ressources numériques modifié en `bigint` pour autoriser les objets de taille supérieure à 2Go

### Traçabilité
- `Fixed` Correction d'un effet indésirable lorsqu'un journal était chaîné plusieurs fois dans la même seconde. La règle de détermination du dernier journal en date utilise la date du dernier événement contenu et non plus la date du premier

### Pérennisation
- `Fixed` Erreur lors de la détection du format de certains fichiers conteneurs, notamment OpenXML et Microsoft OOXML. Correction de l'appel au plugin de décompression pour extraire avec l’arborescence interne des dossiers

## Version 2.0

### IHM
- `Added` Écran principal pour l'accès par navigation et recherche, le classement en dossiers virtuels, le détail des métadonnées et la prévisualisation des données
- `Added` Fonction d'aperçu des documents, avec outils tiers d'échantillonnage (PDF: 2 pages, image si inférieur à 2Mo, etc)
- `Added` Formulaires de saisie dynamique pour les métadonnées des archives lors du versement et pour la modification

### Métadonnées
- `Added` Administration d'un dictionnaire des métadonnées
- `Added` Stockage de métadonnées en NoSQL (Json avec PostGre)
- `Added` Moteur de recherche dynamique sur les métadonnées

### Classement
- `Added` Profils d'archive décrits par l'administrateur
- `Added` Rattachement des profils autorisés aux services producteurs dans l'organigramme
- `Changed` Modification du format autorisé pour les identifiant d'organisation et de services, autorisant les caractères spéciaux pour permettre l'emploi des barres obliques, points, tirets et soulignements notamment

### Intégrité
- `Added` Stratégies de contrôle d'intégrité périodique définies dans les niveaux de service, avec fréquence de contrôle et taux d'échantillonnage

### Sécurité
- `Changed` L'administrateur de la sécurité ne peut désormais plus choisir le mot de passe temporaire. En remplacement, l'intégration d'un client de messagerie électronique permet d'envoyer un courriel à l'utilisateur comportant un mot de passe temporaire généré par l'application
