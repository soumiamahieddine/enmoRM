<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle user.
 *
 * Bundle user is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle user is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle user.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\exceptions;

/**
 * exception serializer
 *
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
trait exceptionTrait
{
    /**
     * exception
     * @param string $exception exception
     *
     * @return void
     */
    public function exception($exception)
    {
        $exceptionPresenter = \laabs::newPresenter('Exception', $this->view, $this->json, $this->view->translator);

        return $exceptionPresenter->Exception($exception);
    }

}
