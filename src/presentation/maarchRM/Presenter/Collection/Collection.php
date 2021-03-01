<?php

/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle Collection.
 *
 * Bundle Collection is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Collection is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Collection.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\Collection;

/**
 * Serializer html for collection
 *
 * @package Collection
 * @author  Jérôme Boucher <jerome.boucher@maarch.com>
 */
class Collection
{
    public $view;

    protected $json;

    protected $translator;

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The json base object
     * @param \dependency\localisation\TranslatorInterface $translator The translator object
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {

        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('Collection/Collection');
    }

    public function index($collection)
    {
        var_dump($collection);
        exit;
    }
}
