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

// Based on Custom SQL Reports Plugin
// See http://moodle.org/mod/data/view.php?d=13&rid=2884

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class timeline_form extends moodleform {
 function definition() {
        global $DB, $CFG;

        $mform =& $this->_form;

        $options = array('previous'=>get_string('previousdays', 'report_configreports'), 'fixeddate'=>get_string('fixeddate', 'report_configreports'));
        $mform->addElement('select', 'timemode', get_string('timemode', 'report_configreports'),$options);
        $mform->setDefault('timemode','previous');

        $mform->addElement('text', 'previousstart', get_string('previousstart', 'report_configreports'));
        $mform->setDefault('previousstart', 1);
        $mform->setType('previousstart', PARAM_INT);
        $mform->addRule('previousstart', null, 'numeric', null, 'client');
        $mform->disabledif ('previousstart','timemode','eq','fixeddate');

        $mform->addElement('text', 'previousend', get_string('previousend', 'report_configreports'));
        $mform->setDefault('previousend', 0);
        $mform->setType('previousend', PARAM_INT);
        $mform->addRule('previousend', null, 'numeric', null, 'client');
        $mform->disabledif ('previousend','timemode','eq','fixeddate');

        $mform->addElement('checkbox', 'forcemidnight', get_string('forcemidnight', 'report_configreports'));
        $mform->disabledif ('forcemidnight','timemode','eq','fixeddate');

        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'report_configreports'));
        $mform->setDefault('starttime', time() - 3600 * 48);
        $mform->disabledif ('starttime','timemode','eq','previous');

        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'report_configreports'));
        $mform->setDefault('endtime', time() + 3600 * 24);
        $mform->disabledif ('endtime','timemode','eq','previous');

        $mform->addElement('text', 'interval', get_string('timeinterval', 'report_configreports'));
        $mform->setDefault('interval', 1);
        $mform->setType('interval', PARAM_INT);
        $mform->addRule('interval', null, 'numeric', null, 'client');
        $mform->addRule('interval', null, 'nonzero', null, 'client');
        
        $mform->addElement('select', 'ordering', get_string('ordering', 'report_configreports'),array('asc'=>'ASC','desc'=>'DESC'));
        
        $this->add_action_buttons();
    }

    function validation($data, $files) {
        global $DB, $CFG, $db, $USER;

        $errors = parent::validation($data, $files);

        return $errors;
    }
    
}

?>