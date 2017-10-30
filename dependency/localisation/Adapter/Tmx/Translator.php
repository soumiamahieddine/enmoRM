<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency localisation.
 *
 * Dependency localisation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency localisation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency localisation.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\localisation\Adapter\Tmx;
/**
 * undocumented class
 *
 * @package Localisation
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */ 
class Translator
    implements \dependency\localisation\TranslatorInterface
{
    use \core\ReadonlyTrait;

    /* Properties */
    public $lang;
    public $source;
    public $domain;

    protected $domainXPath;

    public $codeset;

    public $sourceLanguage;
    public $pluralFormsCount;
    public $pluralFormsDefault;

    public $bindedDomains = array();

    /* Methods */
    /**
     * Construct a new translator
     * @param string $lang The lang target code
     *
     * @return void
     **/
    public function __construct($lang)
    {
        $this->setLang($lang);
    }

        /**
     * Set the locale for localisation
     * @param string $lang The lang indentifer
     */
    public function setLang($lang) 
    {
        $this->lang = strtolower($lang);
    }

    /**
     * Set the source bundle for localisation
     * @param string $bundle The bundle name to get dictionaries
     */
    public function setSource($bundle) 
    {
        /* Search for resources on extensions */
        $sourcedir = LAABS_BUNDLE . DIRECTORY_SEPARATOR .
            $bundle . DIRECTORY_SEPARATOR .
            LAABS_RESOURCE . DIRECTORY_SEPARATOR .
            'locale';

        $domainfiles = \core\Reflection\Extensions::extendedContents($sourcedir, $unique = true, $filesonly = true);
        foreach ($domainfiles as $domainfile) {
            if (substr($domainfile, -4) != ".tmx") {
                continue;
            }
            $domain = basename($domainfile, ".tmx");
            $domaindoc = \laabs::Dependency("Xml")->Document();
            $domaindoc->load($domainfile);
            $this->bindedDomains[$domain] = $domaindoc;
        }
        $this->source = $source;
    }

    /**
     * Set the domain for localisation
     * @param string $domain The domain name
     */
    public function setDomain($domain)
    {
        $this->domainXPath = \laabs::Dependency("Xml")->XPath($this->bindedDomains[$domain]);

        $this->sourceLanguage = strtolower($this->domainXPath->query("/tmx/header/@srclang")->item(0)->value);
        $this->codeset = $this->domainXPath->query("//header/@o-encoding")->item(0)->value;
        $this->pluralFormsCount = $this->domainXPath->query("//prop[@name='plural-forms-count']")->item(0)->nodeValue;
        $this->pluralFormsDefault = $this->domainXPath->query("//prop[@name='plural-forms-default']")->item(0)->nodeValue;

        $this->domain = $domain;
    }

    /**
     * Get a translated text from dictionaries
     * @param string $msgid   The message identifier, the original text
     * @param string $msgctxt The context of message, to search the translation for a specific use
     * 
     * @return string The translated text or the original text if no translation was found
     */
    public function getText($msgid, $msgctxt=false)
    {
        if (!$this->domainXPath) {
            return $msgid;
        }

        if (strpos($msgid, "'") !== false) {
            $msgidProtected = '"' . $msgid . '"';  
        } else {
            $msgidProtected = "'" . $msgid . "'";
        }

        $query = "/tmx/body/tu[";
        if ($msgctxt) { 
            $query .= "context[@context-type='x-laabs' and text()='$msgctxt'] and ";
        }
        $query .= "tuv[(@xml:lang='".strtolower($this->sourceLanguage)."' or @xml:lang='".strtoupper($this->sourceLanguage)."') and seg/text()=$msgidProtected]";
        $query .= "]/tuv[@xml:lang='".strtolower($this->locale)."' or @xml:lang='".strtoupper($this->locale)."']/seg";

        if (!$seg = $this->domainXPath->query($query)->item(0)) {
            return $msgid;
        }

        return $seg->nodeValue;
    }

    /**
     * Get a translated format for a plural form
     * @param string $msgid   The message identifier
     * @param string $nmsgid  The plural form message identifier, the original text used
     * @param int    $count   The number to merge on translation
     * @param string $msgctxt The context of message, to search the translation for a specific use
     * 
     * @return string The translated text with the merged count value, or the original text if no translation was found
     */
    public function getPluralText($msgid, $nmsgid, $count, $msgctxt=false) 
    {
        $query = "/tmx/body/tu/tuv/seg[" .
                    "ancestor::tuv[" .
                        "@xml:lang='".$this->locale."'" .
                        " and ancestor::tu[" .
                            "child::tuv[".
                                "@xml:lang='".$this->sourceLanguage."'".
                                " and child::seg='".$msgid."'".
                            "]".
                            " and child::tuv[".
                                "@xml:lang='".$this->sourceLanguage."'".
                                " and child::prop[@name='tuv-plural']".
                                " and child::seg='".$nmsgid."'".
                            "]";
        if ($msgctxt) {
            $query .=
                            " and child::context[" .
                                "@context-type='x-laabs'" .
                                " and text()='".$msgctxt."'".
                            "]";
        }
        $query .=       "]".
                    "]".
                "]";

        if (!$this->domainXPath) {
            return $nmsgid;
        }

        if ((!$segs = $this->domainXPath->query($query)) || $segs->length == 0) {
            return $nmsgid;
        }

        $pluralIndex = $count-1;
        if ($pluralIndex > $segs->length) {
            $pluralIndex = $this->pluralFormsDefault;
        }

        if ($pluralIndex > $segs->length) {
            return $nmsgid;
        }

        return sprintf($segs->item($pluralIndex)->nodeValue, $count);
    }

}