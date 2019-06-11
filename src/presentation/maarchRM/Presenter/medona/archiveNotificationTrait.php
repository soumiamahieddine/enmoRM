<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\medona;

/**
 * trait html archiveNotification
 *
 * @package medona
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.com>
 */
trait archiveNotificationTrait
{
     /**
     * Show notification history screen
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function notificationHistory($messages)
    {
        $this->view->addContentFile('medona/archiveNotification/notificationHistory.html');
        $this->prepareMesageList($messages, true);
        $this->initHistoryForm();

        $statuses = [
            'sent' => $this->translator->getText('sent'),
            'error' => $this->translator->getText('error')
        ];

        $this->view->setSource('statuses', $statuses);
        $this->view->merge();

        return $this->view->saveHtml();
    }

}
