<?php
// This file is part of The Bootstrap Moodle theme
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

/**
 * Plugin version info
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_megamenu\local\persistent\menu;

defined('MOODLE_INTERNAL') || die();

/**
 * Render content to navbar.
 *
 * @param renderer_base $renderer
 * @return string
 * @throws coding_exception
 */
function local_megamenu_render_navbar_output(\renderer_base $renderer) {
    global $PAGE, $USER;
    try {
        $output = '';
        foreach (menu::get_records(['enabled' => true]) as $menu) {
            $menu->set_context($PAGE->context);
            if ($menu->check_access($USER->id)) {
                $output .= $renderer->render($menu);
            }
        }
    } catch (Exception $e) {
        // Catch all exceptions since every page will be running this code.
        // This will prevent system wide outage if there's an issue with menus.
        debugging('Menu error: ' . $e->getMessage());
    }
    return $output;
}

/**
 * Files support.
 *
 * @param stdClass $course course object
 * @param stdClass $cm
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return void The file is sent along with it's headers
 */
function local_megamenu_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {

    // Menu images are publicly accessible.
    if ($filearea == 'menuimages') {
        $fs = get_file_storage();
        $file = $fs->get_file($context->id, 'local_megamenu', $filearea, $args[0], '/', $args[1]);

        send_stored_file($file);
    }

    // Let other requests fail for now.
}
