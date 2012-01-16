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

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the coursenavigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_configreports_extend_navigation_course($navigation, $course, $context) {
    if ($myreports = report_configreports_get_my_reports($course, $context)) {
        foreach ($myreports as $report) {
            $navigation->add($report['name'], $report['url'], navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
        }
    }
}


/**
 * This function returns the reports an user has access to
 *
 * @param stdClass $course The course to object for the report
 * @param stdClass $context Current context
 * @param stdClass $user The currrent user
 */
function report_configreports_get_my_reports($course, $context) {
    global $CFG, $DB, $USER;
    
    require_once($CFG->dirroot."/report/configreports/locallib.php");

    $reports = $DB->get_records('report_configreports',array('courseid' => $course->id),'name ASC');
    $myreports = array();

    if ($reports) {
        foreach ($reports as $report) {
            if ($report->visible && cr_check_report_permissions($report,$USER->id,$context)) {
                $rname = format_string($report->name);
                $url = new moodle_url('/report/configreports/viewreport.php', array('id' => $report->id, 'courseid' => $course->id));
                $myreports[] = array('name' => $rname, 'url' => $url);
            }
        }
    }

    if (has_capability('report/configreports:managereports', $context) || has_capability('report/configreports:manageownreports', $context)) {
        $url = new moodle_url('/report/configreports/managereport.php', array('courseid' => $course->id));        
        $myreports[] = array('name' => get_string('managereports','report_configreports'), 'url' => $url);
    }
    
    return $myreports;
}
