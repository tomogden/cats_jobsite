<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Job Listings Controller
 *
 * Displays a listing of open jobs.
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


class CATS_index extends CATS_Controller
{
    private $_api;

    public function init()
    {
        $this->_api = new CATS_API();

        /* Searching */
        $params = $this->_getSearchParams();

        /* Pagination */
        if (false !== ($tmp = $this->_getPageParams($params)))
        {
            $params .= $tmp;
        }

        /* Get the jobs for the current page after filtering */
        $params .= sprintf('&limit=%d&excerpt=yes', CATS_Utility::getOption('jobsperpage'));

        /* Sorting */
        if (false !== ($tmp = $this->_getSortParams()))
        {
            if (!empty($params)) $params .= '&';
            $params .= $tmp;
        }

        $jobs = $this->_api->getJobs($params);
        $columns = $this->_api->getColumnsForDisplay();

        foreach ($jobs as $index => $job)
        {
            foreach ($columns as $column)
            {
                $value = htmlspecialchars($job[$column['id']]);

                switch ($column['id'])
                {
                    case 'title':
                        $value = sprintf('<a href="%s">%s</a>',
                            CATS_Utility::uri('cc=show&id=' . $job['job_order_id']),
                            $value
                        );
                        break;
                }

                $jobs[$index][$column['id']] = $value;
            }
        }

        if ($this->_session->isLoggedIn())
        {
            $name = ($firstName = $this->_session->getParameter('first_name'))
                . ' ' . $this->_session->getParameter('last_name');
            $this->_template->assign('name', $name);
            $this->_template->assign('firstName', $firstName);
            $this->_template->assign('isLoggedIn', true);
        }

        $loginURI = CATS_Utility::uri('cc=login');
        $logoutURI = CATS_Utility::uri('cc=onlogin&logout=yes');
        $profileURI = CATS_Utility::uri('cc=profile');
        $resumeURI = CATS_Utility::uri('cc=resume');
        $registerURI = CATS_Utility::uri('cc=register');
        $searchURI = CATS_Utility::uri('cc=index');
        $rssURL = CATS_Utility::getRSSFeedURL();
        $rssIcon = CATS_Utility::getImageURL('feed_plus.png');

        $this->_template->assign('rssURL', $rssURL);
        $this->_template->assign('rssIcon', $rssIcon);
        $this->_template->assign('loginURI', $loginURI);
        $this->_template->assign('profileURI', $profileURI);
        $this->_template->assign('resumeURI', $resumeURI);
        $this->_template->assign('logoutURI', $logoutURI);
        $this->_template->assign('registerURI', $registerURI);
        $this->_template->assign('jobs', $jobs);
        $this->_template->assign('searchURI', $searchURI);
        $this->_template->assign('columns', $columns);
        $this->_template->display('index.tpl.php');
    }

    private function _getSearchParams()
    {
        $params = '';

        if (false !== ($search = $this->_('ccmsSearch')))
        {
            /* User removed search */
            if (empty($search))
            {
                $this->_session->setParameter('search', false);
            }
            else
            {
                /* Save in session so that it doesn't clear */
                $this->_session->setParameter('search', $search);
                $params = sprintf('search=%s', urlencode($search));
            }
        }
        /* Previously saved */
        else if (false !== ($search = $this->_session->getParameter('search')))
        {
            $params = sprintf('search=%s', urlencode($search));
        }

        $this->_template->assign('search', $search);

        return $params;
    }

    private function _getPageParams($params)
    {
        /* How many pages of jobs are there? */
        $numJobs = $this->_api->getNumJobs($params);
        $numPages = ceil($numJobs / CATS_Utility::getOption('jobsperpage'));

        /* What page are we currently viewing? */
        if (false !== ($page = $this->_('ccmsPage')))
        {
            if (($page = intval($page)) > $numPages)
            {
                $page = $numPages;
            }
            else if ($page < 1)
            {
                $page = 1;
            }
        }
        else
        {
            $page = 1;
        }

        $this->_template->assign('page', $page);
        $this->_template->assign('numPages', $numPages);
        $this->_template->assign('numJobs', $numJobs);

        if ($page > 1)
        {
            return sprintf('%soffset=%d',
                !empty($params) ? '&' : '',
                ($page - 1) * CATS_Utility::getOption('jobsperpage')
            );
        }
        else
        {
            return false;
        }
    }

    private function _getSortParams()
    {
        if (false !== ($sort = $this->_('ccmsSort')) && preg_match('/^(.*)\_(asc|desc)$/', $sort, $matches))
        {
            $sortColumn = $matches[1];
            $sortDir = $matches[2];
            $params = sprintf('sort=%s&sort_dir=%s', $sortColumn, $sortDir);

            /* Save in the session for future page loads */
            $this->_session->setParameter('sortColumn', $sortColumn);
            $this->_session->setParameter('sortDir', $sortDir);
        }
        /* Previous stored in session? */
        else if (false !== ($sortColumn = $this->_session->getParameter('sortColumn'))
            && false !== ($sortDir = $this->_session->getParameter('sortDir')))
        {
            $params = sprintf('&sort=%s&sort_dir=%s', $sortColumn, $sortDir);
        }
        else
        {
            $this->_api->getSort($sortColumn, $sortDir);
            $params = false;
        }

        $this->_template->assign('sortColumn', $sortColumn);
        $this->_template->assign('sortDir', $sortDir);

        return $params;
    }
}
