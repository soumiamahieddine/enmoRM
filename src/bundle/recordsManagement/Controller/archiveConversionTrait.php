<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Trait for archives conversion
 */
trait archiveConversionTrait
{
    /**
     * Convert and store the resource
     *
     * @param id $resId The resource identifier
     *
     * @return digitalResource/digitalResource The converted resource
     */
    public function convertAndStore($resId)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        $convertedResource = null;

        try {
            $digitalResource = $this->digitalResourceController->retrieve($resId);
            $archive = $this->sdoFactory->read("recordsManagement/archive", $digitalResource->archiveId);
            $convertedResource = $this->convertResource($archive, $digitalResource);

            $status = false;
            if ($convertedResource != false) {
                $this->useServiceLevel('deposit', $archive->serviceLevelReference);

                $this->digitalResourceController->openContainers($this->currentServiceLevel->digitalResourceClusterId, $archive->storagePath);
                $this->digitalResourceController->store($convertedResource);
                $status = true;
            }

            $this->logConvertion($digitalResource, $convertedResource, $archive, $status);
        } catch (\Exception $e) {
            if (isset($convertedResource)) {
                $this->digitalResourceController->rollbackStorage($convertedResource);
            }

            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $convertedResource;
    }

    /**
     * Convert a resource by the it's identifier
     *
     * @param id $resId The resource identifier
     *
     * @return digitalResource/digitalResource The converted resource
     */
    public function convert($resId)
    {
        $digitalResource = $this->digitalResourceController->retrieve($resId);
        $archive = $this->sdoFactory->read("recordsManagement/archive", $digitalResource->archiveId);

        $convertedResource = $this->convertResource($archive, $digitalResource);

        $status = false;
        if ($convertedResource != false) {
            $status = true;
        }

        $this->logConvertion($digitalResource, $convertedResource, $archive, $status);

        return $convertedResource;
    }

    /**
     * Convert a resource
     *
     * @param recordsManagement/archive       $archive         The archive
     * @param digitalResource/digitalResource $digitalResource The digital resource to convert
     *
     * @return digitalResource/digitalResource The converted resource
     */
    public function convertResource($archive, $digitalResource)
    {
        $conversionRules = \laabs::newController("digitalResource/conversionRule")->index();

        if (empty($conversionRules)) {
            return false;
        }

        if (!$this->currentServiceLevel) {
            if (isset($archive->serviceLevelReference)) {
                $this->useServiceLevel('deposit', $archive->serviceLevelReference);
            } else {
                $this->useServiceLevel('deposit');
            }
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $convertedResource = $this->digitalResourceController->convert($digitalResource);
        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            if (isset($convertedResource)) {
                $this->digitalResourceController->rollbackStorage($convertedResource);
                $this->logConvertion($digitalResource, $convertedResource, $archive, false);
            }

            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        if ($convertedResource == false) {
            return $convertedResource;
        }

        return $convertedResource;
    }
    
}
