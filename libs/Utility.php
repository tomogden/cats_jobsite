<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Static Utility Class
 *
 * This utility class provides numerous functions for generic
 * actions not directly related to a library object. They are
 * called as static methods and do not require object
 * instantiation.
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


class CATS_Utility
{
    private function __construct() {}
    private function __clone() {}

    /**
     * Returns TRUE if the Wordpress administrators has setup the CATS JobSite plugin.
     *
     * @return  boolean
     */
    public static function hasSetup()
    {
        $code = CATS_Utility::getOption('trans_code');
        $domain = CATS_Utility::getOption('domain');
        $companyID = CATS_Utility::getOption('company_id');

        return (!empty($code) && !empty($domain) && !empty($companyID));
    }

    /**
     * Removes the "Powered By" text from certain controllers, like sidebars,
     * where it doesn't make sense.
     *
     */
    private static function hasFooter($cc)
    {
        return !in_array($cc, array(
            'sidebar',
            'sidebarprofile',
            'sidebartopjobs',
            'sidebarrecentjobs',
            'globalfooter',
            'options'
        ));
    }

    /**
     * Loads a Controller object and outputs the display to stdout.
     *
     * @param   string      Controller name
     * @return  void
     */
    public static function loadController($cc = false, $content = '', $noFooter = false)
    {
        /* Save current execution path */
        $cwd = getcwd();

        /* Change path to the plugin's sandbox, this is where we work */
        chdir($dir = dirname(__FILE__) . '/..');

        if (false === ($obj = self::getController($cc, $content)))
        {
            chdir($cwd);
            return false;
        }

        if (!self::hasSetup() && $cc != 'options')
        {
            chdir($cwd);
            return false;
        }

        /* Capture custom exceptions to provide friendly error messages */
        try
        {
            /* Call the controller, gather output */
            $obj->load();
            if (self::hasFooter($cc) && !$noFooter) $obj->footer();
        }
        catch (CATS_Exception $e)
        {
            $e->display();
        }

        chdir($cwd);

        return true;
    }

    /**
     * Loads a Controller object, output buffers the display contents and returns
     * them as a string.
     *
     * @param   string      Controller name
     * @param   string      Page content
     * @return  string      Contents
     */
    public static function getControllerOutput($cc = false, $content = '')
    {
        /* Save current execution path */
        $cwd = getcwd();

        /* Change path to the plugin's sandbox, this is where we work */
        chdir($dir = dirname(__FILE__) . '/..');

        if (false === ($obj = self::getController($cc, $content)))
        {
            chdir($cwd);
            return false;
        }

        /* Save output */
        ob_start();

        if (!self::hasSetup() && $cc != 'options')
        {
            /* Special message to tell administrators to configure the plug-in */
            if ($cc === false)
            {
                chdir($cwd);
                ob_start();
                $template = new CATS_Template();
                $template->display('unconfigured.tpl.php');
                $buffer = ob_get_contents();
                ob_end_clean();
                return $buffer;
            }
            else
            {
                chdir($cwd);
                return false;
            }
        }

        /* Capture custom exceptions to provide friendly error messages */
        try
        {
            /* Call the controller, gather output */
            $obj->load();
            if (self::hasFooter($cc)) $obj->footer();
        }
        catch (CATS_Exception $e)
        {
            $e->display();
        }
        $content = ob_get_contents();
        ob_end_clean();

        chdir($cwd);

        return $content;
    }

    /**
     * Instantiates a controller object based on its name.
     *
     * @param   string      Controller name
     * @param   string      Page content
     * @return  mixed       (CATS_Controller) object or (boolean) false on error
     */
    public static function getController($cc = false, $content = '')
    {
        /* Save current execution path */
        $cwd = getcwd();

        /* Change path to the plugin's sandbox, this is where we work */
        chdir($dir = dirname(__FILE__) . '/..');

        /* Default controller is index (job listings) */
        if ($cc === false)
        {
            $cc = 'index';
        }

        /* Sanitize controller name so that we can safely use it as a file/class name */
        $cc = substr(preg_replace('/[^a-z\_]/', '', strtolower($cc)), 0, 20);

        /* Build file name for the controller based on name and make sure it exists */
        $file = sprintf('./controllers/%s.php', $cc);
        if (!file_exists($file))
        {
            chdir($cwd);
            return false;
        }

        /* Include the controller class file */
        include_once($file);

        /* Build the class name, make sure it exists */
        if (!class_exists($className = sprintf('CATS_%s', $cc)))
        {
            chdir($cwd);
            return false;
        }

        /* Instantiate the class object */
        $obj = new $className($content);

        chdir($cwd);

        return $obj;
    }

