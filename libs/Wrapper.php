<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Wrapper Class
 *
 * Includes several entry points into the framework. If you're incorporating
 * a CMS or website with the framework, this class should be extended and
 * its methods overridden with the appropriate hooks pointing to your
 * website or CMS's entry points. This class is designed to make the connection
 * between the outer tool and the framework.
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

/* Include the framework */
include_once('./libs/Controller.php');

abstract class CATS_Wrapper
{
    protected $_session;

    public function __construct()
    {
        $this->_session = CATS_Utility::getSessionObject();
    }

    public function getSessionObject()
    {
        return $this->_session;
    }

    /**
     * Returns a URL that points to a file relative to the plugin path.
     *
     * @param   string      Name of the file
     */
    public function getRelativeURL($name)
    {
        return sprintf('images/%s', $name);
    }

    /**
     * Converts a URI to incorporate any saved parameters used by the external
     * website or CMS (if any).
     *
     * @param   string      URI
     * @return  string
     */
    public function getURI($uri)
    {
        return '?' . $uri;
    }

    /**
     * Is the current user considered an administrator?
     *
     * @return  boolean
     */
    public function isAdmin()
    {
        return false;
    }

    /**
     * Retrieves the value for a preconfigured option.
     *
     * @param   string      Option name (usually prefixed with "cats_cms_")
     * @return  mixed
     */
    public function getOption($name)
    {
        $name = 'CATS_CMS_' . strtoupper($name);

        @include_once('./config.php');

        if (defined($name))
        {
            return constant($name);
        }

        return false;
    }

    /**
     * Loads a controller based on the cc URL parameter.
     *
     * @param   string      Existing content
     * @return  string      Page contents
     */
    public function getContent($content = '')
    {
        $param = CATS_Utility::getPost('cc');

        if (false === ($content = CATS_Utility::getControllerOutput($param)))
        {
            $content = CATS_Utility::getControllerOutput();
        }

        return $content;
    }

    /**
     * Redirects the user to a new relative URI.
     *
     * @param   string      URI
     */
    public function redirect($uri)
    {
        $this->headerRedirect($uri);
    }

    /**
     * Redirects using the HTTP header method or the javascript method
     * depending on whether headers have been sent or not.
     *
     * @param   string      URI to redirect to
     */
    public function headerRedirect($uri)
    {
        if (headers_sent())
        {
            $this->javaScriptRedirect($uri);
        }
        else
        {
            header("Location: $uri");
            exit(0);
        }
    }

    /**
     * Prints a "you should be transferred" page and script to refresh
     * in 2 seconds. Useful if headers have already been sent.
     *
     * @param   string      URI to redirect to
     */
    public function javaScriptRedirect($uri)
    {
        $template = new CATS_Template();
        $template->assign('uri', CATS_Utility::uri($uri));
        $template->display('redirect.tpl.php');
    }

    /**
     * Should be overloaded to provide localization and internationalization (i8n)
     * for Gettext or whatever libraries the parent is using already. Defaults to
     * returning the same value.
     *
     * @param   string      String to translate
     * @return  string      Translated string
     */
    public function getLocalizedString($text)
    {
        return $text;
    }

    /**
     * Outputs additional includes to the <head> element of the page. For example,
     * we include a reference to the jobs RSS feed so that it's integrated with 
     * modern browers automatically.
     */
    public function printHeadIncludes()
    {
        $url = CATS_Utility::getRSSFeedURL();

        if (!empty($url))
        {
            printf('<link rel="alternate" type="application/rss+xml" '
                . 'title="Jobs (RSS 2.0)" href="%s" />' . "\n",
                $url
            );
        }
    }
}
