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
namespace dependency\localisation\gettext;

class gettext
{
    public function __construct($locale) {
        setlocale(LC_ALL, $locale);
        putenv("LC_ALL=" . $locale);
    }

    /**************************************************************************
    ** GNU GETTEXT UTILITIES
    ** See http://www.gnu.org/software/gettext/manual/gettext.html
    **************************************************************************/
    /*************************************************************************
    ** 5 Making the PO Template File
    *************************************************************************/
    // xgettext : Extracts translatable strings from given input files.
    /*************************************************************************
    ** 6 Creating a New PO File
    *************************************************************************/
    // msginit : Creates a new PO file, initializing the meta information with values from the user's environment.
    /*************************************************************************
    ** 7 Updating Existing PO Files
    *************************************************************************/
    // msgmerge : Merges two Uniforum style .po files together.
    /*************************************************************************
    ** 8 Editing PO Files
    *************************************************************************/
    /*************************************************************************
    ** 9 Manipulating PO Files
    *************************************************************************/
    // msgcat : Concatenates and merges the specified PO files.
    // msgconv : Converts a translation catalog to a different character encoding.
    // msggrep : Extracts all messages of a translation catalog that match a given pattern or belong to some given source files.
    //
    /**
     *  Make localisation for a given context
     *
     */
    public static function publish($part, $name, $locale, $domain='default') 
    {
        $locale_dir = $part . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . LAABS_LOCALE_DIR;
        $messages_dir = $locale_dir . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'LC_MESSAGES';
        $po = $messages_dir . DIRECTORY_SEPARATOR . $domain . '.po';
        self::msgfmt($po);
    }
    /**
     * Set current locale / domains for translations
     *
     */
    /*
        msgid (msgctxt) => {$msgctxt}\004{$msgid};
        App / Bundle    => Localisation directory : in $_SERVER
        locale          => Locale directory : LC_ALL
        domain          => Filename
        context         => msgctxt
        text            => msgid
        setlocale(LC_ALL, <locale>); => once at startup
        putenv("LC_ALL=".<locale>);  => once at startup
        bindtextdomain(<domain_name>, <domain_dir>); => select before translate, done for every extension
    */

    /**
     * Read a po file and store a mo file.
     * If no MO filename is given, one will be generated from the PO filename.
     * @param string $po Filename of the input PO file.
     * @param string $mo Filename of the output MO file.
     * 
     * @return void
     */
    public static function msgfmt($po, $mo = null)
    {
        $stringset = po::fromFile($po);
        if ($mo === null) {
            $mo = substr($po, 0, -3) . '.mo';
        }
        mo::toFile($stringset, $mo);
    }

    /**
     * Reads a mo file and stores the po file.
     * If no PO file was given, only displays what would be the result.
     * @param string $mo Filename of the input MO file.
     * @param string $po Filename of the output PO file.
     * 
     * @return void
     */
    public static function msgunfmt($mo, $po = null)
    {
        $stringset = mo::fromFile($mo);
        if ($po === null) {
            print po::toString($stringset);
        } else {
            po::toFile($stringset, $po);
        }
    }
}
