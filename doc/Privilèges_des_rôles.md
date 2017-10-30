Privilèges des rôles
====================

Ce document détaille les privilèges qui peuvent être attribués aux utilisateurs 
au travers de leur appartenance à un ou plusieurs rôles, chaque rôle ouvrant à 
une liste de privilèges.

Les privilèges sont organisés selon trois niveaux de détail :

  * les familles regroupent des cas d'usage ayant trait au même grand domaine 
  fonctionnel et qui sont habituellement attribués aux mêmes rôles d'utilisateurs
  * les cas d'usage regroupent les privilèges ayant trait à une même transaction
  ou un même domaine fonctionnel
  * les privilèges garantissent un accès à une fonctionalité unitaire, qui doit
  la plus souvent être regroupée avec d'autres pour permettre la réalisation des 
  cas d'usage

# Application de base 

Cette famille regroupe les cas d'usage et privilèges qui sont attribués à tous 
les utilisateurs de l'application.

## Accès à l'archive

  * Accéder à l'écran d'accueil
  * Accéder au plan de classement de ses services
  * Consulter les données et métadonnées de ses archives
  * Déplacer des archives dans ses dossiers de classement virtuels

## Utilisateur 

  * Demander un courriel pour mot de passe oublié
  * Modifier ses informations personnelles
  * Modifier son mot de passe
  * Choisir son service de travail (si plusieurs services de rattachement)

# Administration archivistique

Cette famille regroupe les cas d'usage qui permettent à un administrateur de 
gérer les référentiels de l'Archive.

## Gérer les règles de communicabilité

Ce cas d'usage permet à l'administrateur de gérer le référentiel des règles de 
communicabilité applicables aux archives.

Il comprend les privilèges suivants :

  * Accéder à la gestion des règles de communicabilité
  * Ajouter des règles
  * Modifier les règles
  * Supprimer les règles

## Gérer les règles de conservation

Ce cas d'usage permet à l'administrateur de gérer le référentiel des règles de 
conservation applicables aux archives.

Il comprend les privilèges suivants :

  * Accéder à la gestion des règles de conservation
  * Ajouter des règles
  * Modifier les règles
  * Supprimer les règles

## Gérer les profils d'archive

Ce cas d'usage permet à l'administrateur de gérer le référentiel des profils 
d'archive.

Il comprend les privilèges suivants :

  * Accéder à la gestion des profils d'archive
  * Ajouter des profils
  * Modifier les profils
  * Supprimer les profils
  * Générer une page de séparation pour la numérisation (code-barres)

## Gérer les champs de description

Ce cas d'usage permet à l'administrateur de gérer le référentiel des champs de 
description utilisés dans la définition des modèles de métadonnées descriptives
des profils d'archive.

Il comprend les privilèges suivants :

  * Accéder à la gestion des champs de description
  * Ajouter des champs
  * Modifier les champs
  * Supprimer les champs

# Administration fonctionnelle

Cette famille regroupe les cas d'usage qui permettent à un administrateur de 
gérer les référentiels fonctionnels, de sécurité et d'exploitation du système.

## Gérer l'organisation fonctionnelle

Ce cas d'usage permet à l'administrateur de gérer la structure hiérarchique de 
l'organisation, utilisée pour le plan de classement et la gestion des droits 
d'accès à l'archive notamment.

Il comprend les privilèges suivants :

  * Accéder à l'organigramme
  * Ajouter des organisations et services
  * Déplacer les organisations et services
  * Modifier la description des organisations et services
  * Supprimer les organisations et services
  * Accéder à la gestion des types d'organisation
  * Ajouter des types d'organisation
  * Modifier les types d'organisation
  * Supprimer les types d'organisation

## Gérer les profils d'archive des producteurs

Ce cas d'usage permet à l'administrateur de rattacher des profils d'archive aux 
services producteurs existants dans l'organigramme, afin de définir la plan de 
classement.

Il comprend les privilèges suivants :

  * Accéder à l'organigramme
  * Gérer la liste des profils rattachés à un service (ajouter et ôter)

## Gérer les contacts de l'organisation
  
Ce cas d'usage permet à l'administrateur de gérer l'information de contacts, 
d'adresse et de communication rattachée aux services acteurs existants dans 
l'organigramme, afin de préciser l'information transmise dans les échanges entre
les acteurs.

