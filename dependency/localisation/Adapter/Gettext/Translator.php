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

namespace dependency\localisation\Adapter\Gettext;

 /**
 * Language translator for Personalization Objects / Machine Objects (po/mo) adapter
 *
 * @package Localisation
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class Translator implements \dependency\localisation\TranslatorInterface
{
    use \core\ReadonlyTrait;

    /* Properties */
    public $lang;

    public $enclosure;
    public $escape;

    public $catalogs = array();

    public $currentCatalog;

    /**
     * Construct a new translator
     * @param string $lang      The target lang  code
     * @param string $catalog   The catalog Uri to load at stratup
     * @param string $enclosure Set the field enclosure character (one character only). Defaults as a double-quote.
     *
     * @return void
     **/
    public function __construct($lang, $catalog = false, $enclosure = '"')
    {
        $this->setLang($lang);

        $this->enclosure = $enclosure;

        $this->loadCatalog("default");

        if ($catalog) {
            $this->setCatalog($catalog);
        }
    }

    /**
     * Sets the localisation
     * @param string $lang The lang indentifer
     *
     * @return void
     */
    public function setLang($lang)
    {
        $this->lang = strtolower($lang);
    }

    /**
     * Set the source bundle and domain file for localisation
     * @param string $catalog The uri to a po/mo file
     */
    public function setCatalog($catalog)
    {
        $this->loadCatalog($catalog);

        $this->currentCatalog = $this->catalogs[$catalog];
    }

    /**
     * Load a catalog for later use
     * @param string $name The uri to catalog file resource
     *
     * @return void
     */
    public function loadCatalog($name)
    {
        if (isset($this->catalogs[$name])) {
            return;
        }

        $key = "dependency/Localisation/catalogs.".$name;
        $catalog = \laabs::getCache($key);

        if (!$catalog) {
            $catalog = new catalog($name, $this->lang);

            \laabs::setCache($key, $catalog);
        }

        $this->catalogs[$name] = $catalog;
    }

    /**
     * Get a translated text from dictionaries
     * @param string $msgid   The message identifier, the original text
     * @param string $msgctxt The context of message, to search the translation for a specific use
     * @param string $catalog The catalog to use for this translation (does not modify the current catalog)
     *
     * @return string The translated text or the original text if no translation was found
     */
    public function getText($msgid, $msgctxt = false, $catalog = false)
    {
        if ($msgctxt) {
            $qmsgid = (string) $msgctxt."/".(string) $msgid;
        } else {
            $qmsgid = (string) $msgid;
        }

        $qmsgid = preg_replace('/[^[:print:]]/', '', $qmsgid);

        if ($catalog) {
            if (!isset($this->catalogs[$catalog])) {
                $this->loadCatalog($catalog);
            }
            $useCatalog = $this->catalogs[$catalog];
        } elseif ($this->currentCatalog) {
            $useCatalog = $this->currentCatalog;
        } else {
            $useCatalog = $this->catalogs['default'];
        }

        if (isset($useCatalog->messages[$qmsgid])) {
            $msg = $useCatalog->messages[$qmsgid];
            if (!empty($msg->msgstr)) {
                return $msg->msgstr[0];
            }
        }

        return $msgid;
    }

    /**
     * Get a translated format for a plural form or a formatted message
     * @param string $msgid   The message identifier
     * @param array  $args    The variables for format
     * @param string $msgctxt The context of message, to search the translation for a specific use
     * @param string $catalog The catalog to use for this translation (does not modify the current catalog)
     *
     * @return string The translated text with the merged values, or the original text if no translation was found
     */
    public function getFormattedText($msgid, array $args, $msgctxt = false, $catalog = false)
    {
        $qmsgid = md5($msgctxt."/".$msgid);

        if ($catalog) {
            if (!isset($this->catalogs[$catalog])) {
                $this->loadCatalog($catalog);
            }
            $useCatalog = $this->catalogs[$catalog];
        } elseif ($this->currentCatalog) {
            $useCatalog = $this->currentCatalog;
        } else {
            return $msgid;
        }

        if (!isset($useCatalog->messages[$msgid])) {
            return $msgid;
        }

        $msg = $useCatalog->messages[$msgid];

        if (isset($args['count'])) {
            $nplural = $args['count'] - 1;
            if (count($msg->msgstr) >= $nplural) {
                $msgfmt = $msg->msgstr[$nplural];
            } else {
                $msgfmt = end($msg->msgstr);
            }
        } else {
            $msgfmt = $msg->msgstr[0];
        }

        $msgstr = @vsprintf($msgfmt, $args);

        if ($msgstr !== false) {
            return $msgstr;
        }

        return $msgid;
    }
}
