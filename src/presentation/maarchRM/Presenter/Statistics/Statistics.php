<?php

/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle Statistics.
 *
 * Bundle Statistics is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Statistics is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Statistics.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\Statistics;

/**
 * Serializer html for access code
 *
 * @package Statistics
 * @author  Jérôme Boucher <jerome.boucher@maarch.com>
 */
class Statistics
{
    public $view;

    protected $json;

    protected $translator;

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The json base object
     * @param \dependency\localisation\TranslatorInterface $translator The translator object
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {

        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('Statistics/Statistics');
    }

    /**
     *
     * @param  array $statistics associative array of statistcics and their values
     *
     * @return [type]             [description]
     */
    public function index($statistics)
    {
        $statistics['evolution'] = $this->getEvolution($statistics);

        $this->view->addContentFile("Statistics/index.html");

        $descriptionFragment = $this->view->createDocumentFragment();
        $description = $this->view->getElementById("statsResults");
        if (\laabs::configuration('medona')['transaction']) {
            $descriptionFragment->appendHtmlFile("Statistics/transactionnalResults.html");
        } else {
            $descriptionFragment->appendHtmlFile("Statistics/defaultResults.html");
        }
        $description = $this->view->getElementById("statsResults");
        $description->appendChild($descriptionFragment);

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setSorting(array(array(4, 'desc')));

        $this->view->setSource('statistics', $statistics);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    protected function rearrangeArray($array, $statType)
    {
        $results = [];
        foreach ($array as $key => $statistic) {
            if (array_key_exists('originatingorg', $statistic)) {
                $results[$statistic['originatingorg']] = $statistic[$statType];
            } else {
                $results[$statistic['archivalprofile']] = $statistic[$statType];
            }
        }

        return $results;
    }

    protected function getEvolution($statistics)
    {
        $evolution = $statistics['depositMemorySize'] - $statistics['deletedMemorySize'];
        if (\laabs::configuration('medona')['transaction']) {
            $evolution = $statistics['depositMemorySize'] - $statistics['deletedMemorySize'] - $statistics['transferredMemorySize'] - $statistics['restitutionMemorySize'];
        }

        return $evolution;
    }

    public function retrieveStats($statistics, $operation)
    {
        $statistics['evolution'] = $this->getEvolution($statistics);

        if (isset($statistics['groupedDepositMemorySize'])) {
            $groupedDepositMemorySize = $this->rearrangeArray($statistics['groupedDepositMemorySize'], 'sum');
        }

        if (isset($statistics['groupedDepositMemoryCount'])) {
            $groupedDepositMemoryCount = $this->rearrangeArray($statistics['groupedDepositMemoryCount'], 'count');
        }

        if (isset($statistics['deletedGroupedMemorySize'])) {
            $deletedGroupedMemorySize = $this->rearrangeArray($statistics['deletedGroupedMemorySize'], 'sum');
        }

        if (isset($statistics['deletedGroupedMemoryCount'])) {
            $deletedGroupedMemoryCount = $this->rearrangeArray($statistics['deletedGroupedMemoryCount'], 'count');
        }

        if (isset($statistics['groupedArchiveSize'])) {
            $groupedArchiveSize = $this->rearrangeArray($statistics['groupedArchiveSize'], 'sum');
        }

        if (isset($statistics['groupedArchiveCount'])) {
            $groupedArchiveCount = $this->rearrangeArray($statistics['groupedArchiveCount'], 'count');
        }

        // $condensedStats  =[];
        // foreach (array_merge($groupedDepositMemorySize, $groupedDepositMemoryCount, $deletedGroupedMemorySize, $deletedGroupedMemoryCount, $groupedArchiveSize, $groupedArchiveCount) as $type => $value) {
        //         $condensedStats[$type]['deposit']['sum'] = isset($groupedDepositMemorySize[$type]) ? $groupedDepositMemorySize[$type] : null;
        //         $condensedStats[$type]['deposit']['count'] = isset($groupedDepositMemoryCount[$type]) ? $groupedDepositMemoryCount[$type] : null;
        //         $condensedStats[$type]['deleted']['sum'] = isset($deletedGroupedMemorySize[$type]) ? $deletedGroupedMemorySize[$type] : null;
        //         $condensedStats[$type]['deleted']['count'] = isset($deletedGroupedMemoryCount[$type]) ? $deletedGroupedMemoryCount[$type] : null;
        //         $condensedStats[$type]['archived']['sum'] = isset($groupedArchiveSize[$type]) ? $groupedArchiveSize[$type] : null;
        //         $condensedStats[$type]['archived']['count'] = isset($groupedArchiveCount[$type]) ? $groupedArchiveCount[$type] : null;
        // }

        if (\laabs::configuration('medona')['transaction']) {
            $this->view->addContentFile("Statistics/transactionnalResults.html");
        } else {
            $this->view->addContentFile("Statistics/defaultResults.html");
        }

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setSorting(array(array(4, 'desc')));

        $this->view->setSource('statistics', $statistics);
        // $this->view->setSource('condensedStats', $condensedStats);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }
}
