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

$top = array();

$top[] = new tabobject('report', new moodle_url('/report/configreports/editreport.php', array('id' => $report->id)),
                get_string('report','report_configreports'));

foreach ($reportclass->components as $comptab) {
    $top[] = new tabobject($comptab, new moodle_url('/report/configreports/editcomp.php', array('id' => $report->id, 'comp' => $comptab)),
                get_string($comptab,'report_configreports'));
}

$top[] = new tabobject('viewreport', new moodle_url('/report/configreports/viewreport.php', array('id' => $report->id)),
                get_string('viewreport','report_configreports'));

$tabs = array($top);

print_tabs($tabs, $currenttab);

?>
