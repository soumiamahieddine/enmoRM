<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency html.
 *
 * Dependency html is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency html is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency html.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\html\plugins\DataTree;
class DataTree
    extends \dependency\html\AbstractHtmlClass
{
    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    protected $parameters;
    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    public function __construct($element)
    {
        parent::__construct($element);
        $this->parameters = new \StdClass();
    }
    public function saveHtml() {
        $dataTreeId = \laabs\uniqid();
        $this->element->setAttribute('data-tree-id', $dataTreeId);
        $parameters = json_encode($this->parameters);
        $scriptText =
<<<EOS
$(document).ready(function() {
    $('*[data-tree-id="$dataTreeId"]').dataTree($parameters);
});
EOS;
        $script = $this->element->ownerDocument->createElement('script');
        $CdataSection = $this->element->ownerDocument->createCDataSection($scriptText);
        $script->appendChild($CdataSection);
        $this->element->appendChild($script);
    }
    public function setIconBranchClose($icon)
    {
        $this->parameters->iconBranchClose = $icon;
    }
    public function setIconBranchOpen($icon)
    {
        $this->parameters->iconBranchOpen = $icon;
    }
    public function setIconLeaf($icon)
    {
        $this->parameters->iconLeaf = $icon;
    }
    public function setToggleEasing($easing)
    {
        $this->parameters->toggleEasing = $easing;
    }
    public function setToggleDuration($duration)
    {
        $this->parameters->toggleDuration = $duration;
    }
    public function setExpanded($expanded)
    {
        $this->parameters->expanded = $expanded;
    }
}