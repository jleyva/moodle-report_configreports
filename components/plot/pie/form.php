<?php  

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class pie_form extends moodleform {
    function definition() {
        global $DB, $USER, $CFG;

        $mform =& $this->_form;
        $options = array();

        $report = $this->_customdata['report'];

        if ($report->type != 'sql') {
            $components = cr_unserialize($this->_customdata['report']->components);
    
            if (!is_array($components) || empty($components['columns']['elements']))
                print_error('nocolumns');

            $columns = $components['columns']['elements'];
            foreach ($columns as $c) {
                $options[] = $c['summary'];
            }
        }
        else{
    
            require_once($CFG->dirroot.'/report/configreports/report.class.php');
            require_once($CFG->dirroot.'/report/configreports/reports/'.$report->type.'/report.class.php');
    
            $reportclassname = 'report_'.$report->type;    
            $reportclass = new $reportclassname($report);
    
            $components = cr_unserialize($report->components);
            $config = (isset($components['customsql']['config']))? $components['customsql']['config'] : new stdclass;    
    
            if (isset($config->querysql)) {
        
                $sql = $config->querysql;
                $sql = $reportclass->prepare_sql($sql);
                if ($rs = $reportclass->execute_query($sql)) {
                    foreach ($rs as $row) {            
                        $i = 0;
                        foreach ($row as $colname=>$value) {
                            $options[$i] = str_replace('_', ' ', $colname);
                            $i++;
                        }
                        break;
                    }
                    $rs->close();
                }
            }    
        }


        $mform->addElement('header', '', get_string('coursefield','report_configreports'), '');

        $mform->addElement('select', 'areaname', get_string('pieareaname','report_configreports'), $options);
        $mform->addElement('select', 'areavalue', get_string('pieareavalue','report_configreports'), $options);
        $mform->addElement('checkbox', 'group', get_string('groupvalues','report_configreports'));
        
        // buttons
        $this->add_action_buttons(true, get_string('add'));

    }

}

?>