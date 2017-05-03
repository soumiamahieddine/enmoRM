<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle filePlan.
 *
 * Bundle filePlan is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle filePlan is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle filePlan.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\filePlan;

/**
 * Serializer html journal
 *
 * @package lifeCycle
 * @author  Maarch Prosper DE LAURE (Maarch) <prosper.delaure@maarch.com>
 */
class filePlan
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    private $translator;

    /**
     * Constuctor of archival Agreement html serializer
     * @param \dependency\html\Document $view         The view
     * @param \dependency\json\JsonObject                  $json
     * @param \dependency\localisation\TranslatorInterface $translator
     *
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
        $this->translator->setCatalog('filePlan/messages');
    }

    /**
     * Show the events search form
     * @param tree 
     *
     * @return string
     */
    public function showTree($filePlan)
    {
        
        $this->view->addContentFile('filePlan/filePlanTree.html');
        $this->markTreeLeaf([$filePlan]);

        $this->view->translate();
        $this->view->setSource("filePlan", [$filePlan]);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Mark leaf for html merging
     */
    protected function markTreeLeaf($tree) {
        foreach ($tree as $node) {
            if (!isset($node->organization) && !isset($node->folder)) {
                $node->isLeaf = true;
            }
        }
    }

    /**
     * Create folder response
     * @param string $folderId The new folder identifier 
     *
     * @return string
     */
    public function create($folderId)
    {
        $this->json->message = "New folder created";
        $this->json->folderId = $folderId;
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Update folder response
     *
     * @return string
     */
    public function update()
    {
        $this->json->message = "Folder updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Move folder response
     *
     * @return string
     */
    public function move()
    {
        $this->json->message = "Folder moved";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Delete folder response
     *
     * @return string
     */
    public function delete()
    {
        $this->json->message = "Folder deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
}
