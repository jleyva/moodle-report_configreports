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
 
 class component_base {
    
    var $plugins = false;
    var $ordering = false;
    var $form = false;
    var $help = '';
    
    function component_base($report) {
        global $DB, $CFG;

        if (is_numeric($report))
            $this->config = $DB->get_record('report_configreports',array('id' => $report));
        else
            $this->config = $report;
        $this->init();
    }
    
    function __construct($report) {
        $this->component_base($report);
    }
    
    function add_form_elements(&$mform,$fullform) {
        return false;
    }
    
 }

?>