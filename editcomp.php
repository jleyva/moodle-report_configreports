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

require_once("../../config.php");

require_once($CFG->dirroot."/report/configreports/locallib.php");
require_once($CFG->dirroot.'/report/configreports/report.class.php');
require_once($CFG->dirroot.'/report/configreports/component.class.php');    
require_once($CFG->dirroot.'/report/configreports/plugin.class.php');

$id = required_param('id', PARAM_INT);
$comp = required_param('comp', PARAM_ALPHA);

if (! $report = $DB->get_record('report_configreports',array('id' => $id)))
    print_error('reportdoesnotexists');


if (! $course = $DB->get_record("course",array( "id" =>  $report->courseid)) ) {
    print_error("No such course id");
}    

// Force user login in course (SITE or Course)
if ($course->id == SITEID) {
    require_login();
    $context = get_context_instance(CONTEXT_SYSTEM);
}    
else{
    require_login($course->id);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
}

$PAGE->set_url('/report/configreports/editreport.php', array('id'=>$id,'comp'=>$comp));
$PAGE->set_context($context);    
$PAGE->set_pagelayout('incourse');
$PAGE->requires->js('/report/configreports/js/configurable_reports.js');

if (! has_capability('report/configreports:managereports', $context) && ! has_capability('report/configreports:manageownreports', $context))
    print_error('badpermissions');

    
if (! has_capability('report/configreports:managereports', $context) && $report->ownerid != $USER->id)
    print_error('badpermissions');
    
require_once($CFG->dirroot.'/report/configreports/reports/'.$report->type.'/report.class.php');

$reportclassname = 'report_'.$report->type;    
$reportclass = new $reportclassname($report->id);

if (!in_array($comp,$reportclass->components))
    print_error('badcomponent');

$elements = cr_unserialize($report->components);
$elements = isset($elements[$comp]['elements'])? $elements[$comp]['elements'] : array();

require_once($CFG->dirroot.'/report/configreports/components/'.$comp.'/component.class.php');    
$componentclassname = 'component_'.$comp;
$compclass = new $componentclassname($report->id);

if ($compclass->form) {
    require_once($CFG->dirroot.'/report/configreports/components/'.$comp.'/form.php');
    $classname = $comp.'_form';
    $editform = new $classname('editcomp.php?id='.$id.'&comp='.$comp,compact('compclass','comp','id','report','reportclass','elements'));
    
    if ($editform->is_cancelled()) {
        redirect($CFG->wwwroot.'/report/configreports/editcomp.php?id='.$id.'&amp;comp='.$comp);
    }
    else if ($data = $editform->get_data()) {
        $compclass->form_process_data($editform);
        add_to_log($report->courseid, 'configurable_reports', 'edit', '', $report->name);
    }
    
    $compclass->form_set_data($editform);

}

if ($compclass->plugins) {
    $currentplugins = array();
    if ($elements) {
        foreach ($elements as $e) {
            $currentplugins[] = $e['pluginname'];
        }
    }
    $plugins = get_list_of_plugins('report/configreports/components/'.$comp);
    $optionsplugins = array();
    foreach ($plugins as $p) {
        require_once($CFG->dirroot.'/report/configreports/components/'.$comp.'/'.$p.'/plugin.class.php');
        $pluginclassname = 'plugin_'.$p;
        $pluginclass = new $pluginclassname($report);
        if (in_array($report->type,$pluginclass->reporttypes)) {
            if ($pluginclass->unique && in_array($p,$currentplugins))
                continue;
            $optionsplugins[$p] = get_string($p,'report_configreports');
        }
    }
    asort($optionsplugins);
}


$title = format_string($report->name).' '.get_string($comp,'report_configreports');
$navlinks = array();
$navlinks[] = array('name' => get_string('managereports','report_configreports'), 'link' => $CFG->wwwroot.'/report/configreports/managereport.php?courseid='.$report->courseid, 'type' => 'title');
$navlinks[] = array('name' => $title, 'link' => null, 'type' => 'title');
$navigation = build_navigation($navlinks);


$PAGE->set_title($title);    
$PAGE->set_heading($title);    
$PAGE->set_cacheable(true);

echo $OUTPUT->header();

$currenttab = $comp;
include('tabs.php');

if ($elements) {
    $table = new stdclass;
    $table->head = array(get_string('idnumber'),get_string('name'),get_string('summary'),get_string('edit'));
    $i = 0;

    
    foreach ($elements as $e) {
        require_once($CFG->dirroot.'/report/configreports/components/'.$comp.'/'.$e['pluginname'].'/plugin.class.php');
        $pluginclassname = 'plugin_'.$e['pluginname'];
        $pluginclass = new $pluginclassname($report);
    
        $editcell = '';
    
        if ($pluginclass->form) {
            $editcell .= '<a href="editplugin.php?id='.$id.'&comp='.$comp.'&pname='.$e['pluginname'].'&cid='.$e['id'].'"><img src="'.$OUTPUT->pix_url('/t/edit').'" class="iconsmall"></a>';
        }

        $editcell .= '<a href="editplugin.php?id='.$id.'&comp='.$comp.'&pname='.$e['pluginname'].'&cid='.$e['id'].'&delete=1&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('/t/delete').'" class="iconsmall"></a>';

        if ($compclass->ordering && $i != 0 && count($elements) > 1)
            $editcell .= '<a href="editplugin.php?id='.$id.'&comp='.$comp.'&pname='.$e['pluginname'].'&cid='.$e['id'].'&moveup=1&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('/t/up').'" class="iconsmall"></a>';
        if ($compclass->ordering && $i != count($elements) -1)
            $editcell .= '<a href="editplugin.php?id='.$id.'&comp='.$comp.'&pname='.$e['pluginname'].'&cid='.$e['id'].'&movedown=1&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('/t/down').'" class="iconsmall"></a>';

        $table->data[] = array('c'.($i+1),$e['pluginfullname'],$e['summary'],$editcell);
        $i++;
    }
    cr_print_table($table);
}
else{
    if ($compclass->plugins)
        echo $OUTPUT->heading(get_string('no'.$comp.'yet','report_configreports'));
}

if ($compclass->plugins) {
    echo '<div class="boxaligncenter">';
    echo '<p class="centerpara">';
    print_string('add');
    echo ': &nbsp;';
    //choose_from_menu($optionsplugins,'plugin','',get_string('choose'),"location.href = 'editplugin.php?id=".$id."&comp=".$comp."&pname='+document.getElementById('menuplugin').value");
    $attributes = array('id'=>'menuplugin');

    echo html_writer::select($optionsplugins,'plugin','', array(''=>get_string('choose')), $attributes);
    $OUTPUT->add_action_handler(new component_action('change', 'menuplugin',array('url'=>"editplugin.php?id=".$id."&comp=".$comp."&pname=")),'menuplugin');
    echo '</p>';
    echo '</div>';
}

if ($compclass->form) {
    $editform->display();
}

if ($compclass->help) {
    echo '<div class="boxaligncenter">';    
    echo '<p class="centerpara">';
    echo $OUTPUT->help_icon('comp_'.$comp,'report_configreports',get_string('comp_'.$comp,'report_configreports'));
    //helpbutton('comp_'.$comp, get_string('componenthelp','report_configreports'),'report_configreports', true, true);
    echo '</p>';
    echo '</div>';
}

echo $OUTPUT->footer();
