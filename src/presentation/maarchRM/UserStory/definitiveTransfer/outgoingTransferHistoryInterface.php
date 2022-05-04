<?php
/*
 * Copyright (C) 2015 Maarch
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
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\definitiveTransfer;

/**
 * User story for transfer sending
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface outgoingTransferHistoryInterface
{
    /**
     * Search form
     * @param string $reference
     * @param string $archiver
     * @param string $originator
     * @param string $depositor
     * @param string $archivalAgreement
     * @param date   $fromDate
     * @param date   $toDate
     * @param string $status
     *
     * @uses medona/archiveTransfer/readOutgoingtransferHistory
     * @return medona/message/outgoingTransferHistory
     */
    public function readOutgoingtransferHistory($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null, $status = null);
}