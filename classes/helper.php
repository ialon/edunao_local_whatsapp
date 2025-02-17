<?php

/**
 * @package    local_whatsapp
 * @author     2025 Josemaria Bolanos <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_whatsapp;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/externallib.php');

class helper {
    public static function get_user_contact($userid, $onlyactive = false) {
        global $PAGE;
        
        $user = get_complete_user_data('id', $userid);

        if (empty($user->profile['whatsapp'])) {
            return;
        }
        if ($onlyactive && empty($user->profile['whatsapp_enable'])) {
            return;
        }

        // Remove everything except numbers.
        $whatsapp = preg_replace('/[^0-9]/', '', $user->profile['whatsapp']);

        $contact = [
            'id' => $user->id,
            'type' => 'user',
            'fullname' => fullname($user),
            'whatsapp' => $user->profile['whatsapp'],
            'whatsapp_enable' => (bool) $user->profile['whatsapp_enable'],
            'whatsapp_link' => 'https://wa.me/' . $whatsapp . '?text=' . urlencode(get_string('contact_message', 'local_whatsapp', $PAGE->course->fullname)),
        ];

        return $contact;
    }
    public static function get_group_contact($courseid) {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $handler = \core_course\customfield\course_handler::create();
        $customfields = $handler->export_instance_data_object($course->id);

        $courselink = $customfields->whatsapp_group_link ?? null;

        $contact = [
            'id' => $course->id,
            'type' => 'course',
            'fullname' => $course->fullname,
            'whatsapp' => null,
            'whatsapp_enable' => !empty($courselink),
            'whatsapp_link' => $courselink
        ];

        return $contact;
    }
}
