<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Post-Send to Friend Controller
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


class CATS_onsendtofriend extends CATS_Controller
{
    public function init()
    {
        $api = new CATS_API();

        $jobOrderID = $this->_('id', true);

        if (false === ($job = $api->getJob($jobOrderID)))
        {
            throw new CATS_E_BADPARAM();
        }

        $emailto = $this->_('emailto', true);

        if (strcasecmp($this->_('postback'), 'yes'))
        {
            $jobsURI = CATS_Utility::uri('cc=index');

            $this->_template->assign('jobsURI', $jobsURI);
            $this->_template->assign('emailto', $emailto);
            $this->_template->display('onsendtofriend.tpl.php');
        }
        else
        {
            $subject = $this->_('subject', true);
            $message = $this->_('message', false);
            $email = $this->_('email', true);

            $api->onSendToFriend($jobOrderID, $subject, $message, $emailto, $email);

            CATS_Utility::redirect('cc=onsendtofriend&emailto=' . urlencode($emailto));
        }
    }
}
