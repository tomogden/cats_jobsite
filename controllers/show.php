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


class CATS_show extends CATS_Controller
{
    public function init()
    {
        $api = new CATS_API();

        $id = $this->_('id', true);

        if (false === ($job = $api->getJob($id)))
        {
            throw new CATS_E_BADPARAM();
        }

        $jobsURI = CATS_Utility::uri('cc=index');
        $linkApply = CATS_Utility::uri('cc=apply&id=' . $id);
        $linkSendToFriend = CATS_Utility::uri('cc=sendtofriend&id=' . $id);

        /* Save in the "Recently Viewed" section */
        $this->_session->addRecentJob($id);

        $this->_template->assign('job', $job);
        $this->_template->assign('jobsURI', $jobsURI);
        $this->_template->assign('linkApply', $linkApply);
        $this->_template->assign('linkSendToFriend', $linkSendToFriend);
        $this->_template->display('show.tpl.php');
    }
}
