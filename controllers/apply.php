<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Job Apply Controller
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


class CATS_apply extends CATS_Controller
{
    public function init()
    {
        $api = new CATS_API();

        $id = $this->_('id', true);

        if (false === ($job = $api->getJob($id)))
        {
            throw new CATS_E_BADPARAM();
        }

        $appLib = new CATS_Application();
        $application = $appLib->getAutoCompletedApplication($this->_session, $id);

        //$this->_session->printParams();

        $postURL = CATS_Utility::uri('cc=onApply&id=' . $id);

        $this->_template->assign('job', $job);
        $this->_template->assign('application', $application);
        $this->_template->assign('postURL', $postURL);
        $this->_template->display('apply.tpl.php');
    }
}
