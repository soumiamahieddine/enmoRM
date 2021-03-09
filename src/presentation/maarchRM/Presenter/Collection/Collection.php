<?php

/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle Collection.
 *
 * Bundle Collection is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Collection is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Collection.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\Collection;

/**
 * Serializer html for collection
 *
 * @package Collection
 * @author  Jérôme Boucher <jerome.boucher@maarch.com>
 */
class Collection extends \presentation\maarchRM\Presenter\recordsManagement\archive
{

    /**
     * get a form to search resource
     * @param Collection/Collection $collection Collection Object
     *
     * @return string
     */
    public function index($collection)
    {
        $archives = \laabs::callService('recordsManagement/archives/readArchives', $collection->archiveIds);


        $html = parent::search($archives, \laabs::configuration("presentation.maarchRM")["maxResults"]);

        $this->view->addContentFile("Collection/Collection.html");
        $collectionListHtmlHandler = $this->view->getElementById('collectionList');
        $this->view->addContent($html, $collectionListHtmlHandler);
        $this->view->merge();

        $this->view->translate();

        // var_dump($this->view->saveHtml());
        // exit;
        return $this->view->saveHtml();
    }
}
