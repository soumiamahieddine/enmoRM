<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of Laabs.
 *
 * Laabs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Laabs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Laabs.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 * [ini]
 * register_argc_argv = On
 */
// Require base constants and functions
require_once('../core/laabs.php');

laabs::init();

// instantiate Kernel
\core\Kernel\ServiceKernel::start($requestMode = 'cli', $requestType = 'text');

// Run
\core\Kernel\ServiceKernel::run();

// End
\core\Kernel\ServiceKernel::end();
