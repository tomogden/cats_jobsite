<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Controller Abstract Class
 *
 * Object loaded by the CATS_JobSite plugin dynamically to represent an
 * entry point into the plugin through internal navigation.
 * The controller is abstract and is extended by a class relative
 * to the action being performed and named accordingly.
 *
 * It's sole purpose is to answer user requests, delegate work to
 * libraries, validate user input, polish the resultsets and finally
 * load a template for output.
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


require_once('./libs/Exceptions.php');
require_once('./libs/Utility.php');
require_once('./libs/Session.php');
require_once('./libs/Template.php');
require_once('./libs/API.php');

abstract class CATS_Controller
{
    protected $_template;       // Necessary to generate output
    protected $_content;        // The wordpress page contents
    protected $_session;        // Stores our session to maintain state

    /**
     * Constructs the controller and assigns protected variables to be
     * used by extenders of the abstract class.
     *
     * @param   object      Wordpress post object
     * @param   string      Wordpress page contents
     * @return  void
     */
    public function __construct($content = '')
    {
        $this->_content = $content;
        $this->_template = new CATS_Template();
        $this->_template->assign('content', $content);

        /**
         * Load the user's session if available. We manage our own sessions
         * instead of using PHP sessions for a couple of reasons:
         *
         * 1) Avoid conflicts/low timeouts that may in place by other PHP
         *    applications or by the CMS itself.
         * 2) Only load sessions when viewing plug-in rendered pages,
         *    speeding up other non-plugin pages.
         * 3) Sandboxes all plug-in content in the ./cache directory for
         *    easy clearing during development.
         */
        $this->_session = CATS_Utility::getWrapper()->getSessionObject();
    }

    public function load()
    {
        $this->init();
    }

    public function footer()
    {
        $imageURL = CATS_Utility::getImageURL('poweredByCATS.gif');

        $this->_template->assign('poweredBy', $imageURL);
        $this->_template->display('footer.tpl.php');
    }

    /**
     * Shortcut to collect form post data and optionally validate
     * and scalar-ize it.
     *
     * @param   string      Form element name
     * @param   boolean     Required? If yes and not present, fire an exception
     * @param   boolean     If yes, arrays will be scalar-ized
     * @return  mixed       Data
     */
    protected function _($id, $required = false, $scalar = true)
    {
        return CATS_Utility::getPost($id, $required, $scalar);
    }

    /**
     * Requires that the controller viewer be a privileged administrator or
     * we should fire an exception.
     *
     * @return  void
     */
    protected function setPrivileged()
    {
        if (!CATS_Utility::isCMSAdmin())
        {
            throw new CATS_E_BADLOGIN();
        }
    }
}

