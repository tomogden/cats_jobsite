<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Post Register
 *
 * Processes and submits completed job applications back to CATS via
 * the API.
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


class CATS_onregister extends CATS_Controller
{
    public function init()
    {
        if (strcasecmp($this->_('postback'), 'yes'))
        {
            $this->_template->display('onregister.tpl.php');
        }
        else
        {
            $api = new CATS_API();

            $email = $this->_('email', true);
            $password1 = $this->_('password1', true);
            $password2 = $this->_('password2', true);

            if (strcmp($password1, $password2))
            {
                throw new CATS_E_BADPARAM('Passwords mismatched. Make sure the password in '
                    . 'the second field matches that in the first.'
                );
            }

            $appLib = new CATS_Application();
            $values = $appLib->getValuesFromPost($this->_session);

            $referrer = $this->_session->getReferrer();

            $candidateID = $api->onRegister($email, $password1, $values, $referrer);

            CATS_Utility::redirect('cc=onregister');
        }
    }
}
