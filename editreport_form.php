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
 * A report plugin for creating Configurable Reports
 * @package report
 * @subpackage configreports
 * @copyright Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */ 

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class report_edit_form extends moodleform {
    function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'summary', get_string('summary'));
        $mform->setType('summary', PARAM_RAW);
    
        $typeoptions = cr_get_report_plugins($this->_customdata['courseid']);

        $eloptions = array();
        if (isset($this->_customdata['report']->id) && $this->_customdata['report']->id)
            $eloptions = array('disabled'=>'disabled');
        $mform->addElement('select', 'type', get_string("typeofreport",'report_configreports'), $typeoptions,$eloptions);
        $mform->addHelpButton('type','typeofreport', 'report_configreports');
 
        for($i=0;$i<=100;$i++)
            $pagoptions[$i] = $i;
        $mform->addElement('select', 'pagination', get_string("pagination",'report_configreports'), $pagoptions);
        $mform->setDefault('pagination',0);
        $mform->addHelpButton('pagination','pagination', 'report_configreports');

        $mform->addElement('checkbox','jsordering',get_string('ordering','report_configreports'),get_string('enablejsordering','report_configreports'));
        $mform->addHelpButton('jsordering','jsordering', 'report_configreports');

        $mform->addElement('header', 'exportoptions', get_string('exportoptions', 'report_configreports'));
        $options = cr_get_export_plugins();

        foreach ($options as $key=>$val) {
            $mform->addElement('checkbox','export_'.$key,null,$val);
        }

        if (isset($this->_customdata['report']->id) && $this->_customdata['report']->id)
            $mform->addElement('hidden','id',$this->_customdata['report']->id);
        $mform->addElement('hidden','courseid',$this->_customdata['courseid']);

        // buttons
        $this->add_action_buttons(true, get_string('add'));

    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        return $errors;
    }

}

?>