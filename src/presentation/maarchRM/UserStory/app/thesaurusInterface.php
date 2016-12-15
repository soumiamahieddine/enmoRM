<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle archivesPubliques.
 *
 * Bundle archivesPubliques is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle archivesPubliques is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle archivesPubliques.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\app;
/**
 * Interface pour le thesaurus
 * 
 * @package App
 * @author  Prosper DE LAURE Maarch <prosper.delaure@maarch.org>
 * @access public
 */ 
interface thesaurusInterface
{
    /**
     * Rechercher un concept
     * 
     * @uses archivesPubliques/thesaurus/read_query_
     */
    public function readThesaurus_query_();
}