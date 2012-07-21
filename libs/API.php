<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * API Library
 *
 * Wraps the functionality of calling remote CATS API functions,
 * turning their results into PHP resultsets and caching.
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

include_once('./libs/Application.php');

class CATS_API
{
    public function __construct()
    {
    }

    /**
     * usort() function for sorting internal cache's by an arbitrary
     * array() index.
     *
     * @param   array       First item
     * @param   array       Second item
     * @return  boolean     Change order?
     */
    public function bubbleSort($a, $b)
    {
        if (!strcasecmp($this->_sortDir, 'asc'))
        {
            return $a[$this->_sortColumn] > $b[$this->_sortColumn];
        }
        else
        {
            return $a[$this->_sortColumn] < $b[$this->_sortColumn];
        }
    }

    /**
     * Retrieves the number of jobs in the local cache after applying
     * restrictive filtering (search, categories).
     *
     * @param   string      See args of getJobs() (search, category)
     * @return  integer
     */
    public function getNumJobs($args)
    {
        $this->_getJobs($columns, $rows, $meta);

        $this->_filterBySearch($columns, $rows, $args);

        return count($rows);
    }

    private function _filterBySearch($columns, &$rows, $args)
    {
        /**
         * Perform a basic boolean AND search for all keywords against all possible data
         * for each job, eliminating jobs without a match.
         */
        if (false !== ($arg = $this->_getArg($args, 'search')))
        {
            $words = preg_split('/\s+/', $arg);

            foreach ($rows as $index => $job)
            {
                $match = 0;
                foreach ($columns as $column)
                {
                    foreach ($words as $word)
                    {
                        if (stripos($job[$column['id']], $word) !== false)
                        {
                            if (++$match >= count($words)) break 2;
                        }
                    }
                }

                if ($match < count($words))
                {
                    unset($rows[$index]);
                }
            }

            /**
             * Need to assing new numerical indexes since they're out of order,
             * just so happens array_merge does that for us.
             */
            $rows = array_merge($rows, array());
        }
    }

    public function getSort(&$sortColumn, &$sortDir)
    {
        $this->_getJobs($columns, $rows, $meta);

        $sortColumn = $meta['sort_column'];
        $sortDir = $meta['sort_dir'];
    }

    /**
     * Retrieves and (optionally) modifies the local jobs cache. Options
     * are provided similar to HTTP parameters (in the Wordpress fashion).
     *
     * Possible parameters:
     *
     * [limit=#]
     * The number of jobs returned should not exceed this number.
     *
     * [offset=#]
     * The number of jobs to skip before they are added to the resultset (paging).
     *
     * [sort=column], [sort_dir=asc|desc]
     * The id of the column to sort by and the direction of the sort (ascending/descending).
     * Defaults to what's set in CATS -> Website Tab -> Job Listings.
     *
     * [columns=one,two,three]
     * Columns to return in the resultset. Defaults to those set in
     * CATS -> Website Tab -> Job Listings.
     *
     * [excerpt=yes|no]
     * Yes includes a excerpt of the description shortened with HTML removed for
     * brief excerpt display in a table.
     *
     * [search=text]
     * Performs a keyword search and removes jobs that don't match from the resultset.
     *
     * [ids=1,2,3]
     * Returns jobs in a list of ids. Returned in same order.
     *
     * @param   string      See above for options
     * @return  array       Resultset (see above)
     */
    public function getJobs($args)
    {
        $this->_getJobs($columns, $rows, $meta);

        $this->_filterBySearch($columns, $rows, $args);

        if (false !== ($arg = $this->_getArg($args, 'ids')))
        {
            if (!is_array($arg)) $ids = explode(',', $arg);
            else $ids = $arg;

            foreach ($rows as $index => $row)
            {
                if (!in_array($row['job_order_id'], $ids))
                {
                    unset($rows[$index]);
                }
            }

            /* re-order numerical indexes */
            $rows = array_merge($rows, array());
        }

        if (false !== ($arg = $this->_getArg($args, 'limit')))
        {
            $limit = intval($arg);
        }
        else
        {
            $limit = 25;
        }

        if (false !== ($arg = $this->_getArg($args, 'offset')))
        {
            $offset = intval($arg);
        }
        else
        {
            $offset = 0;
        }

        if (false !== ($arg = $this->_getArg($args, 'sort')))
        {
            if (false === ($this->_sortDir = $this->_getArg($args, 'sort_dir')))
            {
                $this->_sortDir = 'desc';
            }

            $this->_sortColumn = $arg;
            usort($rows, array($this, 'bubbleSort'));
        }

        if (false !== ($arg = $this->_getArg($args, 'columns')))
        {
            $toShow = array_map(
                create_function('$a', 'return urldecode($a);'),
                explode(',', $arg)
            );
        }
        else
        {
            $toShow = $this->getColumnIDsForDisplay();
        }

        if (false !== ($arg = $this->_getArg($args, 'excerpt'))
            && !strcasecmp($arg, 'yes'))
        {
            foreach ($rows as $index => $row)
            {
                $rows[$index]['excerpt'] = CATS_Utility::getExcerpt($row['description']);
            }
        }

        $return = array();

        for ($rowNum = $offset, $i = 0; $i < $limit; $i++, $rowNum++)
        {
            if (!isset($rows[$rowNum]))
            {
                break;
            }

            $mp = array();

            foreach ($toShow as $id)
            {
                $mp[$id] = $rows[$rowNum][$id];
            }

            if (isset($rows[$rowNum][$id = 'excerpt']))
            {
                $mp[$id] = $rows[$rowNum][$id];
            }

            $return[] = $mp;
        }

        return $return;
    }

