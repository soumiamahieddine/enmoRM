<?php

/*
 *  Copyright (C) 2017 Maarch
 * 
 *  This file is part of bundle XXXX.
 *  Bundle XXXX is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  Bundle XXXX is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with bundle XXXX.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Archive access controller
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class archiveAccess
{
    /**
     * Get archive metadata
     * @param string $archiveId The archive identifier
     */
    public function getMetadata($archiveId)
    {
        // Récupérer les métadonnées
    }

    /**
     * Validate archive access
     * @param string $archiveId The archive identifier
     */
    public function accessVerification($archiveId)
    {
        // Contrôler les droits (rôle acteur, règle applicable sur archive, droits sur profil…) sur données
    }

    /**
     * Get archive package, data and metadata
     * @param string $archiveId The archive identifier
     */
    public function getPackage($archiveId)
    {
        // Récupérer les paquets (data+méta)
    }

    /**
     * Get an archive package for the communication
     * @param string $archiveId The archive identifier
     */
    public function getConmmunicationPackage($archiveId)
    {
        // Constituer les paquets à communiquer
    }

    /**
     * Send archive for consultation
     */
    public function sendForConsultation()
    {
        // Envoyer pour consultation simple
    }

    /**
     * Log the archive access
     * @param recordsManagement/archive $archive The archive logged
     */
    public function logging($archive)
    {
        // Journaliser
    }
}
