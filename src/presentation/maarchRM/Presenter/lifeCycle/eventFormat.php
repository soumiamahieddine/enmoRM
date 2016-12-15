<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle lifeCycle.
 *
 * Bundle lifeCycle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle lifeCycle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle lifeCycle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\lifeCycle;

/**
 * Serializer html journal
 *
 * @package lifeCycle
 * @author Alexis RAGOT <alexis.ragot@maarch.org>
 */
class eventFormat
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    private $sdoFactory;

    private $json;

    private $translator;

    /**
     * Constuctor of event format html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\sdo\Factory                      $sdoFactory The sdo factory
     * @param \dependency\json\JsonObject                  $json
     * @param \dependency\localisation\TranslatorInterface $translator
     */
    public function __construct(\dependency\html\Document $view, \dependency\sdo\Factory $sdoFactory, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->sdoFactory = $sdoFactory;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('lifeCycle/messages');
    }

    /**
     * Show the events types index
     * @param array $eventsFormats the array of event format object
     *
     * @return string
     */
    public function index($eventsFormats)
    {
        $this->view->addContentFile('lifeCycle/eventFormat/index.html');

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(4);
        $dataTable->setUnsearchableColumns(4);

        $this->view->translate();

        $this->view->setSource("eventsFormats", $eventsFormats);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Create an event format
     * @param lifeCycle/eventFormat $eventFormat The event format object created
     *
     * @return string
     */
    public function create($eventFormat)
    {
        $this->json->message = "Event format created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
    
    /**
     * Update an event format
     * @param lifeCycle/eventFormat $eventFormat The event format object created
     *
     * @return string
     */
    public function update($eventFormat)
    {
        $this->json->message = "Event format updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Delete an event format
     *
     * @return string
     */
    public function delete()
    {
        $this->json->message = "Event format deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
    
    //JSON

    /**
     * Return event format
     * @param recordsManagement/archiveRetentionRule $retentionRule
     *
     * @return string
     */
    public function edit($eventFormat)
    {
        $this->json->eventFormat = $eventFormat;
        
        return $this->json->save();
    }
}