    /**
     * Used by uri() to gather parameters in a URI and convert them
     * into a PHP array.
     *
     * @param   string      URI string
     * @param   array       Array to merge with (indexed by parameter name)
     * @return  array       Name/value pair
     */
    public static function getParams($uri, $return = array())
    {
        $items = explode('&', $uri);

        foreach ($items as $index => $item)
        {
            if (strpos($item, '=') !== false)
            {
                list($name, $value) = explode('=', $item);
            }
            else
            {
                list($name, $value) = array($item, '');
            }

            if (!empty($name)) $return[$name] = $value;
        }

        return $return;
    }

    /**
     * Generates a link to be used safely to navigate within the CATS CMS
     * plugin within the external CMS. Should be used to generate all
     * <a> links and <form> actions.
     *
     * @param   string      Parameters (i.e.: color=blue&size=3)
     * @return  string      URI
     */
    public static function uri($str)
    {
        return self::getWrapper()->getURI($str);
    }

    /**
     * Retrieves data from a form post and optionally performs basic
     * validation and scalar-ization.
     *
     * @param   string      Form element name
     * @param   boolean     Throw an exception if not found
     * @param   boolean     If passed as an array, turn it into a scalar
     * @return  mixed       Data
     */
    public static function getPost($id, $required = false, $scalar = true)
    {
        if (empty($_REQUEST[$id]))
        {
            if ($required)
            {
                throw new CATS_E_MISSINGPARAM();
            }
            else
            {
                return false;
            }
        }

        $value = $_REQUEST[$id];

        /* Special value to mean: set and empty */
        if ($value == ' ') $value = '';

        if ($scalar && is_array($value))
        {
            $value = reset($value);
        }

        return $value;
    }

    /**
     * Converts a unique cache id into a safe file system path.
     *
     * @param   string      Unique key
     * @return  string      Path
     */
    private static function _getCacheFile($id)
    {
        return sprintf('./cache/%s.ser', $id);
    }

    /**
     * Retrieves data previously stored under a key by updateCache, assuming
     * that it hasn't become stale.
     *
     * @param   string      Unique key
     * @param   boolean     Retrieve recent cache, stale or not
     * @return  mixed       Returns the data stored by updateCache
     */
    public static function getCache($id, $ignoreTimeout = false)
    {
        $file = self::_getCacheFile($id);

        /* No cache file is present */
        if (!file_exists($file))
        {
            return false;
        }

        /* Is a cache file present, but stale? Do we care? */
        if (!$ignoreTimeout && time() - filemtime($file) > CATS_Utility::getOption('cache'))
        {
            return false;
        }

        return unserialize(file_get_contents($file));
    }

    /**
     * Updates a cache key with data. The data can be retrieved with getCache
     * until it becomes stale.
     *
     * @param   string      Unique key
     * @param   mixed       Data to be stored (which will be converted to serialized bytes)
     * @return  boolean     Success?
     */
    public static function updateCache($id, $data)
    {
        $file = self::_getCacheFile($id);

        /* Setting to false removes cache */
        if ($data === false)
        {
            if (file_exists($file)) @unlink($file);
            return true;
        }

        return file_put_contents($file, serialize($data));
    }

    /**
     * Removes stale cache files from the sandbox.
     *
     * @param   integer     Remove files older than this many seconds
     * @return  void
     */
    public static function clearCache($timeToLive = false)
    {
        $cwd = getcwd();
        chdir($dir = dirname(__FILE__) . '/../cache');
        if ($handle = opendir($dir))
        {
            while (false !== ($file = readdir($handle)))
            {
                if (preg_match('/\.ser$/', $file))
                {
                    $path = $dir . '/' . $file;

                    if ($timeToLive !== false && $timeToLive > 0)
                    {
                        $mtime = filemtime($path);

                        if ($mtime > time() - $timeToLive)
                        {
                            continue;
                        }
                    }

                    @unlink($path);
                }
            }
        }
        chdir($cwd);
    }

    /**
     * Builds a URL to an image in the plugin's images directory.
     *
     * @param   string      File name
     * @return  string
     */
    public static function getImageURL($name)
    {
        return self::getWrapper()->getRelativeURL($name);
    }

    /**
     * Returns the URL to the RSS jobs feed.
     *
     * @return  string
     */
    public static function getRSSFeedURL()
    {
        $companyID = CATS_Utility::getOption('company_id');
        $domain = CATS_Utility::getOption('domain');

        if (empty($companyID) || empty($domain)) return '';

        return sprintf('http://%s.%s/rss',
            $companyID,
            $domain
        );
    }

    /**
     * Stores a cookie in the client's browser with a value which can be retrieved
     * later emulating state.
     *
     * @param   string      Name of the cookie
     * @param   string      Value to store
     * @param   integer     Timestamp the cookie should expire
     * @return  boolean     Success?
     */
    public static function setCookie($name, $value, $expire = 28800)
    {
        $host = $_SERVER['HTTP_HOST'];

        /* Get the highest level of the current domain */
        if (preg_match('/(\.[^\.]+(\.co)?\.[^\.]+)$/', $host = $_SERVER['HTTP_HOST'], $matches))
        {
            $host = $matches[1];
        }

        return setcookie($name, $value, $expire, '/', $host, false, true);
    }

