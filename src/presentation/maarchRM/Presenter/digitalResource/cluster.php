<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle digitalResource.
 *
 * Bundle digitalResource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle digitalResource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Presentation\maarchRM\Presenter\digitalResource;

/**
 * Serializer html cluster
 *
 * @package DigitalResource
 * @author  Alexis Ragot <alexis.ragot@maarch.com>
 */
class cluster
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    protected $json;

    protected $translator;

    /**
     * Constuctor of cluster html serializer
     * @param \dependency\html\Document $view The view
     */
    public function __construct(
            \dependency\html\Document $view,
            \dependency\json\JsonObject $json,
            \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('digitalResource/cluster');
    }

    /**
     * Get cluster
     * @param array $clusters Array of digitalResource/cluster objects
     *
     * @return string
     */
    public function index(array $clusters)
    {
        $this->view->addContentFile('digitalResource/cluster/index.html');

        foreach ($clusters as $cluster) {
            $cluster->repositoriesNumber = count($cluster->clusterRepository);
            $cluster->stat = -1;

            // Maximum cluster size in Gb
            $maxSize = 0;
            $unitConversionPower = 0;

            foreach ($cluster->clusterRepository as $clusterRepository) {
                $repository = \laabs::callService('digitalResource/repository/read_repositoryId_', $clusterRepository->repositoryId);
                if ($repository->maxSize && ($maxSize == 0 || $repository->maxSize < $maxSize)) {
                    $maxSize = $repository->maxSize;
                }
            }


            if ($maxSize != 0) {
                if ($cluster->size->gbSize > 0) {
                    $cluster->stat = round(($cluster->size->gbSize + $cluster->size->bSize / 1073741824) * 100 / $maxSize);
                } else {
                    $cluster->stat = round($cluster->size->bSize * 100 / ($maxSize * 1073741824));
                }

                $cluster->statColor = 'success';

                if ($cluster->stat >= 90) {
                    $cluster->statColor = 'danger';
                } elseif ($cluster->stat >= 70) {
                    $cluster->statColor = 'warning';
                }
            }

            $cluster->size->value = '0';
            $cluster->size->unit = 'b';

            if ($cluster->size->gbSize == 0) {
                if ($cluster->size->bSize > 1048576 ) {
                    $cluster->size->value = (string) round($cluster->size->bSize / 1048576, 2);
                    $cluster->size->unit = 'Mb';

                } elseif ($cluster->size->bSize > 1024) {
                    $cluster->size->value = (string) round($cluster->size->bSize / 1024, 2);
                    $cluster->size->unit = 'Kb';
                }
            } else {
                $cluster->size->value = (string) round($cluster->size->gbSize + $cluster->size->bSize / 1073741824, 2);
                $cluster->size->unit = 'Gb';
            }

        }

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(4);

        $this->view->translate();

        $this->view->setSource("clusters", $clusters);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Edit cluster
     * @param digitalResource/cluster $cluster digitalResource/cluster object
     *
     * @return string
     */
    public function edit($cluster = null)
    {
        $this->view->addContentFile('digitalResource/cluster/edit.html');

        $repositories = \laabs::newController("digitalResource/repository")->index();

        if ($cluster != null && $cluster->clusterId != null) {
            $repositoriesCopy = $repositories;
            $repositoriesHide = array();
            foreach ($cluster->clusterRepository as $clusterRepo) {
                for ($i =0; $i < count($repositories); $i++) {
                    if ($repositories[$i]->repositoryId == $clusterRepo->repositoryId) {
                        $clusterRepo->repositoryName = $repositories[$i]->repositoryName;
                        $repositoriesHide[] = $repositories[$i];
                        unset($repositoriesCopy[$i]);
                    }
                }
            }
            $repositories = $repositoriesCopy;
        }

        $this->view->translate();

        if ($cluster != null && $cluster->clusterId != null) {
            $this->view->setSource("cluster", $cluster);
            $this->view->setSource("repositoriesHide", $repositoriesHide);
        }
        $this->view->setSource("repositories", $repositories);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    // JSON
    /**
     * Serializer JSON for create method
     *
     * @return object JSON object with a status and message parameters
     */
    public function create()
    {
        $this->json->message = "Cluster created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for update method
     *
     * @return object JSON object with a status and message parameters
     */
    public function update()
    {
        $this->json->message = "Cluster updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Cluster exception
     * @param digitalResource/Exception/clusterException $clusterException
     *
     * @return string
     */
    public function clusterException($clusterException)
    {
        $this->json->message = $clusterException->getMessage();
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->status = false;

        return $this->json->save();
    }
}
