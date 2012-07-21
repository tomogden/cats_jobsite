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


class CATS_onforgot extends CATS_Controller
{
    public function init()
    {
        $api = new CATS_API();

        $email = $this->_('loginEmail', true);

        try
        {
            $api->onForgotPassword($email);
        }
        catch (CATS_E_API $e)
        {
            $this->_template->assign('none', false);
        }

        $registerURI = CATS_Utility::uri('cc=register');

        $this->_template->assign('registerURI', $registerURI);
        $this->_template->assign('email', $email);
        $this->_template->display('onforgot.tpl.php');
    }
}

