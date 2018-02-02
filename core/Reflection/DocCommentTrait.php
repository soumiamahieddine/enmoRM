<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of LAABS.
 *
 * LAABS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * LAABS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with LAABS. If not, see <http://www.gnu.org/licenses/>.
 */
namespace core\Reflection;
/**
 * Reflection on doc block
 * 
 * @package LAABS
 * @author  Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
trait DocCommentTrait
{
    /**
     * The summary
     * @var string
     */
    public $summary;

    /**
     * The description
     * @var string
     */
    public $description;

    /**
     * The tags
     * @var array
     */
    public $tags;

    /**
     * Parse the doc comment
     */
    public function parseDocComment()
    {
        $docComment = $this->getDocComment();
        $this->tags = null;
        $docComment = preg_split('# *\n\s*\*(\/| *)?#m', substr($docComment, 3));

        $summaryLines = [];
        while (($line = next($docComment)) !== false && (!empty($line) && $line[0] != '@')) {
            $summaryLines[] = $line;
        }

        $this->summary = implode (' ', $summaryLines);

        $descriptionLines = [];
        while (($line = next($docComment)) !== false && (!isset($line[0]) || $line[0] != '@')) {
            $descriptionLines[] = $line;
        }

        $this->description = implode(" ", $descriptionLines);

        $docComment = implode("\n", $docComment);
        preg_match_all('#@(?<name>\w+)(|\s+(?<value>.+))$#m', $docComment, $tagMatches, PREG_SET_ORDER);
        foreach ($tagMatches as $tagMatch) {
            $tagname = $tagMatch['name'];
            if (isset($tagMatch['value'])) {
                $this->tags[$tagname][] = $tagMatch['value'];
            } else {
                $this->tags[$tagname][] = true;
            }
        }

        return;

        $docLines = explode("\n", $docComment);
        foreach ($docLines as $docLine) {
            /*$tag = strtok($docLine, "@");
            if ($tag != $docLine) {
                $name = strtok(' ');
                $value = strtok('');
                if (empty($value)) {
                    $value = true;
                }
                $this->tags[$name][] = $value;
            }
            continue;*/
            if (preg_match('#@(?<name>\w+)\s+(?<value>.*)#', $docLine, $tagMatch)) {
                $tagname = $tagMatch['name'];
                $tagvalue = $tagMatch['value'];
                $this->tags[$tagname][] = $tagvalue;
            } elseif (preg_match('#@(?<name>\w+)#', $docLine, $tagMatch)) {
                $tagname = $tagMatch['name'];
                $this->tags[$tagname][] = true;
            }
        }
    }
}