Il comprend les privilèges suivants :

  * Accéder à l'organigramme
  * Ajouter des contacts à l'organisation
  * Supprimer les contacts de l'organisation

## Gérer les utilisateurs de l'organisation

Ce cas d'usage permet à l'administrateur de rattacher les utilisateurs et les 
comptes de service aux services existants dans l'organigramme, afin de définir 
les droits d'accès aux données.

Il comprend les privilèges suivants :

  * Ajouter des utilisateurs aux services
  * Modifier la fonction d'un utilisateur et le service de rattachement par 
  défaut
  * Détacher les utilisateurs des services

## Gérer les utilisateurs

Ce cas d'usage permet à l'administrateur de gérer les comptes d'utilisateurs du 
système.

Il comprend les privilèges suivants :

  * Accéder à la gestion des utilisateurs
  * Ajouter des utilisateurs
  * Modifier la description des utilisateurs
  * Modifier les rôles des utilisateurs
  * Activer et désactiver les utilisateurs
  * Verrouiller et déverrouiller les utilisateurs
  * Réinitialiser le mot de passe des utilisateurs

## Gérer les comptes de service

Ce cas d'usage permet à l'administrateur de gérer les comptes de service du 
système.

Il comprend les privilèges suivants :

  * Accéder à la gestion des comptes de service
  * Ajouter des comptes de service
  * Modifier la description et les privilèges des comptes de service
  * Activer et désactiver les comptes de service
  * Générer les jetons de sécurité des comptes de service
  * Supprimer les comptes de service

## Gérer les rôles

Ce cas d'usage permet à l'administrateur de gérer les rôles et les privilèges 
correspondants.

Il comprend les privilèges suivants :

  * Accéder à la gestion des rôles
  * Ajouter des rôles
  * Modifier la description des rôles
  * Modifier la liste des privilèges des rôles
  * Activer et désactiver les rôles
  * Supprimer les rôles
  * Gérer les utilisateurs membres des rôles 

## Planifier des tâches

Ce cas d'usage permet à l'administrateur de gérer la planification, l'exécution 
et la surveillance des tâches exécutées en arrière-plan par le système.

Il comprend les privilèges suivants :

  * Accéder à la gestion des tâches planifiées
  * Ajouter des tâches planifiées
  * Modifier la planification et les paramètres des tâches planifiées
  * Exécuter les tâches planifiées
  * Suspendre les tâches planifiées
  * Consulter les traces des tâches planifiées

# Administration technique

Cette famille regroupe les cas d'usage qui permettent à un administrateur de 
gérer les référentiels techniques et la configuration de l'exploitation du 
système.

## Gérer les formats des événements du cycle de vie 

Ce cas d'usage permet à l'administrateur de gérer le format des messages des 
événements du journal du cycle de vie, ainsi que les options de notification de 
l'exploitant.

Il comprend les privilèges suivants :
  
  * Accéder aux types d'événements
  * Modifier le format du message des types d'événement
  * Activer et désactiver la notification sur erreur de l'administrateur

## Gérer les formats

Ce cas d'usage permet à l'administrateur de gérer le format d'encodage de 
l'information conservée et de mettre en oeuvre des politiques de pérennisation 
des contenus.

Il comprend les privilèges suivants :
  
  * Accéder au référentiel des format
  * Détecter le format d'un contenu
  * Accéder à la gestion des règles de conversion
  * Ajouter des règles de conversion
  * Modifier les règles de conversion
  * Supprimer les règles de conversion

## Gérer le stockage

Ce cas d'usage permet à l'administrateur de gérer le stockage des documents 
numériques sur les supports et d'assurer leur pérennité.

Il comprend les privilèges suivants :

  * Accéder à la gestion des supports de stockage
  * Ajouter des supports de stockage
  * Modifier les supports de stockage
  * Supprimer les supports de stockage
  * Vérifier l'intégrité des supports de stockage
  * Lister les erreurs des support de stockage
  * Vérifier l'intégrité d'une adresse de stockage
  * Accéder à la gestion des grappes de stockage
  * Ajouter des grappes de stockage
  * Modifier les grappes de stockage
  * Supprimer les grappes de stockage

## Gérer les niveaux de service