    /**
     * Used internally to allow Wordpress style HTTP parameters as multiple
     * method arguments.
     *
     * @param   mixed       String or array of arguments
     * @param   string      Name of the parameter to retrieve
     * @return  mixed       The value, or (boolean) false if not found
     */
    private function _getArg($args, $name)
    {
        /* If passed as a PHP array */
        if (is_array($args) && isset($args[$name]))
        {
            return $args[$name];
        }

        /* If passed as Wordpress style HTTP parameter string */
        if (preg_match('/\b' . preg_quote($name) . '=([^&]+)/', $args, $matches))
        {
            return urldecode($matches[1]);
        }

        /* Not found */
        return false;
    }

    /**
     * Retrieves the columns WITH metadata as a PHP array that are displayed
     * by default. The defaults are set in CATS -> Website Tab -> Job Listings.
     *
     * @return  array
     */
    public function getColumnsForDisplay()
    {
        $this->_getJobs($columns, $rows, $meta);
        $return = array();

        foreach ($columns as $column)
        {
            if ($column['default']) $return[] = $column;
        }

        return $return;
    }

    /**
     * Retrieves the column IDs displayed by default in CATS -> Website Tab -> Job Listings.
     *
     * @return  array
     */
    public function getColumnIDsForDisplay()
    {
        $this->_getJobs($columns, $rows, $meta);
        $return = array();

        foreach ($columns as $column)
        {
            if ($column['default']) $return[] = $column['id'];
        }

        return $return;
    }

    /**
     * Retrieves all metadata for a job referenced by its ID # as stored in
     * the most recent jobs cache.
     *
     * @param   integer     Job ID #
     * @return  array       Array indexed by column id
     */
    public function getJob($id)
    {
        $this->_getJobs($columns, $rows, $meta);

        foreach ($rows as $row)
        {
            if ($row['job_order_id'] == $id)
            {
                return $row;
            }
        }

        return false;
    }

    /**
     * Retrieves all applications with their questions and both of their
     * metadata from the most recent cache for a given job order. Used
     * to generate an XHTML application form.
     *
     * @param   integer     Job ID # or 0 for the registration app
     * @return  array
     */
    public function getJobApplication($id = 0)
    {
        $cacheID = sprintf('job_%d', $id);

        if (false === ($cache = CATS_Utility::getCache($cacheID)))
        {
            try
            {
                $xml = $this->_send('get_joborder_applications', 'id=' . $id . '&form=yes&php=yes');
            }
            catch (CATS_E_API $e)
            {
                /**
                 * If we get a server or connection error, lets fallback to a
                 * stale cache versus showing an error (if available).
                 */
                if (false !== ($cache = CATS_Utility::getCache($cacheID, true)))
                {
                    /* Give future calls a timeout before they try the broken request again */
                    CATS_Utility::updateCache($cacheID, $cache);
                    return $cache;
                }
                else
                {
                    throw $e;
                }
            }

            $appLib = new CATS_Application();
            $applications = $appLib->get($xml);

            CATS_Utility::updateCache($cacheID, $cache = $applications);
        }

        return $cache;
    }

