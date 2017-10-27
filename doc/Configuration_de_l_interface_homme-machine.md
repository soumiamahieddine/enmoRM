Configuration de l'interface homme-machine
==========================================

L'opérateur du système d'archivage configure la présentation de l'application aux utilisateurs et l'interface homme-machine en adaptant des valeurs de directives de configuration de la couche de présentation. Ces directives sont placées dans la section correspondante dans le fichier de configuration :

    [presentation.maarchRM]
    ... directives de configuration de la présentation ...

## Menu
Maarch RM est livré avec un menu de base qui conviendra à la plupart des applications, et qui est adapté selon les droits fonctionnels de l'utilisateur connecté. Cependant, l'opérateur peut souhaiter ajouter ou enlever des points de menus pour permettre ou au contraire restreindre les accès directs par le menu autorisés aux utilisateurs. La directive "menu" permet à l'opérateur de gérer la structure du menu utilisé.

La configuration par défaut est livrée dans un fichier séparé inclus à la configuration par un mot-clé "@include" :

    @include menu.ini

La directive "menu" accepte une structure composite dérivée du JSON qui utilise au maximum trois niveaux d'imbrication. Cette valeur NE DEVRAIT PAS être modifiée sans assistance de l'éditeur.

## Logo
La directive "logo" indique l'URI d'un fichier de type image qui sera affiché dans divers écrans de l'interface, notamment la page principale et l'invite de connexion.

    logo = "/presentation/img/RM_MNCHM.svg"

L'URI est relative par rapport à la couche de présentation indiquée dans la configuration de l'instance. L'exemple ci-dessus va chercher le fichier dans le chemin réel "/presentation/maarchRM/Resources/img/RM_MNCHM.svg"

## Intitulé
La directive "title" définit l'intitulé de l'application utilisé par les navigateurs internet pour leurs onglets et fenêtres. Cet intitulé ne doit pas être confondu avec le nom de l'hôte Http.

    title = "Maarch RM"

## Intitulé de navigation
La directive "navbarTitle" définit un morceau de code http utilisé comme titre d'application dans l'écran principal, à côté du logo Il peut être omis si le logo comporte déjà le nom adéquat.

    navbarTitle = "My App"

Un style peut être ajouté en définissant une structure complexe de tableau de noeuds Html qui contiennent une valeur de texte et une valeur d'attribut "style" :

    navbarTitle = "[
      {
           'style' : 'font-size:44px;',
            'value' : 'm'
      },
      {
           'style' : 'font-size:32px; font-weight:bold;',
            'value' : 'aarch R'
      },
      {
           'style' : 'font-size:44px;',
            'value' : 'm'
      }
    ]"

## Icône web
La directive "favicon" définit l'icône utilisée par les navigateurs internet pour leurs onglets et fenêtres. Elle précise l'URI d'un fichier au format icône '.ico', par exemple :

    favicon = "/presentation/img/rm.ico"

L'URI est relative par rapport à la couche de présentation indiquée dans la configuration de l'instance. L'exemple ci-dessus va chercher le fichier dans le chemin réel "/presentation/maarchRM/Resources/img/rm.ico"
