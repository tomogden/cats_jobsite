<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Job Show Controller
 *
 * Displays details about a job.
 *
 * This file is part of CATS JobSite.
 *
 * Copyright (C) 2009 - 2010 CATS Software, Inc.
 *
 * CATS JobSite is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CATS JobSite is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CATS JobSite in a file named "COPYING" in the root directory.
 * If not, see <http://www.gnu.org/licenses/>.
 */


class CATS_oneditprofile extends CATS_Controller
{
    public function init()
    {
        if (!$this->_session->isLoggedIn())
        {
            throw new CATS_E_BADLOGIN('You are not logged in.');
        }

        $email = $this->_('email', true);
        $password1 = $this->_('password1');
        $password2 = $this->_('password2', !empty($password1));
        $title = $this->_('title');
        $firstName = $this->_('first_name');
        $middleName = $this->_('middle_name');
        $lastName = $this->_('last_name');
        $address = $this->_('address');
        $city = $this->_('city');
        $state = $this->_('state');
        $postCode = $this->_('post_code');
        $phoneHome = $this->_('phone_home');
        $phoneWork = $this->_('phone_work');
        $phoneCell = $this->_('phone_cell');
        $website = $this->_('website');
        $file = CATS_Utility::getFileUpload('file');

        $api = new CATS_API();

        if (!empty($password1) && strcasecmp($password1, $password2))
        {
            throw new CATS_E_BADPARAM('Passwords do not match.');
        }

        $xml = $api->onUpdateProfile(
            $this->_session->getCandidateID(),
            $email,
            $password1,
            $title,
            $firstName,
            $middleName,
            $lastName,
            $address,
            $city,
            $state,
            $postCode,
            $phoneHome,
            $phoneWork,
            $phoneCell,
            $website,
            $file
        );

        $this->_session->login(
            $this->_session->getCandidateID(),
            $email,
            $this->_session->getPassword()
        );

        if (isset($xml->item->resume))
        {
            $this->_session->setResume(
                intval($xml->item->resume['id']),
                strtotime($xml->item->resume['created']),
                trim($xml->item->resume['md5']),
                intval($xml->item->resume['size']),
                trim($xml->item->resume['name'])
            );
        }

        foreach ($xml->item->parameter as $param)
        {
            $this->_session->setParameter(strval($param['name']), trim($param));
        }

        CATS_Utility::redirect('cc=profile');
    }
}
