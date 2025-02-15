<?php

/**
 * @package    local_whatsapp
 * @author     2025 Josemaria Bolanos <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 use local_whatsapp\helper;

defined('MOODLE_INTERNAL') || die();

function local_whatsapp_extend_navigation_course(navigation_node $navigation, stdClass $course, $context) {
    global $PAGE, $CFG, $USER;

    // Can manage whatsapp.
    $canmanage = has_capability('local/whatsapp:manage', $context);

    // Load list of teachers in the course.
    $teacherroles = array_keys(get_archetype_roles('editingteacher') + get_archetype_roles('teacher'));
    $teachers = get_role_users($teacherroles, $context, false, 'ra.id, u.id AS userid, r.id AS roleid', 'ra.id ASC');

    // Get their WhatsApp settings.
    $teachercontacts = [];
    $mycontact = NULL;
    foreach ($teachers as $teacher) {
        if ($contact = helper::get_user_contact($teacher->userid, true)) {
            if ($teacher->userid == $USER->id) {
                $mycontact = $contact;
                continue;
            }
            $teachercontacts[] = $contact;
        }
    }

    if (is_null($mycontact)) {
        $mycontact = helper::get_user_contact($USER->id);
    }

    // If user can't manage and there are no contacts, don't show the link.
    if (!$canmanage && empty($teachercontacts)) {
        return;
    }

    $url = new moodle_url('/local/whatsapp/contact.php', ['id' => $course->id]);
    $node = navigation_node::create(
        get_string('whatsapp', 'local_whatsapp'),
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'whatsapp',
        new pix_icon('t/share', '')
    );
    $node->showinflatnavigation = true;

    // Add the node to the end of the navigation.
    $navigation->add_node($node);

    // Construct specific requirejs config.
    $requireconfig = [
        'paths' => [
            'qrcode' => $CFG->wwwroot . '/local/whatsapp/js/qrcode-wrapper',
        ],
    ];

    // Set config for requirejs.
    $PAGE->requires->js_amd_inline('require.config(' . json_encode($requireconfig) . ')');

    // Call init js script.
    $PAGE->requires->js_call_amd('local_whatsapp/main', 'init', [
        $teachercontacts,
        $canmanage,
        $mycontact
    ]);
}
