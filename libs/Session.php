<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Session Class
 *
 * Each visitor that logs in or makes a major change is then associated
 * with a session object stored serialized in the cache and later
 * retrieved by a cookie. We do our own session handling to avoid
 * conflicts with existing PHP session timeouts and to fix possible
 * issues with the CMS having to include our session library before
 * sessions can be loaded by PHP.
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

class CATS_Session
{
    private $_id;
    private $_recentJobs;
    private $_loggedIn;
    private $_parameters;
    private $_email;
    private $_password;
    private $_candidateID;
    private $_applications;
    private $_referrer;
    private $_resume;
    private $_mockLang = array();
    private $_mockFormat;

    public function __construct($id)
    {
        $this->_id = $id;
        $this->_recentJobs = array();
        $this->_loggedIn = false;
        $this->_parameters = array();
        $this->_applications = array();

        /* Track referrer for application */
        if (isset($_SERVER[$n = 'HTTP_REFERER'])
            && preg_match('!^https?://([^/]+)!', $_SERVER['HTTP_REFERER'], $matches))
        {
            $this->_referrer = $matches[1];
        }
    }

    public function onLoad()
    {

    }

    public function printParams()
    {
        die(print_r($this->_parameters));
    }

    public function getReferrer()
    {
        return $this->_referrer;
    }

    public function logout()
    {
        $this->_loggedIn = $this->_candidateID = $this->_referrer = null;
        $this->_parameters = $this->_applications = array();
        $this->_email = $this->_password = $this->_candidateID = null;
    }

    public function login($id, $email, $password)
    {
        $this->_loggedIn = true;
        $this->_email = $email;
        $this->_password = $password;
        $this->_candidateID = $id;
        $this->_applications = array();
        $this->_resume = null;
    }

    public function addApplication($id, $when = false)
    {
        if (empty($this->_applications)) $this->_applications = array();
        if (empty($when)) $when = time();
        $this->_applications[$id] = $when;
    }

    public function getApplications()
    {
        return $this->_applications;
    }

    public function getEmail()
    {
        return $this->_loggedIn ? $this->_email : false;
    }

    public function getPassword()
    {
        return $this->_loggedIn ? $this->_password : false;
    }

    public function getCandidateID()
    {
        return $this->_loggedIn ? $this->_candidateID : false;
    }

    public function setParameter($name, $value)
    {
        if ($value === false && isset($this->_parameters[$name]))
        {
            unset($this->_parameters[$name]);
        }
        else
        {
            $this->_parameters[$name] = $value;
        }
    }

    public function getParameter($name)
    {
        return (isset($this->_parameters[$name]) ? $this->_parameters[$name] : false);
    }

    public function hasRecentJobs()
    {
        return !empty($this->_recentJobs);
    }

    public function isLoggedIn()
    {
        return !empty($this->_loggedIn);
    }

    public function getRecentJobs()
    {
        if (empty($this->_recentJobs)) return array();

        $api = new CATS_API();

        $jobs = $api->getJobs(
            'ids=' . implode(',', $this->_recentJobs) . '&columns=title,job_order_id',
            true
        );

        return $jobs;
    }

    public function addRecentJob($id)
    {
        $recentJobs = CATS_Utility::getOption('recentjobs');

        /* Are we tracking any? */
        if ($recentJobs <= 0) return;

        /* Is it already in the list? If so, move it to the front */
        if (false !== ($index = array_search($id, $this->_recentJobs)))
        {
            unset($this->_recentJobs[$index]);
        }

        /* Make room */
        while (count($this->_recentJobs) > ($recentJobs - 1))
        {
            array_pop($this->_recentJobs);
        }

        array_unshift($this->_recentJobs, $id);

        /* re-order the indexes */
        $this->_recentJobs = array_merge($this->_recentJobs, array());
    }

    /**
     * Save session to the file system to maintain values.
     *
     * @return  void
     */
    public function __destruct()
    {
        $toSave = false;

        /* User has viewed jobs, save them for "My Recent Views" */
        if (!empty($this->_recentJobs)) $toSave = true;
        if (!empty($this->_parameters)) $toSave = true;
        if (!empty($this->_loggedIn)) $toSave = true;

        if ($toSave)
        {
            CATS_Utility::saveSessionObject($this, $this->_id);
        }
    }

    public function getResume()
    {
        return $this->_resume;
    }

    /**
     * Stores resume metadata for quick access.
     *
     * @param   integer     Attachment ID
     * @param   timestamp   When it was created
     * @param   string      MD5 checksum
     * @param   integer     File size (bytes)
     * @param   string      File name
     * @return void
     */
    public function setResume($id, $created, $md5, $size, $name)
    {
        $this->_resume = array(
            'id' => $id,
            'created' => date('M j, Y', $created),
            'md5' => $md5,
            'size' => number_format($size / 1024, 1) . ' kb',
            'name' => $name
        );
    }

    /**
     * Creates a fake, unused PHP file in the format:
     *
     * <?php _e("String #1"); ?>
     * <?php _e("String #2"); ?>
     *
     * It is designed so that utilities like Gettext can scan it for
     * literal language strings, as being CMS independent this framework
     * has to be functional for a variety of methods.
     *
     * @param   string      Format of each line (above ex would be "_e($value);")
     * @return  void
     */
    public function loadMockLang($format = false)
    {
        /* Save current execution path */
        $cwd = getcwd();

        /* Change path to the plugin's sandbox, this is where we work */
        chdir($dir = dirname(__FILE__) . '/..');

        if (!file_exists($file = './lang.tpl.php'))
        {
            if (!@file_put_contents($file, "<?php\n"
                . "/**\n"
                . " * This file contains literal string translations.\n"
                . " * It is updated automatically as strings are added \n"
                . " * for Gettext or similar scanning support.\n"
                . " */\n?>\n"
            ))
            {
                /* No need to panic, we may already have a .mo file for translations */
                return;
            }
        }

        $this->_mockLang = array();
        if ($format !== false)
        {
            $this->_mockFormat = $format;
        }

        $fp = @fopen($file, 'rt');
        if ($fp)
        {
            while (!feof($fp))
            {
                $line = fgets($fp);

                $str = str_replace('VALUE', '([^"]+)', preg_quote($this->_mockFormat));

                if (preg_match('/^<\?php\s*' . $str . '\s*\?>\s*$/', $line, $matches))
                {
                    if (!in_array($matches[1], $this->_mockLang))
                    {
                        $this->_mockLang[] = $matches[1];
                    }
                }
            }

            fclose($fp);
        }

        chdir($cwd);
    }

    public function useMockLang($text)
    {
        if (in_array($text, $this->_mockLang))
        {
            return;
        }

        /* Reload the file and try again (other visitors may have already added it) */
        $this->loadMockLang();
        if (in_array($text, $this->_mockLang))
        {
            return;
        }

        /* Save current execution path */
        $cwd = getcwd();

        /* Change path to the plugin's sandbox, this is where we work */
        chdir($dir = dirname(__FILE__) . '/..');

        $file = './lang.tpl.php';

        /* Add to the lang file */
        $fp = @fopen($file, 'at');

        /* Can we open the file? */
        if ($fp)
        {
            fprintf($fp, "\n" . '<?php ' . str_replace('VALUE', '%s', $this->_mockFormat) . ' ?>', $text);
            fclose($fp);

            chdir($cwd);
        }

        /* Add to the array so we don't do it again */
        $this->_mockLang[] = $text;
    }
}
