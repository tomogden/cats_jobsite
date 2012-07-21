<?php
/**
 * @package CATS_JobSite
 * @author Andrew P. Kandels
 * @copyright 2009 - 2010 CATS Software, Inc.
 *
 * Template Class
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


class CATS_Template
{
    /**
     * Passes text through any localization functions in the wrapper and
     * echoes it.
     *
     * @param   string      Text to translate
     */
    public function __($text)
    {
        echo CATS_Utility::getWrapper()->getLocalizedString($text);
    }

    /**
     * Similar to __() except that the string is returned.
     *
     * @param   string      Text to translate
     * @return  string      Translated text
     */
    public function _e($text)
    {
        return CATS_Utility::getWrapper()->getLocalizedString($text);
    }

    /**
     * Assigns a variable which can be referenced by the displayed
     * template during output.
     *
     * @param   string      Name of the variable
     * @param   mixed       Value
     * @return  void
     */
    public function assign($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Called from within the template during output to echo a
     * string safely, escaping any XHTML reserved characters with
     * entities.
     *
     * @param   string      Text to be echoed
     * @return  void
     */
    private function _($text)
    {
        echo htmlspecialchars($text);
    }

    /**
     * Loads a template (usually a file ending in .tpl.php) which can
     * include valid XHTML and optional embedded PHP inline source code.
     *
     * The path for templates is located in the framework directory's
     * "templates" sub-directory. As an alternative (and to prevent the
     * templates from being overwritten), templates can be saved in
     * the mytemplates sub-directory. If they exist in mytemplates,
     * they will be used instead of their templates counterpart.
     *
     * @param   string      Path to file
     * @return  void
     */
    public function display($file)
    {
        /* Check for custom template */
        if (file_exists($path = sprintf('./mytemplates/%s', $file)))
        {
            include($path);
        }

        /* Otherwise, use the template in the default path */
        else if (file_exists($path = sprintf('./templates/%s', $file)))
        {
            include($path);
        }

        /* Template does not exist */
        else
        {
            throw new CATS_E_NOTEMPLATE();
        }
    }
}
