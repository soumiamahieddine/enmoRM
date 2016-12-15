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
namespace dependency\html\plugins\dateRangePicker;
/**
 * Class for datePicker plugin
 */
class dateRangePicker
    extends \dependency\html\AbstractHtmlClass
{

    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    /*
    {
        "locale": {
            "format": "MM/DD/YYYY",
            "separator": " - ",
            "applyLabel": "Apply",
            "cancelLabel": "Cancel",
            "fromLabel": "From",
            "toLabel": "To",
            "customRangeLabel": "Custom",
            "weekLabel": "W",
            "daysOfWeek": [
                "Su",
                "Mo",
                "Tu",
                "We",
                "Th",
                "Fr",
                "Sa"
            ],
            "monthNames": [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            ],
            "firstDay": 1
        },
        "startDate": "08/18/2016",
        "endDate": "08/24/2016"
    }
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
        $this->parameters->locale = new \StdClass();

        $this->parameters->locale->firstDay = 1;

        $format = \laabs::getDateFormat();
        $format = str_replace('y', 'YY', $format);   // 2 digits year
        $format = str_replace('Y', 'YYYY', $format); // 4 digits year
        
        $format = str_replace('n', 'M', $format);    // Month without leading 0
        $format = str_replace('m', 'MM', $format);   // Month with leading 0
        $format = str_replace('M', 'M', $format);    // Short month name
        $format = str_replace('F', 'MM', $format);   // Full month name
        
        $format = str_replace('d', 'DD', $format);   // Day number with leading 0
        $format = str_replace('J', 'D', $format);    // Day number without leading 0
        $format = str_replace('D', 'D', $format);    // Short day name
        $format = str_replace('l', 'DD', $format);   // Full day name
        $this->parameters->locale->format = $format;
        
        $this->element->ownerDocument->addScript("/public/dependency/html/js/bootstrap-daterangepicker/moment.min.js");
        $this->element->ownerDocument->addScript('/public/dependency/html/js/bootstrap-daterangepicker/daterangepicker.js');
        $this->element->ownerDocument->addStyle('/public/dependency/html/css/bootstrap-daterangepicker/daterangepicker.css');
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

        $this->parameters->locale->applyLabel = $translator->getText('Apply');
        $this->parameters->locale->cancelLabel = $translator->getText('Cancel');
        $this->parameters->locale->fromLabel = $translator->getText('From');
        $this->parameters->locale->toLabel = $translator->getText('To');
        $this->parameters->locale->customRangeLabel = $translator->getText('Custom');
        $this->parameters->locale->weekLabel = $translator->getText('Week');
        $this->parameters->locale->daysOfWeek = [
            $translator->getText('Su', 'dayOfWeek'),
            $translator->getText('Mo', 'dayOfWeek'),
            $translator->getText('Tu', 'dayOfWeek'),
            $translator->getText('We', 'dayOfWeek'),
            $translator->getText('Th', 'dayOfWeek'),
            $translator->getText('Fr', 'dayOfWeek'),
            $translator->getText('Sa', 'dayOfWeek'),
        ];
        $this->parameters->locale->monthNames = [
            $translator->getText("January"),
            $translator->getText("February"),
            $translator->getText("March"),
            $translator->getText("April"),
            $translator->getText("May"),
            $translator->getText("June"),
            $translator->getText("July"),
            $translator->getText("August"),
            $translator->getText("September"),
            $translator->getText("October"),
            $translator->getText("November"),
            $translator->getText("December")
        ];
    }


    public function saveHtml()
    {
        $dateRangePickerId = \laabs\uniqid();
        $this->element->setAttribute('data-daterangepicker-id', $dateRangePickerId);
        $parameters = json_encode($this->parameters);

        $scriptText =
<<<EOS
$(document).ready(function() {
    $('*[data-daterangepicker-id="$dateRangePickerId"]').daterangepicker($parameters);
});
EOS;
        $script = $this->element->ownerDocument->createElement('script');
        $CdataSection = $this->element->ownerDocument->createCDataSection($scriptText);
        $script->appendChild($CdataSection);

        $this->element->parentNode->appendChild($script);
    }
}