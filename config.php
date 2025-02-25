<?php

/**
 * @package    local_whatsapp
 * @author     2025 Josemaria Bolanos <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

global $CFG, $DB, $PAGE, $USER;

$courseid = required_param('courseid', PARAM_INT);
$whatsapp = optional_param('whatsapp_number', '', PARAM_TEXT);
$sharenumber = optional_param('share_number', 0, PARAM_BOOL);
$grouplink = optional_param('group_link', '', PARAM_TEXT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
has_capability('local/whatsapp:manage', $context);

$PAGE->set_url(new moodle_url('/local/whatsapp/config.php'));
$PAGE->set_context($context);

// Update custom profile fields data.
$profilefields = [];
$profilefields['whatsapp'] = $whatsapp;
$profilefields['whatsapp_enable'] = $sharenumber;
profile_save_custom_fields($USER->id, $profilefields);
// Refresh user for $USER variable.
$USER = get_complete_user_data('id', $USER->id);

// Update course group link.
$handler = \core_course\customfield\course_handler::create();
$editablefields = $handler->get_editable_fields($course->id);
$records = \core_customfield\api::get_instance_fields_data($editablefields, $course->id);
foreach ($records as $d) {
    $field = $d->get_field();
    if ($field->get('shortname') === 'whatsapp_group_link') {
        $d->set($d->datafield(), $grouplink);
        $d->set('value', $grouplink);
        $d->set('contextid', $context->id);
        $d->save();
    }
}

$redirecturl = new moodle_url('/course/view.php', ['id' => $courseid]);
redirect($redirecturl);
