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
namespace dependency\html;
/**
 * Trait to implement the localisation (translation and format) capacities
 *
 * @package Dependency\Html
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 **/ 
trait LocalisationTrait
{

    /* Properties */
    public $translatableNodes;

    protected $translatedNodes;

    public $dateNodes;

    public $formattedDateNodes;

    /* Methods */
    /* -------------------------------------------------------------------------
    - Localisation
    ------------------------------------------------------------------------- */
    /**
     * Localise the texts
     * @param object $node The context node or null to localise the entire document
     */
    public function localise($node=null)
    {
        if (!$this->documentElement) {
            return;
        }
        
        $this->translate($node);
        $this->formatDatetime($node);
        
    }

    /**
     * Translate the texts found as following a <translate> processing-instruction
     * @param object $node    The context node or null to translate the entire document
     * @param string $catalog The catalog uri for translation
     */
    public function translate($node=null, $catalog=false)
    {
        if (!$node) {
            $this->translate($this->documentElement);
            foreach ($this->fragments as $id => $fragment) {
                $this->translate($fragment);
            }
        }
  
        if ($catalog) {
            $this->translator->setCatalog($catalog);
        }

        if (!$this->translatedNodes) {
            $this->translatedNodes = new \SplObjectStorage();
        }

        $this->getTranslatableNodes($node);
        
        if (isset($this->translatableNodes)) {
            foreach ($this->translatableNodes as $nodepath => $info) {
                if ($nodepath[0] == "?") {
                    $translatableNode = $info['node'];
                } else {
                    $translatableNode = $this->XPath->query($nodepath)->item(0);
                }

                if (!$translatableNode) {
                    continue;
                }

                if ($this->translatedNodes->contains($translatableNode)) {
                    continue;
                }

                $struct = $this->parseText($info['msgid']);

                $msgstr = $this->translator->getText($struct['graph'], $info['context'], $info['catalog']);

                // Replace message vars
                if (count($info['vars'])) {
                    $msgstr = vsprintf($msgstr, $info['vars']);
                }

                $translatableNode->nodeValue = $struct['prefix']  . $msgstr . $struct['suffix'];

                $this->translatedNodes->attach($translatableNode);

            }
        }
        
        $this->translatePlugins($node, $catalog); 

        $langAttrs = $this->XPath->query('//*[@lang]/@lang');
        foreach ($langAttrs as $langAttr) {
            $langAttr->value = $this->translator->lang;
        }

    }

    /**
     * Get translatable nodes, text nodes and some attributes only
     * @param object $node      The current node to get translatable nodes of
     * @param bool   $translate Position of the html "translate" indicator attribute, to disable translation
     * @param string $lang      The html lang code for the current node (original language)
     * @param string $catalog   The uri to the catalog to use (bundle/domain)
     * @param string $context   The current context for messages, used in conjunction with node value as the fully qualified message id
     *
     * @return void
     * @author 
     */
    public function getTranslatableNodes($node=null, $translate=true, $lang=false, $catalog=false, $context=false)
    {
        if (!$node) {
            /*foreach ($this->childNodes as $childNode) {
                $this->getTranslatableNodes($childNode);
            }*/

            //return;
            $node = $this;
        }
        
        switch($node->nodeType) {
            case \XML_ELEMENT_NODE:
                if ($node->hasAttribute('lang')) {
                    $lang = $node->getAttribute('lang');
                    //var_dump($node->getNodePath() . " => lang set to $lang");
                }

                if ($node->hasAttribute('translate')) {
                    if ($node->getAttribute('translate') == "yes") {
                        $translate = true;
                    } 
                    if ($node->getAttribute('translate') == "no") {
                        $translate = false;
                    }
                }

                if ($node->hasAttribute('data-translate-catalog')) {
                    $catalog = $node->getAttribute('data-translate-catalog');
                    //var_dump($node->getNodePath() . " => catalog set to $catalog");
                } 

                if ($node->hasAttribute('data-translate-context')) {
                    $context = $node->getAttribute('data-translate-context');
                    //var_dump($node->getNodePath() . " => context set to $context");
                }

                // Translate attributes if requested
                if ($translate) {
                    foreach ($node->attributes as $attribute) {
                        if (in_array($attribute->name, array("alt", "title", "placeholder")) && trim($attribute->value) != "") {
                             //&& !$this->translatableNodes->contains($attribute) 
                            $this->registerTranslatableNode($attribute, $lang, $catalog, $context);
                        } 
                    }
                }

            case \XML_ELEMENT_NODE:
            case \XML_DOCUMENT_FRAG_NODE:
            case \XML_DOCUMENT_NODE:
                foreach ($node->childNodes as $childNode) {
                    switch($childNode->nodeType) {
                        case \XML_ELEMENT_NODE:
                            $this->getTranslatableNodes($childNode, $translate, $lang, $catalog, $context);
                            break;

                        case \XML_TEXT_NODE:
                            if (trim($childNode->nodeValue, " \t\n\r\0\x0B\xC2\xA0") != ""
                                && $translate
                                //&& $lang
                                //&& $catalog 
                                //&& !$this->translatableNodes->contains($childNode)
                            ) {
                                $this->registerTranslatableNode($childNode, $lang, $catalog, $context);
                            }
                            break;
                    }
                }
                break;
        }

    }

