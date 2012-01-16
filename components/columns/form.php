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

class columns_form extends moodleform {
    function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('reporttable','report_configreports'), '');

        $mform->addElement('text', 'tablewidth', get_string('tablewidth','report_configreports'));
        $mform->setType('tablewidth', PARAM_CLEAN);
        $mform->setDefault('tablewidth', '100%');
        $mform->addHelpButton('tablewidth','reporttable', 'report_configreports');

        $options = array('center'=>'center','left'=>'left','right'=>'right');

        $mform->addElement('SELECT', 'tablealign', get_string('tablealign','report_configreports'), $options);
        $mform->setType('tablealign', PARAM_CLEAN);
        $mform->setDefault('tablealign', 'center');
       
        $mform->addElement('text', 'cellspacing', get_string('tablecellspacing','report_configreports'));
        $mform->setType('cellspacing', PARAM_INT);
        $mform->setDefault('cellspacing', '3');
        $mform->setAdvanced('cellspacing');

        $mform->addElement('text', 'cellpadding', get_string('tablecellpadding','report_configreports'));
        $mform->setType('cellpadding', PARAM_INT);
        $mform->setDefault('cellpadding', '3');
        $mform->setAdvanced('cellpadding');
        
        $mform->addElement('text', 'class', get_string('tableclass','report_configreports'));
        $mform->setType('class', PARAM_CLEAN);
        $mform->setAdvanced('class');
       
        // buttons
        $this->add_action_buttons(true, get_string('update'));

    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!preg_match("/^\d+%?$/i",trim($data['tablewidth'])))
            $errors['tablewidth'] = get_string('badtablewidth','report_configreports');

        return $errors;
    }    
}

?>