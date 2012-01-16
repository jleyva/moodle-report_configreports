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

class coursestats_form extends moodleform {
    function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('coursestats','report_configreports'), '');

        $coursestats = array('totalenrolments'=>get_string('statstotalenrolments','report_configreports'),'activeenrolments'=>get_string('statsactiveenrolments','report_configreports'),'activityview'=>get_string('activityview','report_configreports'),'activitypost'=>get_string('activitypost','report_configreports'));
        $mform->addElement('select', 'stat', get_string('stat','report_configreports'), $coursestats);

        $roles = $DB->get_records('role');
        $userroles = array();
        foreach ($roles as $r)
            $userroles[$r->id] = $r->name;
    
        $mform->addElement('select', 'roles', get_string('roles'), $userroles,array('multiple'=>'multiple'));
        $mform->disabledif ('roles','stat','eq','totalenrolments');
        $mform->disabledif ('roles','stat','eq','activeenrolments');

        $this->_customdata['compclass']->add_form_elements($mform,$this);     
    
        // buttons
        $this->add_action_buttons(true, get_string('add'));

    }

    function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);

        $errors = $this->_customdata['compclass']->validate_form_elements($data,$errors);

        if (!isset($CFG->enablestats) || !$CFG->enablestats)
            $errors['stat'] = get_string('globalstatsshouldbeenabled','report_configreports');
    
        if (($data['stat'] == 'activityview' || $data['stat'] == 'activitypost') && !isset($data['roles'])) {
            $errors['roles'] = get_string('youmustselectarole','report_configreports');
        }

        return $errors;
    }
    
}

?>