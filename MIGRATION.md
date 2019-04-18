# Migration 2.1 => 2.2

Pour toutes les modifications ci-dessous, merci de vous référer à la documentation **AVANT tout changement** pour plus de détails'.

## Exploitation

Les clés des comptes de service doivent être réinitialisées

## Virtual host

Suppression de la dépendance CSRF dans le fichier **vhost.conf**

## SQL

Voir le fichier spécifique

    laabs/data/maarchRM/sql/pgsql/migrationV2.1_V2.2.sql


## Configuration

Les modifications de configuration font référence au fichier **configuration.ini**

### CSRF

Ajout de la configuration suivante : 

    csrfWhiteList = "['user/login']"
    csrfConfig = '{
        "cookieName" : "CSRF",
        "tokenLength" : 32
    }'
        
[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/csrf.md) 

### Customisation CSS

L'ajout de la customisation CSS permet d'utiliser une feuille de style personnalisable. 

    css = "/presentation/css/style.css"

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/customisation.md)

### Gestion des répertoires de log

Les répertoires des logs sont personnalisables depuis la 2.2.

    ; The path of journals in the file plan
    ; To include the type of log you must use <type>
    ; To include a part of date, you must use <date(format)>.
    ;   - Y for a full numeric representation of a year, 4 digits
    ;   - m for the numeric representation of a month, with leading zeros
    ;   - d for day of the month, 2 digits with leading zeros
    logFilePlan = "<type>/<date(Y)>/<date(m)>"
    
    translationLogType = "
    {
         'lifeCycle' : 'Journal du cycle de vie',
         'application' : 'Journal de l\'application',
         'system' : 'Journal du système'
     }" 

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/log_filePlan_path.md)

### Rôle d'organisation

Les rôles d'organisation ont été déplacés de la base de données vers la configuration. 

    orgUnitRoles = "
    {
        'owner' : {
            'code' : 'owner',
            'description' : 'The system owner'
        }
    }"

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/organization_roles.md)

### Ajout d'une nouvelle tâche planifiée

Une tâche se prénommant "Mise à jour de la durée d'utilité administrative" a été ajoutée.

De ce fait, il faut ajouter le privilège :

    servicePrivileges = "[
        {
            'serviceURI': 'audit/event/createChainjournal',
            'description' : 'Chaîner le journal de l\'application'
        },
        {
            'serviceURI': 'batchProcessing/scheduling/updateProcess',
            'description' : 'Exécution automatique des tâches planifiées'
        },
        {
            'serviceURI': 'lifeCycle/journal/createChainjournal',
            'description' : 'Chaîner le journal du cycle de vie'
        },
        {
            'serviceURI': 'recordsmanagement/archivecompliance/readperiodic',
            'description' : 'Valider l\'intégrité des archives'
        },
        {
            'serviceURI': 'recordsManagement/archives/deleteDisposablearchives',
            'description' : 'Détruire les archives'
        },
        {
            'serviceURI': 'recordsManagement/archives/updateIndexfulltext',
            'description' : 'Extraction plein texte'
        },
        {
            'serviceURI': 'recordsManagement/archive/create',
            'description' : 'Création d\'une archive'
        },
        {
            'serviceURI': 'recordsManagement/archive/createArchiveBatch',
            'description' : 'Création par batch d\'archive(s)'
        },
        {
            'serviceURI' : 'recordsManagement/archives/updateArchivesretentionrule',
            'description' : 'Mise à jour de la durée d\'utilité administrative'
        },
        {
            'serviceURI': '*',
            'description' : 'Tous les droits'
        }
    ]"
    
et toutes les tâches :

    [batchProcessing]
    tasks = "[
                {
                    'taskId': '01',
                    'route' : 'audit/event/createChainjournal',
                    'description' : 'Chainer le journal de l\'application'
                },
                {
                    'taskId': '02',
                    'route' : 'lifeCycle/journal/createChainjournal',
                    'description' : 'Chainer le journal du cycle de vie'
                },
                {
                    'taskId': '03',
                    'route' : 'recordsManagement/archiveCompliance/readPeriodic',
                    'description' : 'Valider l\'intégrité des archives'
                },
                {
                    'taskId': '04',
                    'route' : 'recordsManagement/archives/deleteDisposablearchives',
                    'description' : 'Détruire les archives'
                },
                {
                    'taskId': '05',
                    'route' : 'batchProcessing/notification/updateProcess',
                    'description' : 'Envoyer notification'
                },
                {
                    'taskId': '06',
                    'route' : 'recordsManagement/archives/updateIndexfulltext',
                    'description' : 'Extraction plein texte'
                },
                {
                    'taskId': '07',
                    'route' : 'recordsManagement/archives/updateArchivesretentionrule',
                    'description' : 'Mise à jour de la durée d\'utilité administrative'
                }
            ]"
            
[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/scheduling.md)

# Migration 2.2 vers 2.3 

## Configuration

### Ecran de connexion

Un style peut être appliqué à l'écran de connexion utilisateur, via la directive 
"loginBackground" de la section "presentation.maarchRM":

```
loginBackground = ".modal-backdrop {
    background-image: url('presentation/img/19093d7d-21f4-491b-bca1-5f57704c29d9.jpg');
    background-repeat: no-repeat;
    background-position: center top;
    background-color: #fff;
    background-size: cover;
    opacity: 1 !important;
}"
```

De même, le logo utilisé dans la mire de connexion peut être modifié séparément 
de celui affiché dans la barre de navigation:

```
altLogo = "/presentation/img/RM.svg"

```

### CSRF

Modification de la configuration : 

    csrfWhiteList = "['user/login', 'user/password']"
    csrfConfig = '{
        "cookieName" : "CSRF",
        "tokenLength" : 32
    }'

# Migration 2.3 vers 2.4

## Evenement

Ajout de l'évènement recordsManagement/resourceDestruction dans la table "lifeCycle.eventFormat" qui permet la suppression d'une ressource détenue dans une archive.

Ajout de l'évènement recordsManagement/updateRelationship dans la table "lifeCycle.eventFormat" qui permet de mettre à jour les relations d'archives.

Ajout de l'évènement recordsManagement/restitutionRequest dans la table "lifeCycle.eventFormat" qui permet de faire une demande de restitution de l'archive.

Ajout de l'évènement recordsManagement/restitutionRequestCanceling dans la table "lifeCycle.eventFormat" qui permet d'annuler une demande de restitution de l'archive.

## Configuration

Rajout des options dateTimeFormat, timestampFormat, timezone dans les paramètres dependency.localisation

```
[dependency.localisation]
@Adapter                        = Gettext
lang                            = fr
dateFormat                      = d-m-Y
dateTimeFormat                  = "Y-m-d H:i:s \(P\)"
timestampFormat                 = "Y-m-d H:i:s \(P\)"
timezone                        = Europe/Paris
```

Ces paramètres permettent de modifier le fuseau horaire et l'affichage des dates à l'écran. 
Le paramètre `dateTimeFormat` définit le format d'affichage des valeurs date et heure en suivant le formalisme d'affichage php (se référer à http://php.net/manual/fr/function.date.php )
Le paramètre `timestampFormat` définit le format d'affichage des temps en suivant le formalisme d'affichage php.
Le paramètre `timeZone` définit le fuseau horaire utilisé pour l'affichage en heure locale.
Si ces paramètres sont ignorés, les valeurs par défaut sont chargées par le logiciel, correspondant à un format respectant le standard ISO8601.