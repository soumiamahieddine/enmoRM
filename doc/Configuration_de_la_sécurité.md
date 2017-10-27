Configuration de la sécurité
============================
L'opérateur du système d'archivage configure la sécurité de l'application en adaptant 
des valeurs de directives de configuration du module de contrôle de la sécurité Auth. 
Ces directives sont placées dans la section correspondant au module dans le fichier de configuration :

    [auth]
    ... directives de configuration de la sécurité ...

## Politique de sécurité utilisateur
La directive "SecurityPolicy" définit la politique de sécurité appliquée aux comptes des utilisateurs :

    [auth] 
    securityPolicy= "{
      'loginAttempts' : 3,
      'lockDelay' : 60,
      'passwordValidity' : 365,
      'passwordMinLength' : 8,
      'passwordRequiresSpecialChars' : 0,
      'passwordRequiresDigits' : 0,
      'passwordRequiresMixedCase' : 0,
      'sessionTimeout' : 3600,
      'newPasswordValidity : 1,
    }"

Les paramètres qui peuvent être ajustés sont détaillés ci-dessous :

**loginAttempts** : (entier) Nombre maximal d'échec de tentatives de connexion de compte utilisateur. 
Si ce nombre est atteint, le système verrouille automatiquement le compte de l'utilisateur pour empêcher 
les tentatives de connexion suivantes et protéger le système des méthodes d'intrusion qui utilisent la force brute. 
La valeur "0" ou l'absence de valeur désactive la fonction de verrouillage automatique.


**lockDelay** : (entier) Durée du verrouillage automatique des comptes d'utilisateurs, exprimée en secondes. 
Au-delà de cette durée, le compte est automatiquement déverrouillé. 
La valeur "0" ou l'absence de valeur provoque un verrouillage permanent. 
Ce dernier mode nécessite l'intervention de l'administrateur dans le panneau d'administration des utilisateurs 
afin de déverrouiller l'utilisateur.


**passwordValidity** : (nombre) Durée de la validité du mot de passe des comptes d'utilisateurs, exprimée en jours. 
Au-delà de cette période, il est demandé à l'utilisateur de modifier son mot de passe. 
La valeur "0" ou l'absence de valeur indique une validité sans limite de durée.


**passwordMinLength** : (nombre) Longueur minimale du mot de passe, exprimée en nombre de caractères. 
La valeur "0" ou l'absence de valeur indique que toutes les longueurs sont autorisées.


**passwordRequiresSpecialChars** : (indicateur) La valeur "1" indique que le mot de passe doit contenir des caractères spéciaux, 
autres que alphanumériques. La valeur "0" désactive cette option.


**passwordRequiresDigits** : (indicateur) La valeur "1" indique que le mot de passe doit contenir des chiffres. 
La valeur "0" désactive cette option.


**passwordRequiresMixedCase** : (indicateur) La valeur "1" indique que le mot de passe doit contenir à la fois des 
caractères alphabétiques en minuscule et en majuscule. La valeur "0" désactive cette option.


**sessionTimeout** : (nombre) Durée, exprimée en secondes, de validité de la session du compte utilisateur lorsqu'aucune 
activité n'est détectée par le système. La valeur "0" ou l'absence de valeur indique l'absence de limitation de la durée de validité.


**newPasswordValidity** : (nombre) Durée de la validité du mot de passe temporaire généré par le système pour les comptes d'utilisateurs, 
exprimée en jours. Au-delà de cette période, le mot de passe n'est plus valide et une nouvelle demande de mot de passe temporaire devra être émise. La valeur "0" ou l'absence de valeur indique une validité sans limite de durée.

## Algorithme de mot de passe
La directive "passwordEncryption" définit l'algorithme de hachage des mots de passe conservés dans le référentiel de sécurité de l'application. Les mots de passe sont hachés afin d'empêcher l'exploitation des données du compte utilisateur en cas d'intrusion dans la base de données des comptes d'utilisateur.

    [auth]
    passwordEncryption = SHA256

Les algorithmes disponibles sont ceux supportés par la fonction PHP hash_algos(). 
Pour éviter tout risque de hachage inverse, il est fortement conseillé d'utiliser un algorithme complexe tel que SHA512 ou GOST.

## Comptes d'utilisateurs réservés
La directive "adminUsers" liste les identifiants des utilisateurs administrateurs qui ne seront administrables que par eux-mêmes, 
à l'exclusion de tout autre compte habilité à gérer les utilisateurs.

    [auth]
    adminUsers = "['superadmin', 'root']"