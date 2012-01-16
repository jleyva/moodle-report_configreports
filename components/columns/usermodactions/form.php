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

class usermodactions_form extends moodleform {
    function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('usermodactions','report_configreports'), '');

        $columns = $DB->get_columns('user');

        $modules = array();

        // Fix for http://tracker.moodle.org/browse/CONTRIB-2945
        require_once($CFG->dirroot."/course/lib.php");
        get_all_mods($this->_customdata['report']->courseid, $mods, $modnames, $modnamesplural, $modnamesused);

        if ($mods) {
            foreach ($mods as $m) {
                $instance = $DB->get_record("$m->modname",array( "id" =>  "$m->instance"));
                $modules[$m->id] = $instance->name;
            }
        }

        $mform->addElement('select', 'cmid', get_string('module','report_configreports'), $modules);

        $this->_customdata['compclass']->add_form_elements($mform,$this); 

        // buttons
        $this->add_action_buttons(true, get_string('add'));

    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $errors = $this->_customdata['compclass']->validate_form_elements($data,$errors);

        return $errors;
    }
    
}

?>