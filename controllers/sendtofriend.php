<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Send Job to Friend
 *
 * Handles the display of the job application and generation of the
 * XHTML form.
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


include_once('./controllers/index.php');

class CATS_sendtofriend extends CATS_Controller
{
    public function init()
    {
        $api = new CATS_API();

        $id = $this->_('id', true);

        if (false === ($job = $api->getJob($id)))
        {
            throw new CATS_E_BADPARAM();
        }

        /* If the user is signed in */
        $email = $this->_session->getEmail();

        /* Or, "Key" column value for candidate -> email stored from completing a job app */
        if (empty($email))
        {
            $email = $this->_session->getParameter('field_wqgtpk');
        }

        $formURI = CATS_Utility::uri('cc=onsendtofriend&id=' . $id);
        $jobURI = CATS_Utility::uri('cc=show&id=' . $id);

        $this->_template->assign('id', $id);
        $this->_template->assign('title', $job['title']);
        $this->_template->assign('email', $email);
        $this->_template->assign('formURI', $formURI);
        $this->_template->assign('jobURI', $jobURI);
        $this->_template->display('sendtofriend.tpl.php');
    }
}