    /**
     * Returns an instantiated object reflecting the current user's session.
     * If no previous session is stored in the browser through a cookie, a
     * new session object will be instantiated.
     *
     * @return  CATS_Session
     */
    public static function getSessionObject()
    {
        $cookieName = 'cats_cms_session';

        if (isset($_COOKIE[$cookieName])
            && file_exists($file = self::_getFile($_COOKIE[$cookieName])))
        {
            $obj = unserialize(file_get_contents($file));
            $obj->onLoad();
            return $obj;
        }

        /* Generate a new, unique id */
        $id = self::_getID();

        /* Keep session for 2 weeks */
        $expire = time() + 60 * 60 * 24 * 14;

        self::setCookie($cookieName, $id, $expire);

        $obj = new CATS_Session($id);
        $obj->onLoad();

        return $obj;
    }

    /**
     * Saves the current session to the cache.
     *
     * @param   CATS_Session    Current session
     * @param   string          Session ID
     */
    public static function saveSessionObject($obj, $id)
    {
        $file = self::_getFile($id);
        self::clearCache(60 * 60 * 24 * 14);

        if (!file_put_contents($file, serialize($obj)))
        {
            throw new CATS_E_FILEACCESS();
        }
    }

    /**
     * Retrieves a safe file path to where a session ID should be stored in the
     * cache.
     *
     * @param   string      Unique ID
     * @return  string      File path
     */
    private static function _getFile($id)
    {
        /* Sanitize any un-trusted browser value */
        $id = substr(preg_replace('/[^a-z0-9]/i', '', $id), 0, 32);
        return sprintf('%s/cache/sess_%s.ser', dirname(dirname(__FILE__)), $id);
    }

    /**
     * Generates a unique ID which will be stored in a browser cookie
     * so that we can retrieve the session in future page loads.
     *
     * @return  string
     */
    private static function _getID()
    {
        while(1)
        {
            /* Build an ID seeded by a few dynamic values to make it hard to replicate */
            if (function_exists('md5'))
            {
                $id = md5(base_convert(time() + rand(), 10, 35));
            }
            else
            {
                $id = base_convert(time() * rand(time() / 2, time()), 10, 35);
            }

            $file = self::_getFile($id);

            /* If ID already exists, generate a new one */
            if (!file_exists($file))
            {
                break;
            }
        }

        return $id;
    }

    /**
     * Returns TRUE if the current user can be authenticated with the
     * outer CMS's database to perform administrative functions.
     *
     * @return  boolean
     */
    public static function isCMSAdmin()
    {
        return self::getWrapper()->isAdmin();
    }

    /**
     * Retrieves a stored option.
     *
     * @param   string      Name
     * @return  mixed
     */
    public static function getOption($name)
    {
        return self::getWrapper()->getOption($name);
    }

    public static function getWrapper()
    {
        global $cats_wrapper;

        return $cats_wrapper;
    }

    /**
     * Returns a partial excerpt of a larger string based on the settings'
     * excerpt length. Cuts on a word and adds an ellipses (...). Also
     * removes XHTML formatting.
     *
     * @param   string      Text to truncate
     * @return  string      Excerpt
     */
    public static function getExcerpt($text)
    {
        $len = self::getOption('excerpt');

        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        $words = preg_split('/\s+/', trim(strip_tags($text)));

        if (empty($words)) return '';

        $buffer = '';

        foreach ($words as $word)
        {
            if (strlen($buffer) + strlen($word) + 1 > $len)
            {
                return $buffer . ' ...';
            }

            if (!empty($buffer)) $buffer .= ' ';

            $buffer .= $word;
        }

        return $buffer;
    }

    /**
     * Redirects the user to a new URI using javascript or header redirect
     * depending on the outer wrapper.
     *
     * @param   string      URI
     * @return  void
     */
    public static function redirect($uri)
    {
        self::getWrapper()->redirect($uri);
    }

    /**
     * Moves a file upload into a permanent temporary path with its original
     * file name so that it can be uploaded through CURL.
     *
     * @param   string  Post ID
     * @return  string  File path
     */
    public static function getFileUpload($id)
    {
        if (!isset($_FILES[$id]) || !file_exists($_FILES[$id]['tmp_name'])) return false;

        $tmpname = $_FILES[$id]['tmp_name'];
        $path = dirname($tmpname);
        $name = $_FILES[$id]['name'];

        /* Sanitize the file */
        while (strpos($name, '..') !== false) $name = str_replace('..', '.', $name);
        $name = preg_replace('/[^a-z0-9\.]/i', '', $name);

        $path2 = sprintf('%s/%s', $path, $name);

        if (!move_uploaded_file($tmpname, $path2)) return false;

        return $path2;
    }
}
