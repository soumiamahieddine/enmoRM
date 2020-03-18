<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of medona.
 *
 * medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona. If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\delivery;

/**
 * User story - restitution request
 *  @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface deliveryRequestInterface
{
    /**
     * Deliver an archive
     *
     * @param mixed  $archiveIds The archive identifier or a list of identifier
     * @param string $comment    A comment
     * @param string $identifier The reference for message
     * @param string $format     The message format
     *
     * @uses medona/archiveDelivery/createDelivery
     * @return medona/archiveModification/deliver
     */
    public function createArchivedelivery($archiveIds, $comment, $identifier = null, $format = null);
}
