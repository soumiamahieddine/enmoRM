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
namespace dependency\html\plugins\DataList;
class DataList
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
        $this->parameters->items = '*[data-list-item]';
        $this->parameters->pagination = new \StdClass();
        $this->parameters->pagination->visible  = 12;
        $this->parameters->pagination->prev     = '*[data-list-pagination-prev]';
        $this->parameters->pagination->next     = '*[data-list-pagination-next]';
        $this->parameters->pagination->info     = '*[data-list-pagination-info]';
        $this->parameters->pagination->save     = true;
        $this->parameters->filter = new \StdClass();
        $this->parameters->filter->input        = '*[data-list-filter-input]';
        $this->parameters->filter->fields       = '*[data-list-filter-field]';
        $this->parameters->filter->threshold    = 2;
        $this->parameters->filter->highlight    = 'label label-info';
        $this->parameters->filter->save         = true;
    }
    public function saveHtml()
    {
        $dataListContainer = \laabs\uniqid();
        $this->element->setAttribute('data-list-container', $dataListContainer);
        $parameters = json_encode($this->parameters);
        $scriptText =
<<<EOS
$(document).ready(function() {
    $('*[data-list-container="$dataListContainer"]:first').dataList($parameters);
});
EOS;
        $script = $this->element->ownerDocument->createElement('script');
        $CdataSection = $this->element->ownerDocument->createCDataSection($scriptText);
        $script->appendChild($CdataSection);
        $this->element->appendChild($script);
    }
    public function setItemsSelector($itemsSelector)
    {
        $this->parameters->items = $itemsSelector;
    }
    public function disablePagination()
    {
        $this->parameters->pagination->visible = false;
    }
    public function setPaginationNbItems($paginationNbItems)
    {
        $this->parameters->pagination->visible = $paginationNbItems;
    }
    public function setPaginationPreviousSelector($previousSelector)
    {
        $this->parameters->pagination->prev = $previousSelector;
    }
    public function setPaginationNextSelector($nextSelector)
    {
        $this->parameters->pagination->prev = $nextSelector;
    }
    public function setPaginationInfoSelector($infoSelector)
    {
        $this->parameters->pagination->prev = $infoSelector;
    }
    public function setPaginationSave($savePagination)
    {
        $this->parameters->pagination->save = $savePagination;
    }
    public function setFilterInputSelector($filterInputSelector)
    {
        $this->parameters->filter->input = $filterInputSelector;
    }
    public function setFilterFieldsSelector($filterFieldsSelector)
    {
        $this->parameters->filter->fields = $filterFieldsSelector;
    }
    public function setFilterThreshold($filterFieldsSelector)
    {
        $this->parameters->filter->threshold = $filterFieldsSelector;
    }
    public function setFilterHighlightClass($filterHighlightClass)
    {
        $this->parameters->filter->highlight = $filterHighlightClass;
    }
    public function setFilterSave($saveFilter)
    {
        $this->parameters->filter->save = $saveFilter;
    }
}