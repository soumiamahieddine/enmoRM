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