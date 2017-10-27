Configuration de la prévisualisation
====================================

L'opérateur du système d'archivage configure la prévisualisation des documents aux utilisateurs en adaptant des valeurs de directives de configuration de la couche de présentation. Ces directives sont placées dans la section correspondante dans le fichier de configuration :

    [presentation.maarchRM]
    ... directives de configuration de la présentation ...


## Formats visualisables

La directive "displayableFormat" indique les types MIME qui seront affichés dans la prévisualisation.
Pour les formats PDF, la prévisualisation inclus les 2 premières page du document.
Pour sélectionner un ensemble de type MIME d'une même famille, rajouter '*'.
 
    displayableFormat = "['application/pdf', 'image/*', 'text/*']"