Ce cas d'usage permet à l'administrateur de gérer les niveaux de service 
applicables aux archives, qui définissent notamment les opérations techniques 
réalisées lors du versement et pour la durée de conservation.

Il comprend les privilèges suivants :
    
  * Accéder à la gestion des niveaux de service
  * Ajouter des niveaux de service
  * Modifier les niveaux de service
  * Supprimer les niveaux de service 

## Gérer les tâches

Ce cas d'usage permet à l'administrateur de gérer les tâches qui peuvent être 
planifiées et exécutées par l'administrateur fonctionnel.

Il comprend les privilèges suivants :

  * Accéder à la gestion des tâches
  * Ajouter des tâches
  * Modifier les tâches
  * Supprimer les tâches

# Versement 

Cette famille regroupe les cas d'usage qui permettent les transactions de 
versement de documents dans l'archive.

## Déposer une archive

Ce cas d'usage permet à l'utilisateur de constituer un paquet à archiver et de 
le transmettre au système via l'interface homme-machine.

Il comprend les privilèges suivants :

  * Accéder au formulaire de versement unitaire
  * Transmettre une archive unitaire au système 

# Gestion de l'archive 

Cette famille regroupe les cas d'usage qui permettent les opérations de gestion 
de l'archive.

## Accéder au registre

Ce cas d'usage permet à l'utilisateur d'accéder à l'écran de recherche et de 
gestion des archives.

## Contrôler l'intégrité des archives

Ce cas d'usage permet à l'utilisateur de demander au système un contrôle de la 
conformité des archives auxquelles il a accès. 

## Eliminer des archives

Ce cas d'usage permet à l'utilisateur de demander au système l'élimination des 
archives auxquelles il a accès.

Il comprend les privilèges suivants :
    
  * Demander la destruction des archives
  * Annuler les demandes de destruction

## Convertir le format des documents

Ce cas d'usage permet à l'utilisateur de demander au système la conversion d'un 
document de son format actuel vers un format pérenne tel que défini par 
l'administrateur technique dans la politique de conversion.

## Modifier la description des archives

Ce cas d'usage permet à l'utilisateur de demander la mise à jour de 
l'information de description des archives auxquelles il a accès.

Il comprend les privilèges suivants :

  * Accéder au formulaire de modification des métadonnées descriptives 
  * Soumettre une mise à jour des métadonnées descriptives

## Modifier les règles de gestion des archives

Ce cas d'usage permet à l'utilisateur de demander la mise à jour de 
l'information de gestion et d'application du cycle de vie des archives 
auxquelles il a accès.

Il comprend les privilèges suivants :

  * Modifier la règle de conservation des archives
  * Geler et dégeler l'application de la règle de conservation
  * Modifier la règle de communicabilité des archives

# Accès à l'archive 

Cette famille regroupe les cas d'usage qui permettent des accès particuliers 
à l'archive et nécessitent une élévation des droits.
 
Le système d'archivage garantit un accès non restreint pour l'interrogation, la 
recherche et la consultation des données et des métadonnées pour utilisateurs 
producteurs des archives.

## Gérer les dossiers de classement 

Ce cas d'usage permet à l'utilisateur de gérer les dossiers virtuels internes 
à ses services d'appartenance, pour le classement des archives sont il est 
producteur.

Il comprend les privilèges suivants :

  * Ajouter des dossiers de classement
  * Modifier les dossiers, leur nom et description, leur ouverture et fermeture
  * Déplacer les dossiers
  * Supprimer les dossiers

# Journaux

Cette famille regroupe les cas d'usage qui permettent l'exploitation des traces 
et journaux produits par le système d'archivage.

## Consulter le journal de l'application

Ce cas d'usage permet à l'utilisateur d'accéder aux traces du journal de 
l'application.

Il comprend les privilèges suivants :

  * Accéder à la recherche des traces
  * Consulter le détail d'un événement

## Consulter le journal du cycle de vie

Ce cas d'usage permet à l'utilisateur d'accéder aux traces du journal du cycle
de vie de l'archive.

Il comprend les privilèges suivants :

  * Accéder à la recherche des traces
  * Consulter le détail d'un événement

## Journaux archivés

Ce cas d'usage permet à l'utilisateur d'accéder aux journaux archivés dans le 
système.

Il comprend les privilèges suivants :

  * Accéder à la recherche des journaux
  * Consulter un journal

