<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * View candidate's profile.
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


class CATS_profile extends CATS_Controller
{
    public function init()
    {
        if (!$this->_session->isLoggedIn())
        {
            throw new CATS_E_BADLOGIN('You are not logged in.');
        }

        $firstName = $this->_session->getParameter('first_name');
        $lastName = $this->_session->getParameter('last_name');
        $middleName = $this->_session->getParameter('middle_name');
        $address = $this->_session->getParameter('address');
        $city = $this->_session->getParameter('city');
        $state = $this->_session->getParameter('state');
        $postCode = $this->_session->getParameter('post_code');
        $phoneHome = $this->_session->getParameter('phone_home');
        $phoneWork = $this->_session->getParameter('phone_work');
        $phoneCell = $this->_session->getParameter('phone_cell');
        $title = $this->_session->getParameter('title');
        $website = $this->_session->getParameter('website');
        $email = $this->_session->getEmail();
        $registered = date('F j, Y', strtotime($this->_session->getParameter('registered')));
        $resume = $this->_session->getResume();
        $resumeURI = CATS_Utility::uri('cc=resume');
        $editProfileURI = CATS_Utility::uri('cc=editprofile');

        $applications = $this->_session->getApplications();
        $api = new CATS_API();
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

        $this->_template->assign('applications', $applications);
        $this->_template->assign('resume', $resume);
        $this->_template->assign('resumeURI', $resumeURI);
        $this->_template->assign('editProfileURI', $editProfileURI);
        $this->_template->assign('registered', $registered);
        $this->_template->assign('website', $website);
        $this->_template->assign('phoneHome', $phoneHome);
        $this->_template->assign('phoneCell', $phoneCell);
        $this->_template->assign('phoneWork', $phoneWork);
        $this->_template->assign('title', $title);
        $this->_template->assign('email', $email);
        $this->_template->assign('firstName', $firstName);
        $this->_template->assign('middleName', $middleName);
        $this->_template->assign('lastName', $lastName);
        $this->_template->assign('address', $address);
        $this->_template->assign('city', $city);
        $this->_template->assign('state', $state);
        $this->_template->assign('postCode', $postCode);

        $this->_template->display('profile.tpl.php');
    }
}
