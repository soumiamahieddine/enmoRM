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

# Migration 2.4 vers 2.5 

## Présentation et fonctionnalités orientées "archives publiques"

### Situation dans les versions antérieures
Dans la section `[presentation.maarchRM]`, la directive `publicArchives` définissait les comportements suivants :
  * dans la gestion des utilisateurs, un seul rôle autorisé par utilisateur
  * dans la gestion des rôles, pas de gestion des utilisateurs rattachés
  * dans l'organigramme fonctionnel, pas de gestion des accès aux profils d'archive (géré par accords de versement uniquement)
  * dans la gestion des règles de communicabilité, pas de suppression ni de modification (règles issues du référentiel contrôlé par les Archives de France)
  * dans la gestion des règles de conservation, pas de gestion du sort final

La directive `menu` n'intégrait pas le point de menu vers la gestion du dictionnaire de données (champs de description);

Dans la section `[auth]`, la directive `blackListedUserStories` inhibait les droits sur les fonctions suivantes :
  * versement direct dans l'Archive 
  * gestion du dictionnaire de données 

### Nouvelles configurations 

Ces directives sont utilisables à la place de la directive existante `publicArchives` pour 
gérer plus finement les fonctionnalités correspondantes :

Dans la section `[auth]`, ajout de la directive `restrictUserRoles`, de type booléen. 
Si activée, elle restreint le nombre de rôles possibles pour un utilisateur à 1 seul dans la gestion des utilisateurs
et inhibe la gestion des utilisateurs rattachés dans la gestion des rôles.

Dans la section `[recordsManagement]`, la valeur de directive `archivalProfileType` définit désormais le comportement suivant :
  * `1` indique des profils de versement de type MEDONA et inhibe la gestion des accès aux profils dans l'organigramme
  * `2` indique des profils d'archive avec description des métadonnées et règles de gestion et active la gestion des accès aux profils dans l'organigramme
  * `3` indique des profils mixtes (MEDONA et description interne) et active la gestion des accès aux profils dans l'organigramme


## Branchement des schémas de description

Dans la section `[recordsManagement]`, la directive `descriptionSchemes` permet de définir
les schémas de description en lieu et place des entrées de la table `recordsManagement.descriptionClass` 
qui doit être supprimée.

A chaque identifiant de schéma de description (éventuellement précédemment inscrit dans la table) correspond un élément 
de configuration qui fournit :

  * le libellé affiché
  * le format de description : classe php, schema json, schéma XML
  * le nom du schéma
  * les URIs des différents services utilisés par l'application pour la gestion des données, 
    la recherche, la transformation, la présentation, etc.

```
descriptionSchemes = "{
  'seda2' : {
    'name' : 'SEDA 2',
    'type' : 'php',
    'uri' : 'seda2/Content',
    'controller' : '',
    'presenter' : ''
  }
}"
```
