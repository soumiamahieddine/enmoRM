<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\app;

/**
 * User story admin authorization
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface authInterface
{
    /**
     * Prepare a user object for update
     *
     * @uses auth/userAccount/readProfile
     *
     * @return auth/user/editProfile
     */
    public function readMyprofile();

    /**
     * Prepare a user object for update
     *
     * @uses auth/userAccount/updateMyProfile
     *
     * @return auth/user/updateUserInformation
     */
    public function updateMyprofile($userAccount);

    /**
     * Change a user password
     * @param string $newPassword The new password
     * @param string $oldPassword The old password
     *
     * @uses auth/userAccount/updatePassword_userAccountId_
     * @return auth/user/setPassword
     */
    public function updateUseraccountSetpassword_userAccountId_($newPassword, $oldPassword);
}
