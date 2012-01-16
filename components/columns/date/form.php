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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class date_form extends moodleform {
    function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('date','report_configreports'), '');

        $this->_customdata['compclass']->add_form_elements($mform,$this); 

        $mform->addElement('select', 'date', get_string('date','report_configreports'), array('starttime'=>get_string('starttime','report_configreports'),'endtime'=>get_string('endtime','report_configreports')));

        $formats = array(''=>get_string('default'), 'custom'=>get_string('custom','report_configreports'));
        foreach (array('%A, %d %B %Y', '%d %B %Y', '%d/%B/%Y', '%B %d %Y') as $f)
            $formats[$f] = $f;

        $mform->addElement('select', 'dateformat', get_string('dateformat','report_configreports'), $formats);
        $mform->addElement('text', 'customdateformat', get_string('customdateformat','report_configreports'));
        $mform->disabledif ('customdateformat','dateformat','neq','custom');

        // buttons
        $this->add_action_buttons(true, get_string('add'));

    }    
}

?>