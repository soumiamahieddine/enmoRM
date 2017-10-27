Configuration des notifications sur les événements de l’application
=======================
Maarch RM permet de configurer les événements du journal de l’application qui seront sujettes aux notifications. 
Cela nécessite d’avoir configuré le service de notification. 
L'administrateur du système d'archivage peut gérer les options sous la forme de valeurs de directives de configuration du module Audit.

Dans la configuration, la directive suivante est placée dans la section correspondant au module :

    [audit]
    notifications = "{
       'auth/userAccount/updateLock_userAccountId_' : {
           'receivers' : ['info@maarch.org'],
           'title' : 'MaarchRM - User lock',
           'message' : 'A user has been locked. See application history for more details',
           'onResult' : true
       }
    }

La liste ci-dessous détaille les paramètres qui peuvent être ajustés :

**route** : (chaîne de caractère) valeur à remplacer par la route de service sujette à la notification.

**receivers** : (tableau de chaîne de caractère) destinataires de la notification. Dans le cas d’un service de notification par courriel, la valeur sera les courriels des destinataires.

**title** : (chaîne de caractère) titre de la notification. Dans le cas d’un service de notification par courriel, la valeur sera l’objet du courriel.

**message** : (chaîne de caractère) message de la notification.

**onResult** : (boolean) indique pour quel état de l'événement la notification est effective.