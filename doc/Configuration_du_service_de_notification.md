Configuration du service de notification
=========================================
L'opérateur du système d'archivage configure l'envoi de notifications par courriel 
sous la forme de valeurs de directives de configuration de la dépendance Notification.

Dans la configuration, la directive suivante est placée dans la section correspondant à la dépendance:

    [dependency.notification]
    @Adapter = Mailer
    mailHost = 'smtp.gmail.com'
    mailUsername = 'username@maarch.org';
    mailPassword = 'azerty';
    mailPort = 25;
    mailSender = "noreplay@maarch.org";
    mailAdminReceiver = "administrateur@maarch.org";
    mailSMTPAuth = true;
    mailSMTPSecure = "tls";

La liste ci-dessous détaille les paramètres qui peuvent être ajustés :

**mailHost** : (chaîne de caractère) adresse du serveur SMTP permettant l’envoi de courriel.

**mailUsername** : (chaîne de caractère) identifiant de connexion au serveur SMTP, généralement cela se présente sous la forme d’un courriel.

**mailPassword** : (chaîne de caractère) mot de passe de connexion au serveur SMTP.

**mailPort** : (nombre) port de connexion au serveur SMTP.

**mailSender** : (chaîne de caractère) courriel affiché comme émetteur;

**mailAdminReceiver** : (chaîne de caractère) courriel de l’administrateur du système. Cette configuration permet à l’administrateur de recevoir des notification par mail en cas d’erreur ou lors d’un suivi des événement du journal de l’application.

**mailSMTPAuth** : (boolean) La valeur "true" indique l’activation de l’authentification sur le serveur SMTP. La valeur “false” désactive l’authentification.

**mailSMTPSecure** : (chaîne de caractère) indique le mode de sécurisation du serveur SMTP.