    protected function registerTranslatableNode($node, $lang, $catalog, $context)
    {
        //var_dump($node->getNodePath() . " listed with $lang, $catalog $context");
        // Find and keep merge instructions
        $msgid = $node->nodeValue;
        $msgvars = array();
        if (preg_match_all('#\[\?merge (?:(?!\?\]).)*\?\]#', $node->nodeValue, $merges)) {
            foreach ($merges[0] as $merge) {
                $msgid = str_replace($merge, "%s", $msgid);
            }
            $msgvars = $merges[0];
        }
        //$this->translatableNodes->attach($node, array('lang' => $lang, 'catalog' => $catalog, 'context' => $context, 'msgid' => $node->nodeValue, 'vars' => $msgvars, 'nodepath' => $node->getNodePath()));
        $nodepath = $node->getNodePath();
        $this->translatableNodes[$nodepath] = array('lang' => $lang, 'catalog' => $catalog, 'context' => $context, 'msgid' => $node->nodeValue, 'vars' => $msgvars, 'node' => $node);
    }

    /**
     * Retrieve the texts available for translation
     * @param object $node The context node or null to search the entire document
     * 
     * @return array The original texts to translate
     */
    public function getTranslatableTexts($node=null)
    {
        $translatableTexts = array();

        $this->getTranslatableNodes($node);

        $this->translatableNodes->rewind();
        while ($this->translatableNodes->valid()) {
            $index  = $this->translatableNodes->key();
            $translatableNode = $this->translatableNodes->current();
            $info = $this->translatableNodes->getInfo();

            $translatableTexts[] = $info;

            $this->translatableNodes->next();
        }

        return $translatableTexts;
    }


    /**
     * Translate the plugins
     * @param object $node An html node to save plugins of
     */
    public function translatePlugins($node=null)
    {
        $elements = $this->XPath->query("descendant-or-self::*[@class]", $node);
        foreach ($elements as $element) {
            $element->translatePlugins();
        }
    }

    /**
     * Format the dates
     * @param object $node The context node or null to check entire document
     */
    public function formatDatetime($node=null)
    {
        
        $this->dateNodes = new \SplObjectStorage();

        if (!$this->formattedDateNodes) {
            $this->formattedDateNodes = new \SplObjectStorage();
        }

        $this->getFormattableDateNodes($node);

        $this->dateNodes->rewind();
        while ($this->dateNodes->valid()) {
            $index  = $this->dateNodes->key();
            $dateNode = $this->dateNodes->current();
            $info = $this->dateNodes->getInfo();

            if ($this->formattedDateNodes->contains($dateNode)) {
                continue;
            }

            $struct = $this->parseText($info['datetime']);
            $formattedDate = $this->dateTimeFormatter->format($struct['graph'], $info['inputFormat']);

            $dateNode->nodeValue = $struct['prefix']  . $formattedDate . $struct['suffix'];
            
            $this->formattedDateNodes->attach($dateNode);

            $this->dateNodes->next();
        }

    }

