<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Builds and collects form data from applications and registration forms.
 *
 * Loads and generates browser output (typically XHTML) and
 * maintains generated variables for output typically assigned
 * by a controller.
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


class CATS_Application
{
    private $_tmpFiles;

    /**
     * Processes an SimpleXMLObject into a friendly PHP array of applications with
     * their associated questions and metadata used to generate an application form.
     *
     * @param   SimpleXMLObject     XML responses from API
     * @return  array
     */
    public function get($xml)
    {
        $applications = array();
        foreach ($xml->item as $application)
        {
            $questions = array();

            foreach ($application->questions->question as $question)
            {
                $form = preg_replace('/<\/?form>/', '', $question->form->asXML());

                /* Add classes for the input and for the width */
                $myClasses = array('ccms-input');

                $required = (boolean)(!strcasecmp($question['required'], 'yes'));

                if ($required) $myClasses[] = 'required';

                if (!in_array($question['type'], array(
                    'CHECKBOXES',
                    'CHECKBOX',
                    'RADIO',
                    'FILE'
                )))
                {
                    $myClasses[] = 'ccms-' . strtolower($question['width']);
                }

                $form = str_replace('{class}', implode(' ', $myClasses), $form);

                /* Don't self-close textarea elements */
                if (strpos($form, '<textarea ') === 0)
                {
                    $form = substr($form, 0, -1) . '></textarea>';
                }

                $questions[] = array(
                    /* Unique Identifier (used when sending to CATS) */
                    'id' => intval($question['id']),
                    /* Is the question required? (can't be empty) */
                    'required' => $required,
                    /* String description of the width (used for class names) */
                    'width' => trim($question['width']),
                    /* Type of data (file, text, checkboxes, etc.) */
                    'type' => trim($question['type']),
                    /* Title of the question (First Name:) */
                    'title' => trim($question->title),
                    /* Comment (shown below question to describe it) */
                    'comment' => trim($question->comment),
                    /* XHTML to generate a form input */
                    'form' => $form,
                    /* PHP code to retrieve the value from POST */
                    'php' => trim($question->php),
                    /* Key to save the value by in the session for auto-completing other applications */
                    'key' => isset($question['key']) ? trim($question['key']) : false
                );
            }

            $applications[] = array(
                'header' => strval($application->header),
                'questions' => $questions
            );
        }

        return $applications;
    }

    /**
     * Captures all form data from an application generated using the data from
     * get().
     *
     * @param   CATS_Session        Session object (input is stored there for auto-complete later)
     * @param   integer             Job order ID (or 0 for registration)
     * @return  array               Name/value pairs
     */
    public function getValuesFromPost($session, $jobOrderID = false)
    {
        $api = new CATS_API();
        $application = $api->getJobApplication($jobOrderID);

        $values = array();

        $this->_tmpFiles = array();

        foreach ($application as $app)
        {
            foreach ($app['questions'] as $question)
            {
                if (!strcasecmp($question['type'], 'FILE'))
                {
                    $value = false;
                    $id = sprintf('field_%d', $question['id']);

                    if (false !== ($path = CATS_Utility::getFileUpload($id)))
                    {
                        $value = '@' . $path;
                        $this->_tmpFiles[] = $path;
                    }
                }
                else
                {
                    eval($question['php']);

                    if (!empty($value) && preg_match('/^@/', $value))
                    {
                        $value = str_replace('@', '', $value);
                    }
                }

                if (empty($value))
                {
                    if ($question['required'])
                    {
                        throw new CATS_E_MISSINGPARAM($question['title'] . ' is a required field.');
                    }
                }
                else
                {
                    $fieldID = 'field_' . $question['id'];
                    if (isset($_REQUEST[$fieldID]) && is_object($session) && !empty($question['key']))
                    {
                        $session->setParameter('field_' . $question['key'], $_REQUEST[$fieldID]);
                    }

                    $values[$question['id']] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * Modifies form data so that values saved in the session are automatically inserted into
     * the appropriate fields to save re-typing.
     *
     * @param   CATS_Session    Session object to retrieve the values from.
     * @param   integer         Job order id or 0 for registration
     * @
     */
    public function getAutoCompletedApplication($session, $jobOrderID = false)
    {
        $api = new CATS_API();
        $application = $api->getJobApplication($jobOrderID);
        foreach ($application as $index => $app)
        {
            foreach ($app['questions'] as $index2 => $question)
            {
                if (!empty($question['key'])
                    && strcasecmp($question['type'], 'FILE')
                    && false !== ($value = $session->getParameter('field_' . $question['key'])))
                {
                    switch ($question['type'])
                    {
                        case 'multiline':
                            $application[$index]['questions'][$index2]['form'] = str_replace(
                                '</textarea>',
                                sprintf('%s</textarea>',
                                    htmlspecialchars($value)
                                ),
                                $question['form']
                            );
                            break;

                        case 'text':
                            $application[$index]['questions'][$index2]['form'] = str_replace(
                                'class="',
                                sprintf('value="%s" class="',
                                    htmlspecialchars($value)
                                ),
                                $question['form']
                            );
                            break;

                        case 'select':
                            $application[$index]['questions'][$index2]['form'] = str_replace(
                                '<option value=""/>',
                                '<option value=""></option>',
                                $question['form']
                            );

                            $application[$index]['questions'][$index2]['form'] = str_replace(
                                sprintf('<option value="%s">',
                                    htmlspecialchars($value)
                                ),
                                sprintf('<option value="%s" selected="selected">',
                                    htmlspecialchars($value)
                                ),
                                $question['form']
                            );
                            break;
                    }
                }
            }
        }

        return $application;
    }

    public function __destruct()
    {
        /* Remove form uploads */
        if (!empty($this->_tmpFiles)) foreach ($this->_tmpFiles as $file)
        {
            if (file_exists($file)) @unlink($file);
        }
    }
}
