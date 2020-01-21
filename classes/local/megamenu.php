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

namespace local_megamenu\local;

use context;
use core_course_category;
use dml_exception;
use moodle_url;
use renderer_base;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/course/lib.php');

class megamenu implements megamenu_interface, \renderable, \templatable {

    /**
     * @var context
     */
    private $context;

    /**
     * @param context $context
     */
    public function __construct(context $context) {
        $this->context = $context;
    }

    /**
     * Check if user can view mega menu.
     *
     * @param int $userid
     * @return bool
     */
    public function check_access(int $userid): bool {
        if ($this->require_login() && isloggedin()) {
            return false;
        }

        return true;
    }

    /**
     * Check if this mega menu requires user to be logged in.
     *
     * @return bool
     */
    public function require_login(): bool {
        return get_config('local_megamenu', 'requirelogin');
    }

    /**
     * @return string
     * @throws dml_exception
     */
    public function require_capability(): string {
        return get_config('local_megamenu', 'requirecapability');
    }

    /**
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means:
     * 1. No complex types - only stdClass, array, int, string, float, bool
     * 2. Any additional info that is required for the template is pre-calculated (e.g. capability checks).
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        $data = [
            'dropdowntogglelabel' => 'Courses',
            'lists' => []
        ];
        $displaycategories = explode(',', get_config('local_megamenu', 'displaycategories'));

        foreach ($displaycategories as $categoryid) {
            if ($category = core_course_category::get($categoryid, IGNORE_MISSING)) {
                $list = [
                    'heading' => $category->get_formatted_name(),
                    'items' => []
                ];
                foreach ($category->get_courses() as $course) {
                    if (!$course->visible) {
                        continue;
                    }
                    $list['items'][] = [
                        'url' => new moodle_url('/course/view.php', ['id' => $course->id]),
                        'label' => $course->fullname
                    ];
                }
                $data['lists'][] = $list;
            }
        }
        return $data;
    }
}