# CHANGELOG

## Version 2.5

### Sécurité

- `Fixed` Correction de la fonction permettant d'intégrer un fichier css personnalisé pour focer à faire référence à fichier CSS et empêcher la remontée de repertoire grâce au chemin de celui-ci.  (Merci à *Vladimir TOUTAIN* et *Sammy FORGIT* pour la communication de l'exploit).
- `Fixed` Déplacement de la route permettant la modification des utilisateurs pour éviter l'élévation de privilège lorsqu'un utlisateur n'a pas les droits adéquats. (Merci à *Vladimir TOUTAIN* et *Sammy FORGIT* pour la communication de l'exploit).
- `Added` Trois niveaux d'utilisateurs basés sur les exigences de la NF Z 42-020 : Administrateur Général, Administrateur Fonctionnel et Utilisateur Simple.
- `Fixed` Attribution d'un "service par défaut à l'affichage" lors de la suppression du service par défaut d'un utilisateur.

### Journalisation

- `Added` Génération et téléchargement manuel des attestations de dépôt, de validation d'intégrité et de destruction des archives à partir du journal de cycle de vie.
- `Fixed` Le nombre d'évènements sur l'archive n'est plus multiplié par le nombre de ressources qu'elle contient.
- `Fixed` L'accès aux évènements d'un journal chaîné est rétabli pour les utilisateurs non producteurs de cette archive de journal.

### Administration Technique

- `Added` Un fichier d'horodatage peut être généré par défaut lors du chaînage du journal, et associé à l'archive générée.
- `Added` Le nombre de résultats affichés dans les écrans est désormais paramétrable.
- `Changed` Si le niveau de service n'est pas déclaré par l'accord de versement ou par référence directe dans les données versées, c'est le niveau de service par défaut qui est désormais appliqué.

### Administration Fonctionnelle

- `Added` Le droit "Télécharger les attestations" est ajouté dans les privilèges de rôle.
- `Added` L'option "Autoriser les archives sans profil" est activable sur des profils de type dossier sans sous-profil déclaré.

### Métadonnées

- `Added` Des schémas de description d'archive peuvent être ajoutés dans l'application avec des métadonnées de type tableau ou objet.
- `Added` Les champs de métadonnées complexes (tableaux, objets) peuvent être ajoutés à un profil, complétés lors d'un versement, modifiés pendant la conservation et consultés via les écrans.
- `Added` Possibilité d'ajout de champ de description "clé valeur" de type énumération dans les données descriptives.
- `Added` Possibilité d'intégration de référentiels externes (exemple : CSV, base de données...) pour les données descriptives.
- `Added` Possibilité d'ajout d'un fichier permettant d'étendre le modèle de description d'un format d'échange (exemple : format d'échange SEDA 2.1 avec des champs complémentaires pour la modification).
- `Added` La métadonnée descriptive de type date peut comprendre une date complétée d'une heure (format livré par défaut : JJ-MM-AAAA HH:mm:ss).
- `Added` Les libellés des métadonnées descriptives qui sont référencées dans le dictionnaire sont affichés.
- `Added` Ajout d'un contrôle lors de la suppression d'un champ de données descriptives : si le champ est engagé dans un profil d'archive, la suppression sera bloquée.

### Gestion de l'archive

- `Added` Une ressource peut être ajoutée ou supprimée au sein d'une archive conservée, et la modification est tracée dans le journal du cycle de vie.
- `Added` Possibilité de versement par bordereaux XML/Medona.
- `Added` Les bordereaux entrants comportent le type d'empreinte poussé dans le système.
- `Changed` L'intégralité des échanges transactionnels MEDONA (extension thirdPartyArchiving) est désormais disponible dans le socle d'archivage. L'extension thirdPartyArchiving est obsolète.

### IHM

- `Changed` Ajout d'un message indiquant qu'une recherche simple ou avancée n'a retourné aucun résultat.
- `Fixed` Correction et/ou clarification de messages d'erreurs.
- `Fixed` Tri des résultats de recherche sur les champs date.

### Stockage / Conservation

- `Added` Possibilité de convertir unitairement des documents dans la modale de détail d'une archive.
- `Changed` Le nombre de sites de stockage minimum est ramené à 1.

### Modèle conceptuel

- `Added` Ajout de plusieurs index afin d'améliorer les performances de l'application.

### Correctifs

- `Fixed` Correction des erreurs lors de la génération de la documentation openapi

## Version 2.4.4

### IHM

- `Fixed` Retrait de l’affichage des ressources si l'archive est en cours de suppression

### Métadonnées

- `Fixed` Ajout d'un bouton dans la modale de détail de l'archive permettant la conversion unitaire d'un document
- `Fixed` Rétablissement de l'affichage des fichiers convertis et des relations dans la modale de détails de l'archive
- `Changed` Différenciation entre le fichier d'origine et ses conversions dans l'arborescence du plan de classement

### Sécurité

- `Changed` Ajout du paramètre `lifetime` pour le jeton CSRF qui définit la durée de validité des jetons en secondes. Si omis, durée de 1 heure par défaut.
- `Fixed` Jeton CRSF consommé lorsqu'utilisé.

## Version 2.4.3

### Métadonnées

- `Changed` Le format interne _Name_ utilisé pour les codes de règles de conservation
et de communicabilité accepte désormais les caractères numériques en première position, ainsi que les tirets
- `Changed` Les appels au service de modification de la règle de conservation des archives
peuvent désormais n'envoyer que le code de la règle, le système utilisant le référentiel pour récupérer les durées et le sort final. Idem pour la règle de communicabilité et la durée qui y est associée.

### Exploitation

- `Changed` Nouveau paramètre pour les notifications, `mailSMTPAutoTLS` pour activer/désactiver le mode TLS
automatique, notamment pour les connexions sans authentification

## Version 2.4.2

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
