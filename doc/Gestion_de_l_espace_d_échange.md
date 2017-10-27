Gestion de l'espace d'échange
=============================
Maarch RM utilise un répertoire pour stocker les paquets reçus et émis dans le cadre des échanges 
entre les acteurs du système d'archivage. 
L'opérateur du système d'archivage configure l'espace de stockage d'échange dans la section 
de configuration correspondant au module d'échange de données d'archives Medona.

## Configuration du répertoire
La directive "messageDirectory" définit le chemin du répertoire de stockage d'échange pour l'instance.

    messageDirectory = "/home/maarch/messages"

Le répertoire DOIT être partagé entre les instances de l'application. 
En effet, dans une architecture répartie et RESTful, un paquet stocké par l'une des instance applicatives 
DOIT pouvoir être utilisé par une autre, possiblement publiée sur un autre système hôte.

## Sauvegarde du répertoire
Les paquets de données stockés dans cet espace y sont conservés pour toute la durée de la transaction métier correspondante. 
Bien que temporaires du point de vue du métier, les données doivent être considérées comme persistantes dans la mesure 
où les transactions peuvent durer plusieurs jours. 
A ce titre, le répertoire d'échange DOIT être inclus à la sauvegarde quotidienne du système, 
au même titre que les données d'Archive.

## Purge du répertoire
Afin de maîtriser l'espace utilisé par ce sas d'entrée/sortie, il est nécessaire de le purger fréquemment 
des données devenues inutiles et obsolètes. Cette purge est opérée par un service de l'application qui doit 
être exécuté par l'opérateur.

L'identifiant du service à appeler est

    medona/message/deleteMessageDirectoryPurge

L'URI du service pour les appels Http est 

    DELETE medona/message/MessageDirectoryPurge

Le service utilise le configuration pour sélectionner les messages dont les données doivent être détruites de l'espace d'échange. La directive "removeMessageTask" définit les règles de sélection à l'aide de trois critères:

  * le type du message,
  * le statut du message,
  * le délai depuis le stockage des données correspondantes dans l'espace d'échange, 
  suite à la réception ou à l'émission d'un paquet d'information échangé.

Elle contient une structure complexe formatée comme suit :

    removeMessageTask = "{
      '<nom de la tache>' : {
        'type'   : ['<type de message 1>', '<type de message 2>'],
        'status' : ['<status A>', '<status B>'],
        'delay'  : '<delai>'
      },
      '<nom de la tache 2>' : {
        'type'   : ['<type de message 1>', '<type de message 2>'],
        'status' : ['<status A>', '<status B>'],
        'delay'  : '<delai>'
      }
    }"

Chaque tâche configurée utilise les paramètres suivants :

**Un nom de tâche** libre, sous la forme d'une chaîne de caractères normalisée 
(ne contient pas d'espace ou de caractères spéciaux en dehors de '-' et '\_' et 
qui doit commencer par une lettre).

**Le type de message** correspond à un ou plusieurs des types listés ci-après. 
Le statut varie en fonction des types de messages. 
Le statut utilisé DOIT correspondre à un ou plusieur des états finaux du type de message considéré, 
afin d'éviter la destruction des données avant la finalisation de la transaction du métier.

| Type | Statut | Description |
|------|--------|-------------|
| ArchiveTransfer | REJECTED  |Transfert entrant rejeté par l'archiviste |
| ArchiveTransfer | INVALID   |Transfert entrant invalide |
| ArchiveTransfer | PROCESSED | Transfert entrant traité |
| ArchiveDeliveryRequestReply | REJECTED | Communication rejetée |
| ArchiveDeliveryRequestReply | RECEIVED | Bordereau de communication |
| ArchiveRestitution | REJECTED  | Bordereau de restitution rejeté |
| ArchiveRestitution | PROCESSED | Bordereau de restitution traité |

Le delai est exprimé sous la forme d'un intervalle de durée selon la norme ISO 8601, par exemple :

  * P1M : un mois,
  * P10D : dix jours,
  * P1M5D : un mois et 5 jours,
  * PT1H : une heure

Exemple pour suppression des bordereaux de transfert traités, rejetés ou invalides après une semaine :

    'Transferts' : {
      'type'   : ['ArchiveTransfer'],
      'status' : ['processed', 'rejected', 'invalid'],
      'delay'  : 'P7D'
    }
