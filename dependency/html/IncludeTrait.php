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
 * Trait for html inclusion instructions
 */
trait IncludeTrait
{
    /*************************************************************************/
    /* INCLUDE processing instructions                                       */
    /*************************************************************************/
    /**
     * Process hinclude instructions
     * @param DOMNode $node The context node
     */
    public function hinclude($node=null)
    {
        if ($pis = $this->XPath->query("descendant-or-self::processing-instruction('hinclude')", $node)) {
            foreach ($pis as $pi) {
                $this->includeHtml($pi);
            }
        }
    }
    
    protected function includeHtml($pi)
    {
        $includeFragment = $this->createDocumentFragment();
        $includeFragment->appendHtmlFile(trim($pi->data));
        if (!$includeFragment) {
            throw new \Exception("Error including Html fragment: fragment '$pi->data' could not be parsed");
        }

        return $pi->parentNode->replaceChild($includeFragment, $pi);
    }
}