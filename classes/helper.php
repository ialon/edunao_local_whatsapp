<?php

/**
 * @package    local_whatsapp
 * @author     2025 Josemaria Bolanos <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_whatsapp;

defined('MOODLE_INTERNAL') || die();

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
            'fullname' => fullname($user),
            'whatsapp' => $user->profile['whatsapp'],
            'whatsapp_enable' => (bool) $user->profile['whatsapp_enable'],
            'whatsapp_link' => 'https://wa.me/' . $whatsapp . '?text=' . urlencode(get_string('contact_message', 'local_whatsapp', $PAGE->course->fullname)),
        ];

        return $contact;
    }
}
