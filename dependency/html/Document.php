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
 * Represents an entire HTML document; serves as the root of the document tree.
 *
 * @package Dependency\Html
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
class Document extends \dependency\xml\Document
{
    use IncludeTrait,
        LocalisationTrait;

    /* Constants */
    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    protected $layout;
    protected $classes;
    public $plugins;
    protected $headers;
    protected $layoutData;
    public $XPath;
    public $translator;
    public $dateTimeFormatter;
    public $pluginsParameters = [];
    /**
     *   -- document --
     *   <html>
     *       <head>
     *       <body>
     *   -- body --
     *       -- layout --
     *           <main> or <* role="main">
     */
    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    /*************************************************************************/
    /* DOM Document override
    /*************************************************************************/
    /**
     * Construct a new Html document
     * @param string $layout            The uri to a resource for the default layout to use
     * @param string $layoutData        The uri to a service method to merge with the layout
     * @param array  $extensions        An associative array of Html node type+name and Php classes that will extend it
     * @param array  $plugins           An associative array of Html classes and Php classes that will be automatically plugged-in
     * @param array  $headers           Uris of html ressources that will be imported into the <head> tag
     * @param object $translator        The localisation/translator object to translate the texts of the document
     * @param object $dateTimeFormatter The localisation/dateTimeFormatter object to format the dates of the document
     *
     * @return void
     **/
    public function __construct($layout = null, $layoutData = null, $extensions = null, $plugins = null, $headers = null, \dependency\localisation\TranslatorInterface $translator = null, \dependency\localisation\DateTimeFormatter $dateTimeFormatter = null)
    {
        parent::__construct(null, null, $extensions);
        $this->formatOutput = true;

        // Register node classes for Html
        $this->registerNodeClass('DOMElement', '\dependency\html\Element');
        $this->registerNodeClass('DOMDocument', '\dependency\html\Document');
        $this->registerNodeClass('DOMDocumentFragment', '\dependency\html\DocumentFragment');
        //$this->registerNodeClass('DOMProcessingInstruction'   , '\dependency\html\ProcessingInstruction');

        // Keep plugins and classes (extensions are already managed by Xml\Document)
        $this->plugins = $plugins;
        $this->headers = $headers;
        $this->layout = $layout;
        $this->layoutData = $layoutData;

        $this->translator = $translator;
        $this->dateTimeFormatter = $dateTimeFormatter;

        $this->XPath = new XPath($this);

        if ($this->layoutRequested()) {
            $this->addHtmlStructure();

            $this->useLayout();
        } else {
            $container = $this->createElement('div');
            $this->appendChild($container);
        }

    }

    /**
     * Check if layout and headers requested
     * @return bool
     */
    public function layoutRequested()
    {
        // Verbosely request layout
        if (isset($_REQUEST['HTML_USE_LAYOUT'])) {
            return true;
        }

        // Verbosely refuse layout
        if (isset($_REQUEST['HTML_NO_LAYOUT'])) {
            return false;
        }

        // Ajax call
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
            return false;
        }

