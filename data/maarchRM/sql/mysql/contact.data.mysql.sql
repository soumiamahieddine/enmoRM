DELETE FROM `contact.communication`;
DELETE FROM `contact.address`;
DELETE FROM `contact.contact`;
DELETE FROM `contact.communicationMean`;

-- communicationMean --
INSERT INTO `contact.communicationMean` (`code`, `name`, `enabled`) VALUES
    ('TE','Téléphone',true),
    ('AL','Téléphone mobile',true),
    ('FX','Fax',true),
    ('AO','URL',true),
    ('AU','FTP',true),
    ('EM','E-mail',true),
    ('AH','World Wide Web',false);
