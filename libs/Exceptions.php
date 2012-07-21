<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Exceptions Library
 *
 * Abstract class representing all internal plug-in exceptions, which
 * are automatically caught to generate friendly error pages.
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


abstract class CATS_Exception extends Exception
{
    protected $_template;
    protected $_isAdmin;

    public function __construct($extra = 'No additional information available.')
    {
        parent::__construct();
        $this->_template = new CATS_Template();
        $this->_template->extra = $extra;
        $this->_isAdmin = CATS_Utility::isCMSAdmin();
    }

    public function display()
    {
        $this->_template->assign('friendlyError', 'A problem has occurred');
        $this->_template->assign('friendlyDescription', '');

        if (method_exists($this, 'fire')) $this->fire();

        $this->_template->assign('line', $this->getLine());
        $this->_template->assign('file', $this->getFile());
        $this->_template->assign('message', $this->getMessage());
        $this->_template->assign('code', $this->getCode());
        $this->_template->assign('traceString', $this->getTraceAsString());
        $this->_template->assign('trace', $this->getTrace());
        $this->_template->assign('isAdmin', $this->_isAdmin);

        $this->_template->display('error.tpl.php');
    }
}

class CATS_E_API extends CATS_Exception
{
    public function fire()
    {
        if (!$this->_isAdmin)
        {
            $this->_template->assign('friendlyTitle', 'Error Processing your Request');
            $this->_template->assign('friendlyDescription',
                'Some errors occurred when we tried processing your request. The problem '
                . 'is on our end -- your request was valid. Many times, problems like this '
                . 'are due to internet connectivity and clear up quickly. Please use the '
                . '<strong>back</strong> button on your browser and try again in a few '
                . 'minutes.'
            );
        }
        else
        {
            $this->_template->assign('friendlyTitle', 'CATS API 2.0 Server Error');
            $this->_template->assign('friendlyDescription',
                'Either the CATS_JobSite plug-in was unable to connect to the CATS server, or '
                . 'it returned an error/something unexpected.'
            );
        }
    }
}

class CATS_E_FILEACCESS extends CATS_Exception
{
    public function fire()
    {
        if (!$this->_isAdmin)
        {
            $this->_template->assign('friendlyTitle', 'Error Processing your Request');
            $this->_template->assign('friendlyDescription',
                'Some errors occurred when we tried processing your request. The problem '
                . 'is on our end -- your request was valid. Many times, problems like this '
                . 'are due to internet connectivity and clear up quickly. Please use the '
                . '<strong>back</strong> button on your browser and try again in a few '
                . 'minutes.'
            );
        }
        else
        {
            $this->_template->assign('friendlyTitle', 'Permissions Error');
            $this->_template->assign('friendlyDescription',
                'The plug-in attempted to write to a file within the plug-in\'s directory and '
                . 'was denied. Please make sure that the web server user has write access to '
                . 'all files within wp-content/plugins/CATS_JobSite.'
            );
        }
    }
}

class CATS_E_MISSINGPARAM extends CATS_Exception
{
    public function fire()
    {
        $this->_template->assign('friendlyTitle', 'Missing Field');
        $this->_template->assign('friendlyDescription',
            'One of the fields on the previous page was required and left empty. '
            . 'Please click on the <strong>back</strong> button on your browser '
            . 'and complete all required fields and try again.'
        );
    }
}

class CATS_E_BADPARAM extends CATS_Exception
{
    public function fire()
    {
        $this->_template->assign('friendlyTitle', 'No Longer Exists');
        $this->_template->assign('friendlyDescription',
            'The item that you are referencing has been removed or renamed and '
            . 'no longer exists. Please use the <strong>back</strong> button on '
            . 'your browser or navigate to the most recent listings area and select '
            . 'a new item.'
        );
    }
}

class CATS_E_NOTEMPLATE extends CATS_Exception
{
    public function fire()
    {
        $this->_template->assign('friendlyTitle', 'No Template');
        $this->_template->assign('friendlyDescription',
            'Cannot complete your request as no template exists to handle it in '
            . 'the ./templates sub-directory of the CATS_JobSite plug-in.'
        );
    }
}

class CATS_E_BADLOGIN extends CATS_Exception
{
    public function fire()
    {
        $this->_template->assign('friendlyTitle', 'Invalid Email/Password');
        $this->_template->assign('friendlyDescription', sprintf(
            'The email/password combination you entered was invalid. If you haven\'t registered with '
            . 'us before, <a href="%s">click here</a>. If you have registered but can\'t remember your '
            . 'password, <a href="%s">click here</a> to retrieve it.<br /><br />'
            . 'Applying to jobs does not require registration. To view a list of available jobs, '
            . '<a href="%s">click here</a>.',
            CATS_Utility::uri('cc=register'),
            CATS_Utility::uri('cc=forgotpassword'),
            CATS_Utility::uri('cc=index')
        ));
    }
}

