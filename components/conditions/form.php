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

class conditions_form extends moodleform {
    function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('static', 'help','',get_string('conditionexprhelp','report_configreports'));
        $mform->addElement('text', 'conditionexpr', get_string('conditionexpr','report_configreports'),'size="50"');
        $mform->addHelpButton('conditionexpr','conditionexpr_conditions', 'report_configreports');
       
        // buttons
        $this->add_action_buttons(true, get_string('update'));

    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // TODO - this reg expr can be improved
        if (!preg_match("/(\(*\s*\bc\d{1,2}\b\s*\(*\)*\s*(\(|and|or|not)\s*)+\(*\s*\bc\d{1,2}\b\s*\(*\)*\s*$/i",$data['conditionexpr']))
            $errors['conditionexpr'] = get_string('badconditionexpr','report_configreports');
    
        if (substr_count($data['conditionexpr'],'(') != substr_count($data['conditionexpr'],')'))
            $errors['conditionexpr'] = get_string('badconditionexpr','report_configreports');
    
        if (isset($this->_customdata['elements']) && is_array($this->_customdata['elements'])) {
            $elements = $this->_customdata['elements'];
            $nel = count($elements);
            if (!empty($elements) && $nel > 1) {
                preg_match_all('/(\d+)/',$data['conditionexpr'],$matches,PREG_PATTERN_ORDER);
                foreach ($matches[0] as $num) {
                    if ($num > $nel) {
                        $errors['conditionexpr'] = get_string('badconditionexpr','report_configreports');
                        break;
                    }
                }
            }
        }

        return $errors;
    }    
}

?>