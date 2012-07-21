<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Post-Job Application Controller
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


class CATS_onapply extends CATS_Controller
{
    public function init()
    {
        $api = new CATS_API();

        $jobOrderID = $this->_('id', true);

        if (false === ($job = $api->getJob($jobOrderID)))
        {
            throw new CATS_E_BADPARAM();
        }

        if (strcasecmp($this->_('postback'), 'yes'))
        {
            $title = $this->_('title', true);
            $jobsURI = CATS_Utility::uri('cc=index');

            $this->_template->assign('jobsURI', $jobsURI);
            $this->_template->assign('title', $job['title']);
            $this->_template->display('onapply.tpl.php');
        }
        else
        {
            $appLib = new CATS_Application();
            $values = $appLib->getValuesFromPost($this->_session, $jobOrderID);
            $referrer = $this->_session->getReferrer();
            $candidateID = $api->onApply($jobOrderID, $values, $referrer);

            CATS_Utility::redirect('cc=onapply&title=' . urlencode($job['title'])
                . '&id=' . $jobOrderID
            );
        }
    }
}
