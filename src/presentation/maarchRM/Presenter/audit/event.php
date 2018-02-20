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

            if (strpos($route, 'read') || strpos($route, 'get')) {
                $event->class = 'read';
            } elseif (strpos($route, 'create') || strpos($route, 'add') || strpos($route, 'new')) {
                $event->class = 'create';
            } elseif (strpos($route, 'update') || strpos($route, 'modify')) {
                $event->class = 'update';
            } elseif (strpos($route, 'delete')) {
                $event->class = 'delete';
            } else {
                $event->class = 'all';
            }
            $events[] = $event;
        }
        
        $this->view->addContentFile("audit/search.html");
        $this->view->setSource("events", $events);
        $this->view->merge();
        $this->view->translate();

        $this->view->addScriptSrc(
<<<EOD
    $.ajaxSetup({
        headers: { 'X-Laabs-Max-Count': 300}
    });
EOD
        );

        return $this->view->saveHtml();
    }

    /**
     * Get reseult
     *
     * @param Array $events Array of audit/event object
     *
     * @return string view
     */
    public function search($events)
    {
        $this->view->addContentFile("audit/result.html");
        $translator = $this->view->translator;

        $conf = \laabs::configuration('audit');
        if ($conf && array_key_exists('separateInstance', $conf))
        {        
            $multipleInstance = !(bool) $conf['separateInstance'];
        } else {
            $multipleInstance = false;
        }
        
        $this->view->setSource('multipleInstance', $multipleInstance);
        $this->view->setSource("events", $events);
        $this->view->merge();
        $this->view->translate();

        $table = $this->view->getElementById("list");
        $dataTable = $table->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        if ($multipleInstance) {
            $dataTable->setUnsortableColumns(5);
            $dataTable->setSorting(array(array(2, 'desc')));
        } else {
            $dataTable->setUnsortableColumns(4);
            $dataTable->setSorting(array(array(1, 'desc')));
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

        // Fix error on event info
        if(isset($event->info )) {
            foreach (json_decode($event->info) as $name => $value) {
                $event->info2[] = array('name'=> $name, 'value'=> $value);
            }
        }
        if (isset($event->input)) {
            if (is_array($event->input)) {
                foreach ($event->input as $name => $value) {
                    if (is_string($value) && strlen($value) > 70) {
                        $event->input[$name] = substr($value, 0, 70)."...";
                    }
                }
            }
        }

        $event->output = $this->view->translator->getText($event->output, false, "audit/messages");
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
