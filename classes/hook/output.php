<?php

/**
 * Hook to allow plugins to add any elements to the page <head> html tag.
 *
 * @package    local_whatsapp
 * @author     2025 Josemaria Bolanos <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_whatsapp\hook;

defined('MOODLE_INTERNAL') || die();

class output {
    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function hook_before_head(\core\hook\output\before_standard_head_html_generation $hook) {
        global $CFG;

        // Require  library.
        require_once($CFG->dirroot.'/local/whatsapp/lib.php');

        // Call callback implementation.
        return before_head();
    }
}
