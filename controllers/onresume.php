<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Controller for uploading a new resume.
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


class CATS_onresume extends CATS_Controller
{
    public function init()
    {
        if (!$this->_session->isLoggedIn())
        {
            throw new CATS_E_BADLOGIN('You are not logged in.');
        }

        $resume = $this->_session->getResume();
        if (empty($resume))
        {
            throw new CATS_E_BADPARAM('You currently have no resume.');
        }

        $candidateID = $this->_session->getCandidateID();

        $this->_tmpFiles = array();

        if (!isset($_FILES['file']) || !file_exists($tmpname = $_FILES['file']['tmp_name']))
        {
            throw new CATS_E_MISSINGPARAM('You didn\'t select a file.');
        }

        $path = dirname($tmpname);
        $name = $_FILES['file']['name'];

        /* Sanitize the file */
        while (strpos($name, '..') !== false) $name = str_replace('..', '.', $name);
        $name = preg_replace('/[^a-z0-9\.]/i', '', $name);

        $path2 = sprintf('%s/%s', $path, $name);

        if (!move_uploaded_file($tmpname, $path2) || !file_exists($path2))
        {
            throw new CATS_E_FILEACCESS('Unable to move uploaded file to ' . $path2);
        }

        $api = new CATS_API();
        $xml = $api->onUploadResume($candidateID, $path2);

        $this->_session->setResume(
            intval($xml->id),
            strtotime($xml->created),
            trim($xml->md5),
            intval($xml->size),
            trim($xml->name)
        );

        @unlink($path2);

        CATS_Utility::updateCache('att_' . $candidateID, false);

        CATS_Utility::redirect('cc=resume');
    }
}
