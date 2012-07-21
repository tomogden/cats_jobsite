<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Controller for logged in candidates to view their resume.
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


class CATS_resume extends CATS_Controller
{
    public function init()
    {
        if (!$this->_session->isLoggedIn())
        {
            throw new CATS_E_BADLOGIN('You are not logged in.');
        }

        $resume = $this->_session->getResume();
        if (empty($resume))
        {
            throw new CATS_E_BADPARAM('You currently have no resume.');
        }

        $api = new CATS_API();
        $content = $api->getResumeXHTML($this->_session->getCandidateID());

        $formURI = CATS_Utility::uri('cc=onresume');

        $this->_template->assign('content', $content);
        $this->_template->assign('formURI', $formURI);
        $this->_template->assign('resume', $resume);
        $this->_template->display('resume.tpl.php');
    }
}
