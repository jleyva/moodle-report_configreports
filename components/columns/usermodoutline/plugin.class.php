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

class plugin_usermodoutline extends plugin_base{

    function init() {
        $this->fullname = get_string('usermodoutline','report_configreports');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('users');
    }
    
    function summary($data) {
        global $DB;
        // should be a better way to do this
        if ($cm = $DB->get_record('course_modules',array('id' => $data->cmid))) {
            $modname = $DB->get_field('modules','name',array('id' => $cm->module));
            if ($name = $DB->get_field("$modname",'name',array('id' => $data->cmid))) {
                return $data->columname.' ('.$name.')';
            }
        }

        return $data->columname;
    }
    
    function colformat($data) {
        $align = (isset($data->align))? $data->align : '';
        $size = (isset($data->size))? $data->size : '';
        $wrap = (isset($data->wrap))? $data->wrap : '';
        return array($align,$size,$wrap);
    }
    
    // data -> Plugin configuration data
    // row -> Complet user row c->id, c->fullname, etc...
    function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0) {
        global $DB, $CFG;
        if ($cm = $DB->get_record('course_modules',array('id' => $data->cmid))) {
            $mod = $DB->get_record('modules',array('id' => $cm->module));
            if ($instance = $DB->get_record("$mod->name",array('id' => $cm->instance))) {
                $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
               if (file_exists($libfile)) {
                    require_once($libfile);
                    $user_outline = $mod->name."_user_outline";
                    if (function_exists($user_outline)) {
                        if ($course = $DB->get_record('course',array('id' => $this->report->courseid))) {
                            $result = $user_outline($course, $row, $mod, $instance);
                            if ($result) {
                                $returndata = '';
                                if (isset($result->info)) 
                                    $returndata .= $result->info.' ';
                        
                                if ((!isset($data->donotshowtime) || !$data->donotshowtime) && !empty($result->time)) 
                                    $returndata .= userdate($result->time);
                                return $returndata;
                            }
                        }
                    }
                }
            }
        }    
        return '';
    }
    
}

?>