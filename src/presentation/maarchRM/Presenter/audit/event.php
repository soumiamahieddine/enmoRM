<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle audit.
 *
 * Bundle audit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle audit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle audit.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Presentation\maarchRM\Presenter\audit;

/**
 * Bundle audit html serializer
 *
 * @package Audit
 */
class event
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    protected $json;

    /**
     * __construct
     *
     * @param \dependency\html\Document   $view A new ready-to-use empty view
     * @param \dependency\json\JsonObject $json A new ready-to-use empty json
     *
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;
    }

    /**
     * index of events
     * @param array $events Array of events
     *
     * @return string view
     */
    public function byObject(array $events)
    {
        $this->view->addContentFile("audit/events.html");

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        $this->view->translate();
        $translator = $this->view->translator;

        foreach ($events as $event) {
            $catalog = $event->origin.LAABS_URI_SEPARATOR."messages";

            $event->message = $translator->getText($event->message, false, $catalog);
            $event->mergeMessage();
        }

        $this->view->setSource("events", $events);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get form to search events
     *
     * @return string view
     */
    public function index()
    {
        $events = array();
        $routes = array();
        $translator = $this->view->translator;
        
        $bundles = \laabs::bundles();
        foreach ($bundles as $bundle) {
            $apis = $bundle->getApis();
            foreach ($apis as $api) {
                $paths = $api->getPaths();
                foreach ($paths as $path) {
                    if (!strpos($path, 'audit')) {
                        $routes[] = $path->getName();
                    }
                }
            }
        }

        $routes = array_unique($routes);
        foreach ($routes as $route) {
            $event = new \stdClass();
            $event->path = $route;
            $event->label = $translator->getText($event->path, false, "audit/messages");
            $events[] = $event;
        }

        $maxResults = null;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])) {
            $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        }

        $this->view->addContentFile("audit/search.html");
        $this->view->setSource("events", $events);
        $this->view->setSource("maxResults", $maxResults);
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get result
     *
     * @param array   $events       Array of audit/event object
     * @param integer $totalResults Max number of total results from query
     *
     * @return string view
     */
    public function search($events, $totalResults)
    {
        $this->view->addContentFile("audit/result.html");

        $conf = \laabs::configuration('audit');
        if ($conf && array_key_exists('separateInstance', $conf)) {
            $multipleInstance = !(bool) $conf['separateInstance'];
        } else {
            $multipleInstance = false;
        }

        $hasReachMaxResults = false;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])
            && $totalResults >= \laabs::configuration('presentation.maarchRM')['maxResults']) {
            $hasReachMaxResults = true;
        }

        $this->view->setSource('hasReachMaxResults', $hasReachMaxResults);
        $this->view->setSource('totalResults', $totalResults);
        $this->view->setSource('multipleInstance', $multipleInstance);
        $this->view->setSource("events", $events);
        $this->view->merge();
        $this->view->translate();

        $table = $this->view->getElementById("list");
        $dataTable = $table->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        if ($multipleInstance) {
            $dataTable->setUnsortableColumns(5);
            $dataTable->setSorting(array(array(1, 'desc')));
        } else {
            $dataTable->setUnsortableColumns(4);
            $dataTable->setSorting(array(array(0, 'desc')));
        }

        return $this->view->saveHtml();
    }

    /**
     * Get event
     * @param audit/event $event Object event
     *
     * @return audit/event $event Object event
     */
    public function getevent($event)
    {
        $this->view->addContentFile("audit/modalEvent.html");

        $this->view->translate();

        if (isset($event->info)) {
            foreach (json_decode($event->info) as $name => $value) {
                $nameTraduction = $this->view->translator->getText($name, false, "audit/messages");
                $event->info2[] = array('name'=> $nameTraduction, 'value'=> $value);
            }
        }

        if ($event->output) {
            $output = [];
            $outputObject = json_decode($event->output);
            if (is_array($outputObject)) {
                foreach ($outputObject as $outputMessage) {
                    if (isset($outputMessage->message)) {
                        $outputMessage->message = $this->view->translator->getText($outputMessage->message, false, "audit/messages");
                        if (isset($outputMessage->variables)) {
                            $output[] = vsprintf($outputMessage->message, $outputMessage->variables);
                        } else {
                            $output[] = $outputMessage->message;
                        }
                    }
                }

                $event->output = $output;
            } elseif (is_string($event->output)) {
                $event->output = [$this->view->translator->getText($event->output, false, "audit/messages")];
            }
        }

        $event->pathTraduction = $this->view->translator->getText($event->path, false, "audit/messages");
        $this->view->setSource("event", $event);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    //JSON
    /**
     * Exception
     * @param audit/Exception/eventException $eventException
     *
     * @return string
     */
    public function eventException($eventException)
    {
        $this->json->load($eventException);
        $this->json->status = false;

        return $this->json->save();
    }
}
