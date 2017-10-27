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
namespace presentation\maarchRM\Presenter\recordsManagement;

/**
 * Serializer html service levels
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.com>
 */
class serviceLevel
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    
    protected $json;
    protected $translator;

    /**
     * Constuctor of archival profile html serializer
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
        $this->translator->setCatalog('recordsManagement/serviceLevel');
    }

    /**
     * Get archival profiles
     * @param array $archivalProfiles Array of archival profiles
     *
     * @return string
     */
    public function index(array $serviceLevel)
    {
        //$this->view->addHeaders();
       //$this->view->useLayout();
        $this->view->addContentFile('recordsManagement/serviceLevel/index.html');
        
        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(1, 2, 3);
        
        $this->view->translate();
        
        $this->view->setSource("serviceLevel", $serviceLevel);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * The view to add a service level
     * @param recordsManagement/serviceLevel $serviceLevel The archival profile object
     *
     * @return string
     */
    public function newServiceLevel($serviceLevel)
    {
        $this->view->addContentFile('recordsManagement/serviceLevel/edit.html');

        $clusterController = \laabs::newController('digitalResource/cluster');
        $clusters = $clusterController->index();
        $this->view->setSource("clusters", $clusters);
        $clusterSlector = $this->view->getElementById("digitalResourceClusterId");
        $this->view->merge($clusterSlector);

        $this->view->translate();
        $this->view->setSource("serviceLevel", $serviceLevel);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * The view to create or edit a archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     * @return string
     */
    public function read($serviceLevel)
    {
        $this->view->addContentFile('recordsManagement/serviceLevel/edit.html');
        $this->view->translate();

        if ($serviceLevel->control) {
            $controlList = explode(' ', $serviceLevel->control);
            foreach ($controlList as $control) {
                $serviceLevel->{$control} = true;
            }
        }

        $clusterController = \laabs::newController('digitalResource/cluster');
        $clusters = $clusterController->index();
        $this->view->setSource("clusters", $clusters);
        $clusterSlector = $this->view->getElementById("digitalResourceClusterId");
        $this->view->merge($clusterSlector);

        $this->view->setSource("serviceLevel", $serviceLevel);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    protected function listProperties($class, &$properties, &$dateProperties, $containerClass ='')
    {
        foreach ($class->getProperties() as $property) {
            $type = $property->getType();
            if ($type == "date" || $type == "timestamp") {
                array_push($dateProperties, $containerClass.$property->name);
            }
            array_push($properties, $containerClass.$property->name);
            if (!$property->isScalar()) {
                $childClass = \laabs::getType($type);
                $this->listProperties($childClass, $properties , $dateProperties, $containerClass.$property->name.'/');
            }
        }
    }
    
    //JSON
    /**
     * Serializer JSON for create method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function create()
    {
        $this->json->message = "Service level created.";
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
        $this->json->message = "Service level updated.";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    } 

    /**
     * Serializer JSON for delete method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function delete($result)
    {
        if ($result == true) {
            $this->json->message = "Service level removed.";
            $this->json->message = $this->translator->getText($this->json->message);
        } else {
            $this->json->message = "The  default service level or used can't be deleted.";
            $this->json->status = false;
            $this->json->message = $this->translator->getText($this->json->message);
        }
        return $this->json->save();
    }

    /**
     * Serializer JSON for setDefault method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function setDefault()
    {
        $this->json->message = "The default service level is changed.";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
}
