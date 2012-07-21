<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Forgot password form
 *
 * Generates the sidebar which is a Wordpress Widget.
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


class CATS_forgot extends CATS_Controller
{
    public function init()
    {
        if ($this->_session->isLoggedIn())
        {
            throw new CATS_E_BADPARAM('You are already logged in.');
        }

        /* If the user is signed in */
        $email = $this->_session->getEmail();

        /* Or, "Key" column value for candidate -> email stored from completing a job app */
        if (empty($email))
        {
            $email = $this->_session->getParameter('field_wqgtpk');
        }

        $formURI = CATS_Utility::uri('cc=onforgot&logout=no');
        $registerURI = CATS_Utility::uri('cc=register&logout=no');

        $this->_template->assign('email', $email);
        $this->_template->assign('formURI', $formURI);
        $this->_template->assign('registerURI', $registerURI);
        $this->_template->display('forgot.tpl.php');
    }
}

