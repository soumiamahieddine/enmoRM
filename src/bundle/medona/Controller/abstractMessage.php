<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\medona\Controller;

/**
 * Trait for all types of messages
 *
 * @author Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
abstract class abstractMessage extends message
{

    protected $message;

    /**
     * Load a message
     * @param medona/message $message
     */
    public function loadMessage($message)
    {
        $this->message = $message;

        if (!isset($message->xPath)) {
            $message->xPath = \laabs::newService('dependency/xml/XPath', $message->xml);
        }

        $message->xPath->registerNamespace('medona', 'org:afnor:medona:1.0');
        $message->xPath->registerNamespace('recordsManagement', 'maarch.org:laabs:recordsManagement');
        $message->xPath->registerNamespace('digitalResource', 'maarch.org:laabs:digitalResource');
    }

    protected function sendError($code, $message = false, $variable = null)
    {
        if ($message) {
            array_push($this->errors, new \core\Error($message, $variable, $code));
        } else {
            array_push($this->errors, new \core\Error($this->getReplyMessage($code), $variable, $code));
        }

        if ($this->replyCode == null) {
            $this->replyCode = $code;
        }
    }

    protected function getReplyMessage($code)
    {
        switch ((string) $code) {
            case "000":
                $name = "OK";
                break;
            case "001":
                $name = "OK (consulter les commentaires pour information)";
                break;
            case "002":
                $name = "OK (Demande aux autorités de contrôle effectuée)";
                break;

            case "101":
                $name = "Message mal formé.";
                break;
            case "102":
                $name = "Système momentanément indisponible.";
                break;
            case "103":
                $name = "Message déjà reçu.";
                break;
            case "104":
                $name = "Message en erreur.";
                break;

            case "200":
                $name = "Service producteur non reconnu.";
                break;
            case "201":
                $name = "Service d'archive non reconnu.";
                break;
            case "202":
                $name = "Service versant non reconnu.";
                break;
            case "203":
                $name = "Dépôt non conforme au profil de données.";
                break;
            case "204":
                $name = "Format de document non géré.";
                break;
            case "205":
                $name = "Format de document non conforme au format déclaré.";
                break;
            case "206":
                $name = "Signature du message invalide.";
                break;
            case "207":
                $name = "Empreinte(s) invalide(s).";
                break;
            case "208":
                $name = "Archive indisponible. Délai de communication non écoulé.";
                break;
            case "209":
                $name = "Archive absente (élimination, restitution, transfert)";
                break;
            case "210":
                $name = "Archive inconnue";
                break;
            case "211":
                $name = "Pièce attachée absente.";
                break;
            case "212":
                $name = "Dérogation refusée.";
                break;
            case "213":
                $name = "Identifiant de document incorrect";
                break;

            case "300":
                $name = "Convention invalide.";
                break;
            case "301":
                $name = "Dépôt non conforme à la convention. Quota des versements dépassé.";
                break;
            case "302":
                $name = "Dépôt non conforme à la convention. Identifiant du producteur non conforme.";
                break;
            case "303":
                $name = "Dépôt non conforme à la convention. Identifiant du service versant non conforme.";
                break;
            case "304":
                $name = "Dépôt non conforme à la convention. Identifiant du service d'archives non conforme.";
                break;
            case "305":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) absente(s).";
                break;
            case "306":
                $name = "Dépôt non conforme à la convention. Volume non conforme.";
                break;
            case "307":
                $name = "Dépôt non conforme à la convention. Format non conforme.";
                break;
            case "308":
                $name = "Dépôt non conforme à la convention. Empreinte(s) non transmise(s).";
                break;
            case "309":
                $name = "Dépôt non conforme à la convention. Absence de signature du message.";
                break;
            case "310":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) non valide(s).";
                break;
            case "311":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) non vérifiée(s).";
                break;
            case "312":
                $name = "Dépôt non conforme à la convention. Dates de début ou de fin non respectées.";
                break;

            case "400":
                $name = "Demande rejetée.";
                break;
            case "404":
                $name = "Message non trouvé.";
                break;

            default:
                $name = null;
        }

        return $name;
    }

    protected function sendUnitIdentifier($mUnitIdentifier)
    {
        $unitIdentifier = \laabs::newInstance('medona/Identifier', (string) $mUnitIdentifier->objectId);

        switch ($mUnitIdentifier->objectClass) {
            case 'recordsManagement/archive':
                $unitIdentifier->schemeName = 'ArchivalAgencyArchiveIdentifier';
                break;
        }

        return $unitIdentifier;
    }

    protected function sendOrganization($orgOrganization)
    {
        $organization = \laabs::newInstance('medona/Organization');

        $organization->identifier = \laabs::newInstance('medona/Identifier', $orgOrganization->registrationNumber);

        $organization->organizationDescriptiveMetadataClass = 'organization/organization';

        $organization->organizationDescriptiveMetadata = $orgOrganization;

        return $organization;
    }

    protected function useArchivalAgreement($archivalAgreementReference)
    {
        $archivalAgreementController = \laabs::newController('medona/archivalAgreement');

        if (!isset($this->archivalAgreements[$archivalAgreementReference])) {
            $this->currentArchivalAgreement = $archivalAgreementController->getByReference($archivalAgreementReference);
            $this->archivalAgreements[$archivalAgreementReference] = $this->currentArchivalAgreement;
        } else {
            $this->currentArchivalAgreement = $this->archivalAgreements[$archivalAgreementReference];
        }

        return $this->currentArchivalAgreement;
    }

    protected function getMessageTypeController($schema, $messageType)
    {
        if (!isset($this->packageSchemas[$schema])) {
            $schemaConf = $this->packageSchemas[$schema];
            if (isset($schemaConf['controllers'][$messageType])) {
                return \laabs::newController($schemaConf['controllers'][$messageType]);
            }
        }

        if (\laabs::hasBundle($schema)) {
            $bundle = \laabs::bundle($schema);

            if ($bundle->hasController($messageType)) {
                return \laabs::newController($schema.'/'.$messageType);
            }
        }
    }

    protected function logValidationErrors($message, $exception)
    {
        $eventInfo = get_object_vars($message);
        $eventInfo['code'] = $exception->getCode();
        $eventInfo['info'] = $exception->getMessage();
        foreach ((array) $exception->errors as $error) {
            if (is_string($error) || (is_object($error) && method_exists($error, '__toString'))) {
                $eventInfo['info'] .= PHP_EOL. (string) $error;
            }
        }

        $event = $this->lifeCycleJournalController->logEvent(
            'medona/sending',
            'medona/message',
            $message->messageId,
            $eventInfo,
            false
        );
    }
}
