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
 * Represents a single menu to be rendered. Menu fields stored in database.
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_megamenu\local\persistent;

use coding_exception;
use local_megamenu\local\menu_interface;
use context;
use core_course_category;
use dml_exception;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a single menu to be rendered. Menu fields stored in database.
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class menu extends \core\persistent implements menu_interface, renderable, templatable {
    /**
     * Table name.
     */
    const TABLE = 'local_megamenu_menu';

    /**
     * @var context
     */
    private $context;

    /**
     * Set context this menu is rendered in. Must be set before rendering.
     *
     * @param context $context
     */
    public function set_context(context $context): void {
        $this->context = $context;
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'name' => [
                'type' => PARAM_TEXT
            ],
            'label' => [
                'type' => PARAM_RAW
            ],
            'enabled' => [
                'type' => PARAM_BOOL,
                'default' => true
            ],
            'coursecategories' => [
                'type' => PARAM_TEXT
            ],
            'requirelogin' => [
                'type' => PARAM_BOOL
            ],
            'requirecapabilities' => [
                'type' => PARAM_TEXT
            ]
        ];
    }

    /**
     * Check if user can view mega menu.
     *
     * @param int $userid
     * @return bool
     */
    public function check_access(int $userid): bool {
        foreach ($this->get_required_capabilities() as $capability) {
            if (!has_capability($capability->name, $this->get_context(), $userid)) {
                return false;
            }
        }

        if ($this->get('requirelogin') && !isloggedin()) {
            return false;
        }

        return true;
    }

    /**
     * Get required capability objects for viewing this menu.
     *
     * @return array
     * @throws dml_exception|coding_exception
     */
    public function get_required_capabilities(): array {
        if ($requirecapabilities = $this->get('requirecapabilities')) {
            global $DB;
            if ($requirecapabilities = explode(',', $requirecapabilities)) {
                [$sql, $params] = $DB->get_in_or_equal($requirecapabilities, SQL_PARAMS_NAMED, 'cap');
                return $DB->get_records_sql('SELECT * FROM {capabilities} WHERE id ' . $sql, $params);
            }
        }

        return [];
    }

    /**
     * Get context this menu is displayed in.
     *
     * @return context
     * @throws coding_exception
     */
    public function get_context() {
        if (!$this->context) {
            throw new coding_exception('Menu context was never set. Did you forget to call set_context()?');
        }
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
     * @throws moodle_exception
     * @throws coding_exception
     */
    public function export_for_template(renderer_base $output) {
        $data = [
            'dropdowntogglelabel' => $this->get('label'),
            'lists' => [],
            'uniqueid' => $this->get('id')
        ];

        $fs = get_file_storage();
        $files = $fs->get_area_files(\context_system::instance()->id, 'local_megamenu', 'menuimages', $this->get('id'));
        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                $data['hasimage'] = true;
                $data['imageurl'] = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                );
            }
        }

        $displaycategories = $this->get('coursecategories') ? explode(',', $this->get('coursecategories')) : [];

        foreach ($displaycategories as $categoryid) {
            if ($category = core_course_category::get($categoryid, IGNORE_MISSING)) {
                if (!$category->id) {
                    continue;
                }
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