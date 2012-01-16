<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/** Configurable Reports
 * A report plugin for creating customizable reports
 * @package report
 * @subpackage configreports
 * @copyright Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/report/configreports/plugin.class.php');

class plugin_reportscapabilities extends plugin_base{
    
    function init() {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('reportscapabilities','report_configreports');
        $this->reporttypes = array('courses','sql','users','timeline','categories');
    }
    
    function summary($data) {
        return get_string('reportscapabilities_summary','report_configreports');
    }
    
    function execute($userid, $context, $data) {
        global $DB, $CFG;

        return has_capability('moodle/site:viewreports',get_context_instance(CONTEXT_SYSTEM),$userid);

    }
    
}

?>