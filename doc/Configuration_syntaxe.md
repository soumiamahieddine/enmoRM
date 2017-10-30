Configuration : syntaxe
=======================

L'opérateur du système d'archivage configure l'application en adaptant des valeurs de directives 
de configuration pour les modules métier (bundles), les dépendances techniques (dependency) et la présentation. Ces directives sont placées dans les sections correspondantes dans le fichier de configuration principal dont le chemin est fourni dans la variable d'environnement "LAABS_CONFIGURATION".

## Sections
La configuration est organisée en **sections**, chaque section débutant par le nom du paquet métier, de la dépendance ou de la présentation indiqué entre crochets.

    [nom]

Pour les paquets de la couche métier (bundles), le nom de la section est uniquement constitué du nom du paquet, par exemple : 

    [auth]

    [digitalResource]

Pour les dépendances, le nom de la section est constitué de la racine "dependency", puis d'un point (séparateur de nom) puis du nom de la dépendance, par exemple : 

    [dependency.sdo]

    [dependency.html]

Pour la présentation, le nom de la section est constitué de la racine "presentaton", puis d'un point (séparateur de nom) puis du nom de la présentation, par exemple : 

    [presentation.maarchRM]

## Directives

Une section contient un ensemble de **directives** nommées et dont la valeur est toujours scalaire, par exemple 

    passwordEncryption = SHA256

Les valeurs contenant des caractères spéciaux ou des espaces DOIVENT être encadrées par des guillemets, par exemple :

    logo = "/presentation/img/RM_MNCHM.svg"

Pour les valeurs complexes telles que des tableaux ou des objets, Maarch RM utilise une syntaxe dérivée du JSON, dont les guillemets sont remplacés par des apostrophes, par exemple :

    securityPolicy = "{
	   'loginAttempts' : 3,
	   'passwordValidity' : 0
    }"

## Mots-clés

La syntaxe de la configuration utilise des mots-clés pour déclencher des comportements particuliers. Les mots-clés sont des noms qui débutent par un caractères arobase "@".

**@include**

Le mot-clé "Include" permet d'inclure des fichiers de configuration dans la configuration principale. Il précise le chemin à inclure :

    @include path/to/conf

Dans cet exemple, le chemin est "path/to/conf".

Le chemin peut être absolu ou relatif par rapport au fichier de configuration principal, par exemple :

    @include confvars.ini

    @include /home/user/maarchRM/confvars.ini

La cible du chemin peut être un fichier de configuration ou un répertoire, dans ce cas l'intégralité des fichiers contenus dans ce répertoire sera inclue, par exemple :

    @include confvars.ini

    @include conf.d

**@var**

Le mot-clé "var" permet de déclarer des variables qui seront utilisées dans la configuration, comme valeur d'autres directives. Il précise le nom de la variable et sa valeur :

    @var.varname = "varvalue"

Dans cet exemple, le nom de la variable est "varname" et sa valeur "varvalue".

Les variables définies dans la configuration sont utilisées dans les directives en indiquant leur nom encadré par des caractères de pourcentage "%", par exemple :

    directive = %varname%

La variable peut être utilisée seule ou au milieu de valeurs constantes, par exemple :

    directive = "start_%varname%_end"

La variable "laabsDirectory" est automatiquement évaluée par l'application, avec le chemin absolu du répertoire d'installation de l'application. Elle est donc disponible pour son utilisation dans la configuration sans déclaration préalable.

**@Adapter**

Le mot-clé "Adapter" indique un nom d'adaptateur de service à utiliser dans les dépendances technique lorsque celles-ci doivent implémenter une interface. Il précise le nom de l'adaptateur à utiliser, qui correspond à l'espace de nom relatif par rapport au service injecté, par exemple :

    [dependency.datasource]
    @Adapter = Database

L'exemple ci-dessus demande que le service "datasource" utilise l'adaptateur nommé "database" pour l'implémentation de ses interfaces. Les services injectés seront ainsi instanciés à partir des classes de l'espace de nom "\dependency\datasource\Database".

