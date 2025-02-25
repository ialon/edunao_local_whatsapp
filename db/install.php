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

    // Find/Create the user category.
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
        $field->description = '<p>Enter the phone number in international format without "+" or special characters. For example, use <strong>11234567890</strong> for the US or <strong>33123456789</strong> for France</p>';
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

    // Find/Create the course category.
    $category = $DB->get_record('customfield_category', ['name' => 'WhatsApp']);
    if (!$category) {
        $category = new stdClass();
        $category->name = 'WhatsApp';
        $category->description = '';
        $category->descriptionformat = 0;
        $category->sortorder = $DB->count_records('customfield_category') + 1;
        $category->timecreated = time();
        $category->timemodified = time();
        $category->component = 'core_course';
        $category->area = 'course';
        $category->itemid = 0;
        $category->contextid = 1;
        $category->id = $DB->insert_record('customfield_category', $category);
    }

    // Find/Create the course field.
    $grouplink = $DB->get_record('customfield_field', ['shortname' => 'whatsapp_group_link']);
    if (!$grouplink) {
        $field = new stdClass();
        $field->shortname = 'whatsapp_group_link';
        $field->name = 'Group chat link';
        $field->type = 'text';
        $field->description = '<p>To get a WhatsApp group link, open the group chat, tap the group name, select <strong>Invite via link</strong>, and copy the link.</p>';
        $field->descriptionformat = 1;
        $field->sortorder = $DB->count_records('customfield_field', ['categoryid' => $category->id]) + 1;
        $field->categoryid = $category->id;
        $field->configdata = '{"required":"0","uniquevalues":"1","defaultvalue":"","displaysize":50,"maxlength":1333,"ispassword":"0","link":"","locked":"0","visibility":"2"}';
        $field->timecreated = time();
        $field->timemodified = time();
        $field->id = $DB->insert_record('customfield_field', $field);
    }

    return true;
}