    /**
     * Sends collected and validated form data from an XHTML job application
     * back to CATS to perform the necessary actions such as adding the candidate,
     * attaching their resume, adding activity logs, attaching to pipelines,
     * firing workflow and application triggers, generating emails to the
     * applicant and recruiters (if enabled) and anything else required.
     *
     * If the applicant already exists as a candidate, their existing record will
     * be modified, therefore the return candidate ID # may not always be a
     * new record -- it may already have existed.
     *
     * @param   string      Job ID #
     * @param   array       Form data indexed by question ID #
     * @param   string      Source referrer
     * @return  integer     Candidate ID #
     */
    public function onApply($id, $values, $referrer = false)
    {
        $q = 'id=' . $id . '&referrer=' . urlencode($referrer);

        foreach ($values as $name => $value)
        {
            $q .= sprintf('&%d=%s', $name, urlencode($value));
        }

        $result = $this->_send('apply_joborder', $q);

        return $result->candidate_id;
    }

    public function onSendToFriend($id, $subject, $message, $emailto, $email)
    {
        $this->_send('send_to_friend', 'id=' . $id
            . '&subject=' . urlencode($subject)
            . '&message=' . urlencode($message)
            . '&to=' . urlencode($emailto)
            . '&from=' . urlencode($email)
        );

        return true;
    }

    /**
     * Sends collected and validated form data from an XHTML registration form
     * back to CATS to perform the necessary actions such as adding the candidate,
     * attaching their resume, adding activity logs,
     * firing workflow and application triggers, generating emails to the
     * applicant and recruiters (if enabled) and anything else required.
     *
     * If the applicant already exists as a candidate, their existing record will
     * be modified, therefore the return candidate ID # may not always be a
     * new record -- it may already have existed.
     *
     * @param   string      Job ID #
     * @param   array       Form data indexed by question ID #
     * @param   string      Source referrer
     * @return  integer     Candidate ID #
     */
    public function onRegister($email, $password, $values, $referrer = false)
    {
        // FIXME: use hash option
        $q = 'email=' . urlencode($email) . '&password=' . urlencode($password)
            . '&referrer=' . urlencode($referrer);
        foreach ($values as $name => $value)
        {
            $q .= sprintf('&%d=%s', $name, urlencode($value));
        }

        $result = $this->_send('apply_joborder', $q);

        return $result->candidate_id;
    }

    /**
     * Gets the title for a job order based on its ID #.
     *
     * @param   integer     ID #
     * @return  string      Title
     */
    public function getJobTitle($id)
    {
        $this->_getJobs($columns, $rows, $meta);

        foreach ($rows as $job)
        {
            if ($job['job_order_id'] == $id) return $job['title'];
        }

        return '';
    }

    /**
     * Sends an email with the passwod to an email address.
     * An exception is thrown if they don't exist or no password is set.
     *
     * @param   string      Email address
     * @return  void
     */
    public function onForgotPassword($email)
    {
        return $this->_send('portal_forgot_password', 'email=' . urlencode($email));
    }

    /**
     * Processing a candidate login and retrieves account details for
     * profile display.
     *
     * @param   string      Email
     * @param   string      Password
     * @return  SimpleXMLObject
     */
    public function onLogin($email, $password)
    {
        $hash = CATS_Utility::getOption('hash');

        /* For md5-based hashing, make sure mcrypt is installed */
        if (strpos($hash, 'md5') !== false && !function_exists('md5'))
        {
            $hash = '';
        }

        /* For crc32-based hashing, make sure mcrypt is installed */
        if (strpos($hash, 'crc') !== false && !function_exists('crc32'))
        {
            $hash = '';
        }

        /* For sha1-based hashing, make sure mcrypt is installed */
        if (strpos($hash, 'sha1') !== false && !function_exists('sha1'))
        {
            $hash = '';
        }

        switch ($hash)
        {
            case 'md5-salt':
                $password = md5(CATS_Utility::getOption('company_id') . md5($password));
                break;

            case 'md5':
                $password = md5($password);
                break;

            case 'crc32':
                $password = crc32($password);
                break;

            case 'sha1':
                $password = sha1($password);
                break;
        }

        $args = sprintf('email=%s&password=%s&hash=%s',
            urlencode($email),
            urlencode($password),
            $hash
        );

        $result = $this->_send('portal_login', $args);

        if (empty($result->item))
        {
            return false;
        }
        else
        {
            return $result;
        }
    }

