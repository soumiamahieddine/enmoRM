<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\recordsManagement;

/**
 * welcomePage serializer html
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class welcome
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    
    public $view;

    /**
     * Constuctor of welcomePage html serializer
     * @param \dependency\html\Document $view The view
     */
    public function __construct(\dependency\html\Document $view)
    {
        $this->view = $view;
    }
    
    /**
     * get a welcome page
     *
     * @return string
     */
    public function welcomePage()
    {
        //$this->view->addHeaders();
        //$this->view->useLayout();
        $this->view->addContentFile("dashboard/welcomePage.html");

        $this->view->translate();
        
        $accountToken = \laabs::getToken('AUTH');
        $user = \laabs::newController('auth/userAccount')->get($accountToken->accountId);

        $this->view->setSource('user', $user);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Display error
     *
     * @return string
     */
    public function error($error)
    {
        //$this->view->addHeaders();
        //$this->view->useLayout();
        $this->view->addContentFile("dashboard/error.html");

        $this->view->translate();

        $this->view->setSource('error', $error);
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