    /**
     * Get translatable nodes, text nodes and some attributes only
     * @param object $node The current node to get translatable nodes of
     *
     * @return void
     * @author 
     */
    public function getFormattableDateNodes($node=null)
    {

        if (!$node) {
            $node = $this->documentElement;
        }

        switch($node->nodeType) {
            case \XML_ELEMENT_NODE:
                if ($node->hasAttribute('data-datetime-format')) {
                    foreach ($node->childNodes as $childNode) {
                        switch($childNode->nodeType) {
                            case \XML_ELEMENT_NODE:
                                $this->getFormattableDateNodes($childNode);
                                break;

                            case \XML_ATTRIBUTE_NODE:
                                if (in_array($childNode->name, array("alt", "title", "placeholder"))
                                    && $node->hasAttribute('data-datetime-format') 
                                ) {
                                    break;
                                }
                                break;

                            case \XML_TEXT_NODE:
                                if (trim($node->nodeValue) != ""
                                    && !$this->dateNodes->contains($node)
                                ) {
                                    $format = $node->parentNode->getAttribute('data-datetime-format');
                                    $this->dateNodes->attach($node, array('datetime' => $datetime, 'format' => $format));
                                }
                        }
                    }
                } else {
                    foreach ($node->childNodes as $childNode) {
                        switch($childNode->nodeType) {
                            case \XML_ELEMENT_NODE:
                                $this->getFormattableDateNodes($childNode);
                                break;

                            case \XML_ATTRIBUTE_NODE:
                                if (in_array($childNode->name, array("alt", "title", "placeholder"))
                                    && $node->hasAttribute('data-datetime-format') 
                                ) {
                                    break;
                                }
                                break;

                            case \XML_TEXT_NODE:
                                if (trim($node->nodeValue) != ""
                                    && !$this->dateNodes->contains($node)
                                ) {
                                    $format = $node->parentNode->getAttribute('data-datetime-format');
                                    $this->dateNodes->attach($node, array('datetime' => $datetime, 'format' => $format));
                                }
                        }
                    }
                }
            case \XML_DOCUMENT_FRAG_NODE:
                foreach ($node->childNodes as $childNode) {
                    switch($childNode->nodeType) {
                        case \XML_ELEMENT_NODE:
                            if ($node->hasAttribute('data-datetime-format')) {
                                $this->getFormattableDateNodes($childNode);
                            }
                            break;

                        case \XML_ATTRIBUTE_NODE:
                            if (in_array($childNode->name, array("alt", "title", "placeholder"))
                                && $node->hasAttribute('data-datetime-format') 
                            ) {
                                break;
                            }
                            break;

                        case \XML_TEXT_NODE:
                            if (trim($node->nodeValue) != ""
                                && !$this->dateNodes->contains($node)
                            ) {
                                $format = $node->parentNode->getAttribute('data-datetime-format');
                                $this->dateNodes->attach($node, array('datetime' => $datetime, 'format' => $format));
                            }
                    }
                }
                break;

            

        }
        if ($node->hasAttribute('data-datetime-format') 
                    && trim($node->nodeValue) != ""
                    && !$this->dateNodes->contains($node)
        ) {
            $format = $node->getAttribute('data-datetime-format');
            $this->dateNodes->attach($node, array('datetime' => $datetime, 'format' => $format));
        } 

        switch($node->nodeType) {
            case \XML_ELEMENT_NODE:

                if ($node->hasAttribute('data-datetime-inputformat')) {
                    $inputFormat = $node->getAttribute('data-datetime-inputformat');
                } 

                if ($node->hasAttribute('data-datetime-format')) {
                    $format = true;
                } 

                foreach ($node->attributes as $attribute) {
                    $this->getFormattableDateNodes($attribute, $inputFormat, $format);
                }
                foreach ($node->childNodes as $childNode) {
                    $this->getFormattableDateNodes($childNode, $inputFormat, $format);
                }
                break;

            case \XML_ATTRIBUTE_NODE:
                if (!in_array($node->name, array("alt", "title", "placeholder"))) {
                    break;
                }
                // If one of the attributes to translate, continue to add it

            case \XML_TEXT_NODE:
                if (trim($node->nodeValue) != ""
                    && !$this->dateNodes->contains($node)
                    && $format
                ) {
                    $datetime = $node->nodeValue;
                    $this->dateNodes->attach($node, array('datetime' => $datetime, 'inputFormat' => $inputFormat));
                }
                break;
        }

    }


    protected function parseText($text)
    {
        if (preg_match('#^(?<prefix>[\s\t\r\n]*)?(?<graph>.*[[:graph:]])(?<suffix>[\s\t\r\n]*)?$#', $text, $format)) {
            $graph = preg_replace('# +#', " ", $format['graph']);
            $prefix = isset($format['prefix']) ? $format['prefix'] : "";
            $suffix = isset($format['suffix']) ? $format['suffix'] : "";
        } else {
            $graph = $text;
            $prefix = $suffix = "";
        }

        return array("prefix" => $prefix, "graph" => $graph, "suffix" => $suffix);

    }


}