    /**
     * Collects a list of public jobs from CATS and stores them locally in
     * the cache.
     *
     * @return  array       Two element array of rows and columns respectfully.
     */
    private function _onSynchronize()
    {
        $columns = $ids = array();
        $rows = $this->_send('get_portal_joborders', 'export_xml=yes');

        foreach ($rows->columns->item as $row)
        {
            $ids[] = $id = strval($row->id);
            $columns[] = array(
                'id' => $id,
                'title' => strval($row->title),
                'width' => intval($row->width),
                'default' => (boolean)(!strcasecmp($row->default, 'yes')),
                'align' => strval($row->align)
            );
        }

        $jobs = array();

        foreach ($rows->rows->item as $job)
        {
            $row = array();

            foreach ($ids as $id)
            {
                $row[$id] = strval($job->$id);
            }

            $jobs[] = $row;
        }

        $meta = array(
            'sort_column' => trim($rows['sort_column']),
            'sort_dir' => trim($rows['sort_dir'])
        );

        return array($columns, $jobs, $meta);
    }

    /**
     * Retrives jobs either from the cache (if not stale) or from CATS directly
     * and places them into two variables passed by reference to the method.
     * If CATS cannot be connected to and a stale cache exists, it will be
     * used.
     *
     * @param   array&      Columns array
     * @param   array&      Jobs array
     * @return  void
     */
    private function _getJobs(&$columns, &$rows, &$meta)
    {
        /* Unserialize once per page view */
        static $_columns;
        static $_rows;
        static $_meta;

        if (!empty($_columns) && !empty($_rows) && !empty($_meta))
        {
            list($columns, $rows, $meta) = array($_columns, $_rows, $_meta);
        }
        else
        {
            $cacheID = 'jobs';
            $cache = CATS_Utility::getCache($cacheID);

            if ($cache === false)
            {
                try
                {
                    list($columns, $rows, $meta) = $this->_onSynchronize();
                }
                catch (CATS_E_API $e)
                {
                    /* Is there a stale cache available? */
                    if (false !== ($cache = CATS_Utility::getCache($cacheID, true)))
                    {
                        list($columns, $rows, $meta) = $cache;
                    }
                    else
                    {
                        throw $e;
                    }
                }

                CATS_Utility::updateCache($cacheID, array($columns, $rows, $meta));
            }
            else
            {
                list($columns, $rows, $meta) = $cache;
            }

            $_columns = $columns;
            $_rows = $rows;
            $_meta = $meta;
        }
    }

    /**
     * Retrieves an XHTML version of the candidate's resume that they can view
     * without having to open Word, Adobe, etc.. Same as "magic preview" feature
     * in CATS.
     *
     * @param   integer     Candidate ID
     * @return  string      XHTML
     */
    public function getResumeXHTML($id)
    {
        $cacheID = sprintf('att_%d', $id);

        if (false !== ($content = CATS_Utility::getCache($cacheID)))
        {
            return $content;
        }

        try
        {
            $xml = $this->_send('get_magic_preview', 'id=' . $id);
        }
        catch (CATS_E_API $e)
        {
            if (false !== ($content = CATS_Utility::getCache($cacheID, true)))
            {
                return $content;
            }
            else
            {
                throw $e;
            }
        }

        $content = trim($xml->item);
        CATS_Utility::updateCache($cacheID, $content);
        return $content;
    }

    /**
     * Sends an updated resume to CATS.
     *
     * @param   integer     Candidate ID
     * @param   string      Path to the file
     * @return  SimpleXMLObject
     */
    public function onUploadResume($id, $path)
    {
        $args = 'data_type=candidate'
            . '&id=' . $id
            . '&file=' . urlencode('@' . $path)
            . '&is_resume=yes';
        $xml = $this->_send('add_attachment', $args);

        return $xml;
    }

    /**
     * Updates the candidate's profile.
     *
     * @param   integer     Candidate ID
     * @param   string      Email
     * @param   string      Password
     * @param   string      Title
     * @param   string      First name
     * @param   string      Middle name
     * @param   string      Last name
     * @param   string      Address
     * @param   string      City
     * @param   string      State
     * @param   string      Post Code
     * @param   string      Phone home
     * @param   string      Phone work
     * @param   string      Phone cell
     * @param   string      Website
     * @return  boolean
     */
    public function onUpdateProfile($candidateID, $email, $password, $title,
        $firstName, $middleName, $lastName, $address, $city, $state, $postCode,
        $phoneHome, $phoneWork, $phoneCell, $website, $file = false)
    {
        $q = 'id=' . $candidateID
            . '&email='         . urlencode($email)
            . '&password='      . urlencode($password)
            . '&title='         . urlencode($title)
            . '&first_name='    . urlencode($firstName)
            . '&middle_name='   . urlencode($middleName)
            . '&last_name='     . urlencode($lastName)
            . '&address='       . urlencode($address)
            . '&city='          . urlencode($city)
            . '&state='         . urlencode($state)
            . '&post_code='     . urlencode($postCode)
            . '&phone_home='    . urlencode($phoneHome)
            . '&phone_work='    . urlencode($phoneWork)
            . '&phone_cell='    . urlencode($phoneCell)
            . '&website='       . urlencode($website);

        if (!empty($file) && file_exists($file))
        {
            $q .= '&file=' . urlencode('@' . $file);
        }

        return $this->_send('portal_profile_update', $q);
    }

