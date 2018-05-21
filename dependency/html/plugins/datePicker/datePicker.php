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
namespace dependency\html\plugins\datePicker;
/**
 * Class for datePicker plugin
 */
class datePicker
    extends \dependency\html\AbstractHtmlClass
{

    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    /**
     * The parameters
     *  autoclose
     *  beforeShowDay
     *  beforeShowMonth
     *  calendarWeeks
     *  clearBtn
     *  toggleActive
     *  container
     *  daysOfWeekDisabled
     *  datesDisabled
     *  defaultViewDate
     *  endDate
     *  forceParse
     *  format
     *  inputs
     *  keyboardNavigation
     *  language
     *  minViewMode
     *  multidate
     *  multidateSeparator
     *  orientation
     *  startDate
     *  startView
     *  todayBtn
     *  todayHighlight
     *  weekStart
     */
    protected $parameters;

    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    /**
     * Constructor
     * @param DOMElement $element The html element with class="datepicker"
     */
    public function __construct($element)
    {
        parent::__construct($element);
        
        $this->parameters = new \StdClass();

        //$this->parameters->language = 'fr';
        $this->parameters->weekstart = 1;
        $this->parameters->autoclose = 'true';

        $format = \laabs::getDateFormat();
        $format = str_replace('y', 'yy', $format);   // 2 digits year
        $format = str_replace('Y', 'yyyy', $format); // 4 digits year
        
        $format = str_replace('n', 'm', $format);    // Month without leading 0
        $format = str_replace('m', 'mm', $format);   // Month with leading 0
        $format = str_replace('M', 'M', $format);    // Short month name
        $format = str_replace('F', 'MM', $format);   // Full month name
        
        $format = str_replace('d', 'dd', $format);   // Day number with leading 0
        $format = str_replace('J', 'd', $format);    // Day number without leading 0
        $format = str_replace('D', 'D', $format);    // Short day name
        $format = str_replace('l', 'DD', $format);   // Full day name
        $this->parameters->format = $format;
    }
    /*
    {
        format: "yyyy-mm-dd",
        weekStart: 1,
        language: "fr",
        autoclose: true,
        todayHighlight: true,
    };
    */

    public function translate()
    {
        $view = $this->element->ownerDocument;
        $translator = $view->translator;

        $this->parameters->language = $translator->lang;
        if (\laabs::hasPublicResource('public/js/datePicker/locales/bootstrap-datepicker.' . $translator->lang . '.js')) {
            $view->addScript('/public/js/datePicker/locales/bootstrap-datepicker.' . $translator->lang . '.js');
        } 
    }


    public function saveHtml()
    {
        $datePickerId = \laabs\uniqid();
        $this->element->setAttribute('data-datepicker-id', $datePickerId);
        $parameters = json_encode($this->parameters);

        $scriptText =
<<<EOS
$(document).ready(function() {
    $('*[data-datepicker-id="$datePickerId"]').datepicker($parameters);
});
EOS;

        $script = $this->element->ownerDocument->createElement('script');
        $CdataSection = $this->element->ownerDocument->createCDataSection($scriptText);
        $script->appendChild($CdataSection);

        $this->element->parentNode->appendChild($script);

    }
}