<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle filePlan.
 *
 * Bundle filePlan is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle filePlan is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle filePlan.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\filePlan\Controller;

/**
 * Control of the file plan
 *
 * @package filePlan
 * @author  Prosper De Laure <delaure.prosper@maarch.org> 
 */
class filePlan {

    protected $sdoFactory;

    /**
     * Constructor
     * @param object $sdoFactory The model for organization
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory) {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get the file plan's list
     *
     * @return array The list of file plans with their position
     */
    public function getTree() {
        $subjects = $this->sdoFactory->find('filePlan/subject');


        // sort by parent
        $roots = [];
        $subjectList = [];

        foreach ($subjects as $subject) {
            $parentSubjectId = (string) $subject->parentSubjectId;

            if ($parentSubjectId == null) {
                $roots[] = $subject;
            } else {
                if (!isset($subjectList[$parentSubjectId])) {
                    $subjectList[$parentSubjectId] = [];
                }
                $subjectList[$parentSubjectId][] = $subject;
            }
        }
        
        return $this->buildTree($roots, $subjectList);
    }

    /**
     * Build the file plan tree
     *
     */
    protected function buildTree($roots, $subjectList)
    {
        foreach ($roots as $subject) {
            $subjectId = (string) $subject->subjectId;

            if (isset($subjectList[$subjectId])) {
                $subject->subject = $this->buildTree($subjectList[$subjectId], $subjectList);
            }
        }

        return $roots;
    }

}