        // Not an ajax call
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != "XMLHttpRequest") {
            return true;
        }

        // Ajax call but referer has requested layout so no need to send it again
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'HTML_USE_LAYOUT')) {
            return false;
        }

        // No referer : refresh or first request
        if (!isset($_SERVER['HTTP_REFERER'])) {
            return true;
        }

        if (isset($_SERVER['SCRIPT_URL']) && $_SERVER['SCRIPT_URL'] == '/') {
            return true;
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            if ($_SERVER['HTTP_REFERER'] == $_SERVER['SCRIPT_URI']) {
                //return true;
            }

            //http://<host>/uri?...
            $parts = parse_url($_SERVER['HTTP_REFERER']);

            if ($parts["host"] != $_SERVER['HTTP_HOST']) {
                //return true;
            }
        }

        return false;
    }

    /**
     * Load an html string
     * @param string  $html
     * @param integer $options
     */
    public function loadHtml($html, $options = null)
    {
        parent::loadHtml($html);

        $this->XPath = new XPath($this);
    }

    /**
     * Save the entire Html document or the given node into a Html string; also resolves the plugins to automatically insert necessary data
     * @param mixed $node The html node to save or null to save entire document
     *
     * @return string The Html string
     */
    public function saveHtml($node = null)
    {
        // Deploy js of plugins
        $this->savePlugins($node);

        // All document is to save
        if (!$node) {
            // If html document, save and add HTML 5 declaration
            if ($this->documentElement->tagName == 'html') {
                $html = '<!DOCTYPE html>'.parent::saveHtml();
            } else {
                $html = parent::saveHtml($this->documentElement);
            }
        } else {
            $html = parent::saveHtml($node);
        }

        return $html;
    }

    /*************************************************************************/
    /* DOM Shortcuts
    /*************************************************************************/
    /**
     * Set default document structure
     * @access protected
     */
    protected function addHtmlStructure()
    {
        $html = $this->createElement('html');
        $this->appendChild($html);

        $head = $this->createElement('head');
        $this->documentElement->appendChild($head);

        $body = $this->createElement('body');
        $this->documentElement->appendChild($body);
    }

    /**
     * Insert header contents onto the <head> tag of the document
     * @param array $headers Uris of html head content ressources or null to use default headers declared at consruction
     */
    public function addHeaders(array $headers = null)
    {
        if ($this->documentElement->tagName == 'div') {
            $this->removeChild($this->documentElement);
            $this->addHtmlStructure();
        }

        $head = $this->getHead();

        if ($headers) {
            foreach ($headers as $header) {
                $this->addContentFile($header, $head);
            }
        } else {
            foreach ((array) $this->headers as $header) {
                $this->addContentFile($header, $head);
            }
        }


        // add css
        $this->addStyle("/public/css/bootstrap-toggle/bootstrap-toggle.css");
        $this->addStyle("/public/css/bootstrap-datetimepicker/bootstrap-datetimepicker.css");

        // Add js scripts
        $this->addScript("/public/js/jQuery-3.4.1/jquery-3.4.1.min.js");
        $this->addScript("/public/js/jQueryUI_1.12.1/jquery-ui.min.js");

        // Datatable
        $this->addScript("/public/js/DataTables/datatables.js");
        // $this->addScript("/public/js/DataTables/datatables.min.js"); /: TODO add dependencies for min version

        $this->addScript("/public/js/jQueryUI_touch-punch_1.0.7/jquery.ui.touch-punch.js"); // min version does not exists
        //less compiler
        $this->addScript("/public/js/less_1.7.0/less.js"); //min version

        $this->addScript("/public/js/bootstrap_3.1.1/all.min.js");

        //metisMenu
        $this->addScript("/public/js/metisMenu_1.0.1/metisMenu.js"); // min version does not exists
        //dataForm
        $this->addScript("/public/js/dataForm_0.0.1/dataForm.js"); //min version does not exists

        //gritter
        // Two scripts needed. Second one is custom made for quick framework access
        $this->addScript("/public/js/gritter/gritter.min.js");
        $this->addScript("/public/js/gritter/gritter.js");

        //typeahead
        $this->addScript("/public/js/typeahead_0.11.1/typeahead.js"); // cannot use min due to bloodhound conflict
        // $this->addScript("/public/js/typeahead_0.11.1/typeahead.min.js");

        $this->addScript("/public/js/konami-code/jquery.raptorize.1.0.js");

        $this->addScript("/public/js/bootstrap-tree/bootstrap-tree.js");// home made
        $this->addScript("/public/js/dataList_0.0.1/dataList.js");//min version does not exists
        $this->addScript("/public/js/datePicker/bootstrap-datepicker.js"); //min version does not exists

        // monment
        // $this->addScript("/public/js/moment_2.14.1/moment.js");
        $this->addScript("/public/js/moment_2.14.1/moment.min.js");

        // $this->addScript("/public/js/dateTimePicker/bootstrap-datetimepicker.js");
        $this->addScript("/public/js/dateTimePicker/bootstrap-datetimepicker.min.js");

        $this->addScript("/public/js/csrf/csrfprotector.js");//min version does not exists

        // $this->addScript("/public/js/bootstrap-toggle/bootstrap-toggle.js");
        $this->addScript("/public/js/bootstrap-toggle/bootstrap-toggle.min.js");

        //$this->addScript("/public/js/webodf.js-0.5.8/webodf.js");
    }

    /**
     * Deploy a layout on the empty document
     * @param string $layout     The uri of a resource for the layout html content or null to use the default layout declared at construction
     * @param string $layoutData Some data to merge with layout
     */
    public function useLayout($layout=false, $layoutData = null)
    {
        $this->addHeaders();

        if (!$layout) {
            $layout = $this->layout;
        }
        if (!$layout) {
            return;
        }

        if ($this->documentElement->tagName == 'div') {
            $this->removeChild($this->documentElement);
            $this->addHtmlStructure();
        }

        $body = $this->getBody();
        $layoutFragment = $this->createDocumentFragment();
        $layoutFragment->appendHtmlFile($layout);

        $body->appendChild($layoutFragment);

        if (!$layoutData) {
            if ($this->layoutData) {
                $methodRoute = new \core\Route\MethodRouter($this->layoutData);
                $layoutService = $methodRoute->service->call();
                $layoutData = $methodRoute->method->call($layoutService);
            }
        }

        $layoutData->localisation = \laabs::configuration()['dependency.localisation']->getArrayCopy();

        if ($layoutData) {
            $this->merge($this, $layoutData);
        }
    }

    /**
     * Add html content to the document. If no container is provided as argument, the method will guess where to append the html
     * @param string $content   The html content to add
     * @param object $container An html node to append the content. If ignored, the method will search for the best place to append content to
     *
     * @return object The first node appended to the container
     */
    public function addContent($content, $container = false)
    {
        $contentFragment = $this->createDocumentFragment();
        $contentFragment->appendHtml($content);
        if (!$container) {
            $container = $this->getContainer();
        }

        return $container->appendChild($contentFragment);
    }

    /**
     * Add a resource content to the document. If no container is provided as argument, the method will guess where to append the html
     * @param string $contentResource The uri to a resource filr holding the html content to add
     * @param object $container       An html node to append the content. If ignored, the method will search for the best place to append content to
     *
     * @return object The first node appended to the container
     */
    public function addContentFile($contentResource, $container = false)
    {
        $contentFragment = $this->createDocumentFragment();
        $contentFragment->appendHtmlFile($contentResource);

        if (!$container) {
            $container = $this->getContainer();
        }

        return $container->appendChild($contentFragment);
    }

    /**
     * Get the contents of the view
     * @return DOMNodeList
     */
    public function getContents()
    {
        $container = $this->getContainer();

        if ($container) {
            return $container->childNodes;
        }
    }

    /*************************************************************************/
    /* HTML plugins management
    /*************************************************************************/
    /**
     * Add the plugins to a html tag
     * @param object $node An html node to add plugins to
     */
    public function addPlugins($node = null)
    {
        $elements = $this->XPath->query("descendant-or-self::*[@class]", $node);
        foreach ($elements as $element) {
            $element->addPlugins();
        }
    }

    /**
     * Save the plugins of a html tag
     * @param object $node An html node to save plugins of
     */
    public function savePlugins($node = null)
    {
        $elements = $this->XPath->query("descendant-or-self::*[@class]", $node);
        foreach ($elements as $element) {
            $element->savePlugins();
        }
    }

    /**
     * Get the plugins of a given html class for a given html node
     * @param string $class The class name the plugins are linked with
     * @param object $node  An html node to get plugins
     *
     * @return array The plugin objects
     */
    public function getPlugins($class, $node = null)
    {
        $elements = $this->getElementsByClass($class, $node);
        $plugins = array();
        foreach ($elements as $element) {
            $plugins[] = $element->plugin[$class];
        }

        return $plugins;
    }

    /*-------------------------------------------------------------------------
    - get Methods
    ------------------------------------------------------------------------- */
    /**
     * Get a node with given id attribute
     * @param string $elementId The id attribute
     * @param object $node      The html node for relative search
     *
     * @return The element or false
     */
    public function getElementById($elementId, $node = null)
    {
        if ($nodeList = $this->XPath->query("descendant-or-self::*[@id='$elementId']", $node)) {
            return $nodeList->item(0);
        }
    }

    /**
     * Get a node list of elements having the given name attribute
     * @param string $name The name attribute value
     * @param object $node The html node for relative search
     *
     * @return The node list of elements
     */
    public function getElementsByName($name, $node = null)
    {
        return $this->XPath->query("descendant-or-self::*[@name='$name']", $node);
    }

    /**
     * Get a node list of elements having the given class attribute
     * @param string $class The class attribute value
     * @param object $node  The html node for relative search
     *
     * @return The node list of elements
     */
    public function getElementsByClass($class, $node = null)
    {
        return $this->XPath->query("descendant-or-self::*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]", $node);
    }

    /**
     * Get a node list of elements having the given role attribute
     * @param string $role The role attribute value
     * @param object $node The html node for relative search
     *
     * @return The node list of elements
     */
    public function getElementsByRole($role, $node = null)
    {
        return $this->XPath->query("descendant-or-self::*[@role='$role']", $node);
    }

    /**
     * Retrieve the <head> element of the document
     * @return The head element
     */
    public function getHead()
    {
        $head = $this->getElementsByTagName('head')->item(0);

        return $head;
    }

    /**
     * Retrieve the <body> element of the document
     * @return The body element
     */
    public function getBody()
    {
        return $this->getElementsByTagName('body')->item(0);
    }

    /**
     * Retrieve the main container element of the document
     *
     * @return The main container element
     */
    public function getContainer()
    {
        if ($mainElement = $this->getElementsByTagName("main")->item(0)) {
            return $mainElement;
        }
        if ($mainRole = $this->getElementsByRole("main")->item(0)) {
            return $mainRole;
        }
        if ($containerClass = $this->getElementsByClass("container")->item(0)) {
            return $containerClass;
        }

        if ($body = $this->getBody()) {
            return $body;
        }

        return $this->documentElement;
    }

    /**
     * Get the script element by its src attribute
     * @param string $src The src of script
     *
     * @return \DOMElement
     */
    public function getScript($src)
    {
        $script = $this->XPath->query("//script[@src='$src']")->item(0);

        return $script;
    }

    /**
     * Add all package scripts
     * @param string $package The directory
     *
     * @return array The script elements
     */
    public function addScriptPackage($package)
    {
        $elements = array();

        $webdir = "..".DIRECTORY_SEPARATOR.LAABS_WEB.DIRECTORY_SEPARATOR;
        $srcdir = $webdir.str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $package);
        if (is_dir($srcdir)) {
            foreach (glob($srcdir.DIRECTORY_SEPARATOR."*.js") as $srcfile) {
                $src = str_replace(DIRECTORY_SEPARATOR, LAABS_URI_SEPARATOR, substr($srcfile, strlen($webdir)));
                $elements[] = $this->addScript($src);
            }
        }

        return $elements;
    }

    /**
     * Add a script element if not already included
     * @param string $src The src of script
     *
     * @return \DOMElement The created or already included element
     */
    public function addScript($src)
    {
        if (!($script = $this->getScript($src))) {

            $script = $this->createElement('script');
            $script->setAttribute('src', $src);
            $script->setAttribute('type', 'text/javascript');
            $script->setAttribute('data-auto', '1');

            $this->appendScript($script);
        }

        return $script;
    }

    protected function appendScript($script)
    {
        // Parent is either <head> or container
        if ($head = $this->getHead()) {
            // previous sibling is the last auto-append script OR last meta
            $autos = $head->getElementsByTagName('script');
            $metas = $head->getElementsByTagName('meta');
            if ($autos->length) {
                $lastAuto = $autos->item($autos->length-1);
                $head->insertBefore($script, $lastAuto->nextSibling);
            } elseif ($metas->length) {
                $lastMeta = $metas->item($metas->length-1);
                if ($lastMeta->nextSibling) {
                    $head->insertBefore($script, $lastMeta->nextSibling);
                } else {
                    $head->appendChild($script);
                }
            } else {
                $head->appendChild($script);
            }
        } else {
            $container = $this->getContainer();
            $container->appendChild($script);
        }
    }

    /**
     * Add a script element from source
     * @param string $src The src of script
     *
     * @return \DOMElement The created or already included element
     */
    public function addScriptSrc($src)
    {
        $script = $this->createElement('script');
        $script->setAttribute('type', 'text/javascript');

        $CDATASection = $this->createCDATASection($src);
        $script->appendChild($CDATASection);

        $this->appendScript($script);
    }

    /**
     * Get the style element by its src attribute
     * @param string $href The href of style
     *
     * @return \DOMElement
     */
    public function getStyle($href)
    {
        $style = $this->XPath->query("//style[@href='$href']")->item(0);

        return $style;
    }

    /**
     * Add all package styles
     * @param string $package The directory
     *
     * @return array The style link elements
     */
    public function addStylePackage($package)
    {
        $elements = array();

        $webdir = "..".DIRECTORY_SEPARATOR.LAABS_WEB.DIRECTORY_SEPARATOR;
        $srcdir = $webdir.str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $package);
        if (is_dir($srcdir)) {
            foreach (glob($srcdir.DIRECTORY_SEPARATOR."*.css") as $srcfile) {
                $src = str_replace(DIRECTORY_SEPARATOR, LAABS_URI_SEPARATOR, substr($srcfile, strlen($webdir)));
                $elements[] = $this->addStyle($src);
            }
        }

        return $elements;
    }

    /**
     * Add a script element if not already included
     * @param string $href The href of script
     *
     * @return \DOMElement The created or already included element
     */
    public function addStyle($href)
    {
        if (!($style = $this->getStyle($href))) {

            $style = $this->createElement('link');
            $style->setAttribute('href', $href);
            $style->setAttribute('rel', 'stylesheet');
            $style->setAttribute('data-auto', '1');

            $parent = $this->getHead();

            if (!$parent) {
                $parent = $this->getContainer();
            }

            $autos = $this->XPath->query('./link[@data-auto]', $parent);
            if ($autos->length) {
                $lastAuto = $autos->item($autos->length-1);
                $parent->insertBefore($style, $lastAuto->nextSibling);

                return $style;
            }

            $metas = $this->XPath->query('./meta', $parent);
            if ($metas->length) {
                $lastMeta = $metas->item($metas->length-1);
                if ($lastMeta->nextSibling) {
                    $parent->insertBefore($style, $lastMeta->nextSibling);
                } else {
                    $parent->appendChild($style);
                }
            } else {
                $parent->appendChild($style);
            }
        }

        return $style;
    }
}
