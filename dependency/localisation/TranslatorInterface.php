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
namespace dependency\localisation;
/**
 * Language translator interface
 * 
 * A translation is made from the original language string provided to the target locale, using the following parameters :
 * source  : The directory of the catalogs/dictionaries, represented by the name of the bundle
 * locale  : The localisation code (locale, language code)
 * domain  : The catalog/dictionary to get translations from
 * msgid   : The identifier for the message. It can be a code or the original text
 * msgctxt : The context for translation. It will switch between several translated forms of the original msgid. Optional.
 * 
 * @package Localisation
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */ 
interface TranslatorInterface
{
    /* Constants */

    /* Methods */
    /**
     * Set the target language for translations
     * @param string $lang The language indentifer
     */
    public function setLang($lang);

    /**
     * Set the source bundle and domain file for localisation
     * @param string $catalog The uri to a source catalog file (bundle/catalog)
     */
    public function setCatalog($catalog);

    /**
     * Get a translated text from dictionaries
     * @param string $msgid   The message identifier, the original text
     * @param string $msgctxt The context of message, to search the translation for a specific use
     * @param string $catalog The catalog to use for this translation (does not modify the current catalog)
     * 
     * @return string The translated text or the original text if no translation was found
     */
    public function getText($msgid, $msgctxt=false, $catalog=false);

    /**
     * Get a translated format for a plural form or a formatted message
     * @param string $msgid   The message identifier
     * @param array  $args    The variables for format
     * @param string $msgctxt The context of message, to search the translation for a specific use
     * @param string $catalog The catalog to use for this translation (does not modify the current catalog)
     * 
     * @return string The translated text with the merged values, or the original text if no translation was found
     */
    public function getFormattedText($msgid, array $args, $msgctxt=false, $catalog=false);

}

