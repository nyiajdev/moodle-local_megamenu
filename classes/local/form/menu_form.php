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
 * Used for creating or editing menus.
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_megamenu\local\form;

use context_system;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Used for creating or editing menus.
 *
 * @package local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class menu_form extends \core\form\persistent {
    /** @var string Persistent class name. */
    protected static $persistentclass = 'local_megamenu\\local\\persistent\\menu';

    /** @var array Fields to remove from the persistent validation. */
    protected static $foreignfields = ['image'];

    /**
     * Define the form.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('name'));
        $mform->addHelpButton('name', 'name', 'local_megamenu');
        $mform->addRule('name', get_string('required'), 'required');

        $mform->addElement('text', 'label', get_string('label', 'local_megamenu'));
        $mform->addHelpButton('label', 'label', 'local_megamenu');
        $mform->addRule('label', get_string('required'), 'required');

        $mform->addElement('advcheckbox', 'enabled', get_string('enabled', 'local_megamenu'));
        $mform->addHelpButton('enabled', 'enabled', 'local_megamenu');

        $mform->addElement('header', 'menucontent', get_string('menucontent', 'local_megamenu'), 'hello');

        $mform->addElement('autocomplete', 'coursecategories', get_string('coursecategories', 'local_megamenu'),
            \core_course_category::make_categories_list(), ['multiple' => true]);
        $mform->addHelpButton('coursecategories', 'coursecategories', 'local_megamenu');

        $mform->addElement('filemanager', 'image', get_string('image', 'local_megamenu'), null,
            ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['image']]);
        $mform->addHelpButton('image', 'image', 'local_megamenu');

        $mform->addElement('header', 'restrictaccess', get_string('restrictaccess', 'local_megamenu'));

        $mform->addElement('advcheckbox', 'requirelogin', get_string('requirelogin', 'local_megamenu'));
        $mform->addHelpButton('requirelogin', 'requirelogin', 'local_megamenu');

        $systemcontext = context_system::instance();
        $options = [];
        foreach ($systemcontext->get_capabilities() as $capabilityid => $capability) {
            $options[$capabilityid] = sprintf('%s (%s)', get_capability_string($capability->name), $capability->name);
        }
        $mform->addElement('autocomplete', 'requirecapabilities', get_string('requirecapabilities', 'local_megamenu'),
            $options, ['multiple' => true]);
        $mform->addHelpButton('requirecapabilities', 'requirecapabilities', 'local_megamenu');

        $this->add_action_buttons();
    }

    /**
     * Converts data to data suitable for storage.
     *
     * @param stdClass $data
     * @return stdClass
     */
    protected static function convert_fields(stdClass $data) {
        $data->coursecategories = is_array($data->coursecategories) ? implode(',', $data->coursecategories) : '';
        $data->requirecapabilities = is_array($data->requirecapabilities) ? implode(',', $data->requirecapabilities) : '';
        return $data;
    }
}