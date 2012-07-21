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


class CATS_sidebartopjobs extends CATS_Controller
{
    public function init()
    {
        if (0 > ($numJobs = CATS_Utility::getOption('topjobs'))) return;

        $api = new CATS_API();

        $jobs = $api->getJobs(
            sprintf('limit=%d&excerpt=yes&columns=title,description,job_order_id,posted',
                $numJobs
            )
        );

        foreach ($jobs as $index => $job)
        {
            $jobs[$index]['link'] = CATS_Utility::uri('cc=show&id=' . $job['job_order_id']);
        }

        $jobsURI = CATS_Utility::uri('');

        $this->_template->assign('jobs', $jobs);
        $this->_template->assign('jobsURI', $jobsURI);
        $this->_template->display('sidebartopjobs.tpl.php');
    }
}

