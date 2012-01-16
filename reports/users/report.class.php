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

class report_users extends report_base{
    
    function init() {
        $this->components = array('columns','conditions','ordering','filters','template','permissions','calcs','plot');
    }    

    function get_all_elements() {
        global $DB;

        $elements = array();
        $rs = $DB->get_recordset('user', null, '', 'id');
        foreach ($rs as $result) {
            $elements[] = $result->id;
        }
        $rs->close();
        return $elements;
    }
    
    function get_rows($elements, $sqlorder = '') {
        global $DB, $CFG;
    
        if (!empty($elements)) {
            list($usql, $params) = $DB->get_in_or_equal($elements);    
            return $DB->get_records_select('user',"id $usql", $params, $sqlorder);
        }    
        else{
            return array();
        }
    }
    
}

?>