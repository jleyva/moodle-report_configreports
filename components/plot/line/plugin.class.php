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

class plugin_line extends plugin_base{
    
    function init() {
        $this->fullname = get_string('line','report_configreports');
        $this->form = true;
        $this->ordering = true;
        $this->reporttypes = array('timeline', 'sql','timeline');
    }
    
    function summary($data) {
        return get_string('linesummary','report_configreports');
    }
    
    // data -> Plugin configuration data
    function execute($id, $data, $finalreport) {
        global $DB, $CFG;

        $series = array();
        $data->xaxis--;
        $data->yaxis--;
        $data->serieid--;
        $minvalue = 0;
        $maxvalue = 0;

        if ($finalreport) {
            foreach ($finalreport as $r) {
                $hash = md5(strtolower($r[$data->serieid]));            
                $sname[$hash] = $r[$data->serieid];
                $val = (isset($r[$data->yaxis]) && is_numeric($r[$data->yaxis]))? $r[$data->yaxis] : 0;
                $series[$hash][] = $val;
                $minvalue = ($val < $minvalue)? $val : $minvalue;
                $maxvalue = ($val > $maxvalue)? $val : $maxvalue;
            }    
        }

        $params = '';
        
        $i = 0;
        foreach ($series as $h=>$s) {
            $params .= "&amp;serie$i=".base64_encode($sname[$h].'||'.implode(',',$s));
            $i++;
        }

        return $CFG->wwwroot.'/report/configreports/components/plot/line/graph.php?reportid='.$this->report->id.'&id='.$id.$params.'&amp;min='.$minvalue.'&amp;max='.$maxvalue;
    }
    
    function get_series($data) {
        $series = array();
        foreach ($_GET as $key=>$val) {
            if (strpos($key,'serie') !== false) {
                $id = (int) str_replace('serie','',$key);
                list($name, $values) = explode('||',base64_decode($val));
                $series[$id] = array('serie'=> explode(',',$values), 'name'=> $name);
            }
        }
        return $series;
    }
    
}

?>