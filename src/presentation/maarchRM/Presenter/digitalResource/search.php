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
 * searchResource html serializer
 *
 * @package DigitalResource
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class search
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    /**
     * Constuctor of search html serializer
     * @param \dependency\html\Document $view The view
     */
    public function __construct(\dependency\html\Document $view)
    {
        $this->view = $view;
    }

    /**
     * get a form to search resource
     * @param object $cluster 
     *
     * @return string
     */
    public function form($cluster)
    {
        //$this->view->addHeaders();
       //$this->view->useLayout();
        $this->view->addContentFile("digitalResource/digitalResource/searchResource.html");

        $this->view->setSource('cluster', $cluster);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    public function getProfileMetadata($profileMetadata)
    {
        $this->view->addContentFile("digitalResource/digitalResource/metadataList.html");

        $metadataPropreties = $this->view->getElementById("metadataPropreties");

        $activeClass = $this->view->createAttribute('class');
        $activeClass->value = "active";

        $firstPanelId = null;
        foreach ($profileMetadata as $metadata) {
            $identifier = str_replace('/', '', $metadata->objectClass);
            $metadata->identifier = $identifier;

            $panel = $this->view->createElement('div');
            $attribute = $this->view->createAttribute('class');
            $attribute->value = 'tab-pane';
            $panel->appendChild($attribute);

            $attribute = $this->view->createAttribute('id');
            $attribute->value = $identifier;
            $panel->appendChild($attribute);

            $attribute = $this->view->createAttribute('data-classname');
            $attribute->value = $metadata->objectClass;
            $panel->appendChild($attribute);

            if(!$firstPanelId) {
                $firstPanelId = $identifier;
                $panel->setAttribute('class','tab-pane active' );
                $metadata->active = 'active';
            }


            foreach ($metadata->properties as $property) {

                $textInputFragment = $this->view->createDocumentFragment();

                switch($property->getType()) {
                    case 'string' :
                        $textInputFragment->appendHtmlFile("digitalResource/digitalResource/textInputFragment.html");
                        break;

                    case 'date' :
                        $textInputFragment->appendHtmlFile("digitalResource/digitalResource/dateInputFragment.html");
                        break;

                    case 'numeric' :
                        $textInputFragment->appendHtmlFile("digitalResource/digitalResource/numericInputFragment.html");
                        break;

                    default :
                        $textInputFragment->appendHtmlFile("digitalResource/digitalResource/textInputFragment.html");
                }
                $this->view->merge($textInputFragment, $property);
                $panel->appendChild($textInputFragment);
            }

            $metadataPropreties->appendChild($panel);
        }

        $this->view->setSource('metadatas', $profileMetadata);
        $this->view->merge();
    
        return $this->view->saveHtml();
    }

    /**
     * get a result of global research
     * @param array $digitalResources Array of object digitalResource/digitalResource
     * 
     * @return string
     */
    public function getResultSearch(array $digitalResources)
    {
        $this->view->addContentFile("digitalResource/digitalResource/resultSearch.html");
        $this->view->setSource('digitalResources', $digitalResources);
        $this->view->merge();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        return $this->view->saveHtml();
    }
}
