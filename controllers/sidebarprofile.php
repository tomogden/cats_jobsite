<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Sidebar Controller
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


class CATS_sidebarprofile extends CATS_Controller
{
    public function init()
    {
        if ($this->_session->isLoggedIn())
        {
            $this->_showProfile();
        }
        else
        {
            $this->_showLoginForm();
        }
    }

    private function _showProfile()
    {
        $logoutURI = CATS_Utility::uri('cc=onlogin&logout=yes');

        $name = $this->_session->getParameter('first_name') . ' ' . $this->_session->getParameter('last_name');
        $address = $this->_session->getParameter('address');
        $city = $this->_session->getParameter('city');
        $state = $this->_session->getParameter('state');
        $postCode = $this->_session->getParameter('post_code');
        $email = $this->_session->getEmail();
        $registered = date('F j, Y', strtotime($this->_session->getParameter('registered')));

        $api = new CATS_API();
        $applications = $this->_session->getApplications();
        if (!empty($applications)) foreach ($applications as $id => $when)
        {
            $title = $api->getJobTitle($id);

            if (empty($title))
            {
                unset($applications[$id]);
                continue;
            }

            $applications[$id] = array(
                'link' => CATS_Utility::uri('cc=show&id=' . $id),
                'title' => $title,
                'when' => date('M j, Y', $when)
            );
        }

        $resume = $this->_session->getResume();
        $resumeURI = CATS_Utility::uri('cc=resume');

        $profileURI = CATS_Utility::uri('cc=editprofile');

        $this->_template->assign('resume', $resume);
        $this->_template->assign('resumeURI', $resumeURI);
        $this->_template->assign('profileURI', $profileURI);
        $this->_template->assign('applications', $applications);
        $this->_template->assign('registered', $registered);
        $this->_template->assign('email', $email);
        $this->_template->assign('name', $name);
        $this->_template->assign('address', $address);
        $this->_template->assign('city', $city);
        $this->_template->assign('state', $state);
        $this->_template->assign('postCode', $postCode);
        $this->_template->assign('logoutURI', $logoutURI);
        $this->_template->display('sidebarprofile.tpl.php');
    }

    private function _showLoginForm()
    {
        /* If the user is signed in */
        $email = $this->_session->getEmail();

        /* Or, "Key" column value for candidate -> email stored from completing a job app */
        if (empty($email))
        {
            $email = $this->_session->getParameter('field_wqgtpk');
        }

        $forgotURI= CATS_Utility::uri('cc=forgot&logout=no');
        $registerURI= CATS_Utility::uri('cc=register&logout=no');
        $formURI = CATS_Utility::uri('cc=onlogin&logout=no');

        $this->_template->assign('email', $email);
        $this->_template->assign('formURI', $formURI);
        $this->_template->assign('registerURI', $registerURI);
        $this->_template->assign('forgotURI', $forgotURI);
        $this->_template->display('sidebarlogin.tpl.php');
    }
}

