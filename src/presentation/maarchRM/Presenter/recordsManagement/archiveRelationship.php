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
 * batch html serializer
 *
 * @package RecordsManagement
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class archiveRelationship
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    
    public $view;
     
    protected $json;

    protected $translator;

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The json object handler
     * @param \dependency\localisation\TranslatorInterface $translator The translator service
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
        $this->translator->setCatalog('recordsManagement/archiveRelationship');
    }

    /**
     * get a view to create a relationship
     *
     * @return string The form
     */
    public function form()
    {
        //$this->view->addHeaders();
       //$this->view->useLayout();
        $this->view->addContentFile("recordsManagement/archiveRelationship/form.html");
        
        $relationType = array('contains') ;
        $this->view->setSource("relationType", $relationType);
        $this->view->merge();

        $this->view->translate();

        return $this->view->saveHtml();
    }
    
    /**
     * Get archive relationships
     * 
     * @param string $archiveRelationships The archive relationships
     * 
     * @return string
     */
    public function getByArchiveId($archiveRelationships)
    {
        
        $this->view->addContentFile("recordsManagement/archiveRelationship/relationshipTable.html");
        $this->view->setSource("archiveRelationships", $archiveRelationships);
        $this->view->merge();
        $this->view->translate();
        
        return $this->view->saveHtml();
    }
    
    
    //JSON
    public function unknownArchive($exception)
    {
        $this->json->status = false;
        $this->translator->setCatalog('recordsManagement/exception');
        $this->json->message = $this->translator->getText("Archive with identifier '%s' doesn't exists");
        $this->json->message = sprintf($this->json->message, $exception->getMessage());
        
        return $this->json->save();
    }
    
    public function sameArchivesException()
    {
        $this->json->status = false;
        $this->translator->setCatalog('recordsManagement/exception');
        $this->json->message = $this->translator->getText("Archive identifiers are identical");
        
        return $this->json->save();
    }
    
    public function archiveRelationshipException()
    {
        $this->json->status = false;
        $this->translator->setCatalog('recordsManagement/exception');
        $this->json->message = $this->translator->getText("Archive relationship already exists");
        
        return $this->json->save();
    }
}