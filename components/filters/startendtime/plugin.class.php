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

class plugin_startendtime extends plugin_base{
    
    function init() {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('startendtime','report_configreports');
        $this->reporttypes = array('sql','timeline','users','courses');
    }
    
    function summary($data) {
        return get_string('filterstartendtime_summary','report_configreports');
    }
    
    function execute($finalelements, $data) {
    
        if ($this->report->type != 'sql')
            return $finalelements;

        $filter_starttime = optional_param('filter_starttime',0,PARAM_RAW);
        $filter_endtime = optional_param('filter_endtime',0,PARAM_RAW);
        if (!$filter_starttime || ! $filter_endtime)
            return $finalelements;
    
        $filter_starttime = make_timestamp($filter_starttime['year'],$filter_starttime['month'],$filter_starttime['day']);
        $filter_endtime = make_timestamp($filter_endtime['year'],$filter_endtime['month'],$filter_endtime['day']);
        
        $operators = array('<','>','<=','>=');

        if (preg_match("/%%FILTER_STARTTIME:([^%]+)%%/i",$finalelements,
$output)) {
            list($field,$operator) = split(':',$output[1]);
            if (!in_array($operator,$operators))
                print_error('nosuchoperator');
            $replace = ' AND '.$field.' '.$operator.' '.$filter_starttime;    
            $finalelements = str_replace('%%FILTER_STARTTIME:'.$output[1].'%%',$replace,$finalelements);
        }    

        if (preg_match("/%%FILTER_ENDTIME:([^%]+)%%/i",$finalelements,
$output)) {
            list($field,$operator) = split(':',$output[1]);
            if (!in_array($operator,$operators))
                print_error('nosuchoperator');
            $replace = ' AND '.$field.' '.$operator.' '.$filter_endtime;    
            $finalelements = str_replace('%%FILTER_ENDTIME:'.$output[1].'%%',$replace,$finalelements);
        }

        $finalelements = str_replace('%STARTTIME%%',$filter_starttime,$finalelements);
        $finalelements = str_replace('%ENDTIME%%',$filter_endtime,$finalelements);

        return $finalelements;
    }
    
    function print_filter(&$mform) {
        global $DB, $CFG;

        $mform->addElement('date_selector', 'filter_starttime', get_string('starttime', 'report_configreports'));
        $mform->setDefault('filter_starttime', time() - 3600 * 24);
        $mform->addElement('date_selector', 'filter_endtime', get_string('endtime', 'report_configreports'));
        $mform->setDefault('filter_endtime', time() + 3600 * 24);

    }
    
}

?>