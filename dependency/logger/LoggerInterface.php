<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of dependency logger.
 *
 * Dependency logger is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency logger is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency logger.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\logger;

/**
 * Logger interface
 *
 * @package dependency\logger
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface LoggerInterface
{
    /**
     * Log a message
     * @param string $message   The message to log
     * @param string $level     The log level
     * @param string $eventType The event type
     */
    public function log($message, $level, $eventType);
}
