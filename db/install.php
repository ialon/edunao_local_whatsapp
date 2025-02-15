<?php

/**
 * Install code for WhatsApp local plugin.
 *
 * @package    local_whatsapp
 * @author     2025 Josemaria Bolanos <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Perform the post-install procedures.
 */
function xmldb_local_whatsapp_install() {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/lib/datalib.php');

    // Find/Create the category.
    $category = $DB->get_record('user_info_category', ['name' => 'WhatsApp']);
    if (!$category) {
        $category = new stdClass();
        $category->name = 'WhatsApp';
        $category->sortorder = $DB->count_records('user_info_category') + 1;
        $category->id = $DB->insert_record('user_info_category', $category);
    }

    // Find/Create the user fields.
    $number = $DB->get_record('user_info_field', ['shortname' => 'whatsapp']);
    if (!$number) {
        $field = new stdClass();
        $field->shortname = 'whatsapp';
        $field->name = 'WhatsApp Number';
        $field->datatype = 'text';
        $field->description = '';
        $field->descriptionformat = 1;
        $field->categoryid = $category->id;
        $field->sortorder = $DB->count_records('user_info_field', ['categoryid' => $category->id]) + 1;
        $field->required = 0;
        $field->locked = 0;
        $field->visible = 3;
        $field->forceunique = 0;
        $field->signup = 0;
        $field->defaultdata = '';
        $field->defaultdataformat = 0;
        $field->param1 = 30;
        $field->param2 = 2048;
        $field->param3 = 0;
        $field->param4 = '';
        $field->param5 = '';
        $field->id = $DB->insert_record('user_info_field', $field);
    }

    $enable = $DB->get_record('user_info_field', ['shortname' => 'whatsapp_enable']);
    if (!$enable) {
        $field = new stdClass();
        $field->shortname = 'whatsapp_enable';
        $field->name = 'Share with my students';
        $field->datatype = 'checkbox';
        $field->description = '';
        $field->descriptionformat = 1;
        $field->categoryid = $category->id;
        $field->sortorder = $DB->count_records('user_info_field', ['categoryid' => $category->id]) + 1;
        $field->required = 0;
        $field->locked = 0;
        $field->visible = 3;
        $field->forceunique = 0;
        $field->signup = 0;
        $field->defaultdata = 0;
        $field->defaultdataformat = 0;
        $field->id = $DB->insert_record('user_info_field', $field);
    }

    return true;
}
