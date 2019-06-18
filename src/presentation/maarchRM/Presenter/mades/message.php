<?php 
/*
 * Copyright (C) 2019  Maarch
 *
 * This file is part of Maarch RM
 *
 * Maarch RM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Maarch RM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Maarch RM. If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\mades;
/**
 * 
 * @author  Maarch Cyril Vazquez <cyril.vazquez@maarch.com>
 */
class message
{
    public $view;

    protected $json;
    protected $translator;

     /**
     * Constuctor of message html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The JSON dependency
     * @param \dependency\localisation\TranslatorInterface $translator The localisation dependency
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('seda2/messages');
    }

    /**
     * Reads a message
     * @param array $messages
     * 
     * @return string The html
     */
    public function read($messages)
    {
        //$this->view->addContentFile("seda/message/message.html");
        return '<pre>'.print_r($messages, true).'</pre>';

        $this->view->setSource('messages', $messages);
        $this->view->merge();

        $this->view->translate();

        return $this->view->saveHtml();
    }
}