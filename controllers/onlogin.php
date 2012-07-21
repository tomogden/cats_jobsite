<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Candidate Login Controller
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


class CATS_onlogin extends CATS_Controller
{
    public function init()
    {
        $api = new CATS_API();

        if ($this->_('logout', false) == 'yes')
        {
            $this->_session->logout();
        }
        else
        {
            if (!strcasecmp($this->_('postback'), 'yes'))
            {
                $email = $this->_('loginEmail', true);
                $password = $this->_('loginPassword', true);

                if (false === ($job = $api->onLogin($email, $password)))
                {
                    throw new CATS_E_BADLOGIN();
                }

                $this->_session->login(
                    intval($job->item['id']),
                    $email,
                    $password
                );

                if (isset($job->item->resume))
                {
                    $this->_session->setResume(
                        intval($job->item->resume['id']),
                        strtotime($job->item->resume['created']),
                        trim($job->item->resume['md5']),
                        intval($job->item->resume['size']),
                        trim($job->item->resume['name'])
                    );
                }

                foreach ($job->item->parameter as $param)
                {
                    $this->_session->setParameter(strval($param['name']), trim($param));

                    if (!empty($param['key']))
                    {
                        $this->_session->setParameter('field_' . trim($param['key']), trim($param));
                    }
                }

                if (!empty($job->item->applications->job)) foreach ($job->item->applications->job as $job)
                {
                    $this->_session->addApplication(intval($job['id']), strtotime($job['added']));
                }
            }
        }

        CATS_Utility::redirect('cc=index');
    }
}