    /**
     * Retrieves options stored internally for a CATS site/customer. Used for initial authentication.
     *
     * @return  SimpleXMLObject
     */
    public function getOptions()
    {
        return $this->_send('get_options');
    }

    /**
     * Generates a URL which can be used to connect to the CATS API.
     *
     * @param   string  Name of the API function
     * @return  string  URL
     */
    private function _getURL($func)
    {
        return sprintf('http%s://%s.%s/api/%s?transaction_code=%s',
            CATS_Utility::getOption('ssl') ? 's' : '',
            CATS_Utility::getOption('company_id'),
            CATS_Utility::getOption('domain'),
            $func,
            CATS_Utility::getOption('trans_code')
        );
    }

    /**
     * Calls a remote API function on the CATS server and returns and
     * SimpleXML object as a response. If no connection can be made,
     * then a CATS_E_API() exception is thrown which generates a
     * friendly error page (by default).
     *
     * @param   string      Name of the remote API function
     * @param   string      POST data arguments (i.e.: color=blue&size=13)
     * @return  SimpleXMLElement
     */
    private function _send($func, $postData = '')
    {
        $url = $this->_getURL($func);

        /**
         * If one of the post data parameters is a file (indicated by prefixing it with a @),
         * then we need to separate GET from POST so that Curl uploads the file as
         * expected.
         *
         * This also means that all untrusted client post data sent to _send() should be validated and
         * protected against @ prefixed values (this is done in libs/Applicantion.php) to protect
         * users from uploading random files to CATS.
         */
        if (0 < ($x = preg_match_all('/[&?]([^=]+)=(%40[^&?]+)/', $postData, $matches)))
        {
            $get = str_replace($matches[0], '', $postData);
            if (!empty($get)) $url .= '&' . $get;

            $postData = array();

            for ($i = 0; $i < $x; $i++)
            {
                $file = urldecode($matches[2][$i]);
                $name = urldecode($matches[1][$i]);

                /* File indexes can't be numerical, convert to field_### */
                if (preg_match('/^[0-9]+$/', $name))
                {
                    $postData['field_' . $name] = $file;
                }
                else
                {
                    $postData[$name] = $file;
                }
            }
        }

        /**
         * Initialize a Curl object and set the parameters for transmission to
         * the CATS 2.0 API.
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CATS_Utility::getOption('timeout'));
        curl_setopt($ch, CURLOPT_TIMEOUT, CATS_Utility::getOption('timeout'));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        /* This is required for lighttpd web servers */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

        /* If we're using SSL, we need to register some trusted CAs as curl (by default) has none */
        if (CATS_Utility::getOption('ssl'))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_CAINFO, './certs/ca.crt');
        }

        /* Send the request, save the output and any errors */
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (!empty($error))
        {
            throw new CATS_E_API(sprintf('CURL Error (HTTP %d): %s',
                $info['http_code'],
                $error
            ));
        }

        if (!preg_match('/^<\?xml/', $response))
        {
            throw new CATS_E_API(sprintf('Bad response from CATS API 2.0 server:' . "\n--\n%s",
                $response
            ));
        }

        $xml = @simplexml_load_string($response);

        if (!$xml)
        {
            throw new CATS_E_API(sprintf('Bad/malformed XML response from CATS API 2.0 server: '
                . "\n--\n%s",
                $response
            ));
        }

        if (!isset($xml['success']))
        {
            throw new CATS_E_API(sprintf('Unexpected Responses from CATS API 2.0 Server:' . "\n--\n"
                . '%s',
                $response
            ));
        }

        if (strcasecmp($xml['success'], 'true'))
        {
            throw new CATS_E_API(sprintf('CATS API 2.0 Remote Function Exception:' . "\n--\n"
                . 'HTTP Status Code %d: %s',
                intval($xml->error['code']),
                trim($xml->error)
            ));
        }

        return $xml;
    }
}
