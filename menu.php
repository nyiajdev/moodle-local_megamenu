<?php
// This file is part of Moodle - http://moodle.org/
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
 * Manage a menu object. Create, edit, or delete a menu.
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_megamenu\local\form\menu_form;
use local_megamenu\local\persistent\menu;

require_once(__DIR__.'/../../config.php');
require_once("$CFG->libdir/adminlib.php");

global $PAGE, $DB;

$action = required_param('action', PARAM_TEXT);

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/megamenu/menu.php', ['action' => $action]));
$PAGE->navbar->add(get_string('managemenus', 'local_megamenu'), new moodle_url('/local/megamenu/menus.php'));

require_login();
require_capability('local/megamenu:managemenus', $context);

switch ($action) {
    case 'create':
        $PAGE->set_title(get_string('createmenu', 'local_megamenu'));
        $PAGE->set_heading(get_string('createmenu', 'local_megamenu'));
        $PAGE->navbar->add(get_string('createmenu', 'local_megamenu'));

        $form = new menu_form($PAGE->url, [
            'persistent' => null,
        ]);

        if ($data = $form->get_data()) {
            $image = $data->image;
            unset($data->image);
            $menu = new menu(0, $data);
            $menu->create();

            file_save_draft_area_files($image, \context_system::instance()->id, 'local_megamenu', 'menuimages',
                $menu->get('id'), ['subdirs' => 0, 'maxfiles' => 1]);

            \core\notification::success(get_string('menucreated', 'local_megamenu', $menu->to_record()));
            redirect(new moodle_url('/local/megamenu/menus.php'));
        } else if ($form->is_cancelled()) {
            redirect(new moodle_url('/local/megamenu/menus.php'));
        }

        echo $OUTPUT->header();
        $form->display();

        break;

    case 'edit':
        $PAGE->set_title(get_string('editmenu', 'local_megamenu'));
        $PAGE->set_heading(get_string('editmenu', 'local_megamenu'));
        $PAGE->navbar->add(get_string('editmenu', 'local_megamenu'));

        $id = required_param('id', PARAM_INT);
        $url = clone $PAGE->url;
        $url->params(['id' => $id]);
        $PAGE->set_url($url);

        $menu = new menu($id);

        $form = new menu_form($PAGE->url, [
            'persistent' => $menu,
        ]);

        if ($data = $form->get_data()) {
            $image = $data->image;
            unset($data->image);
            $menu->from_record($data);
            $menu->update();

            file_save_draft_area_files($image, \context_system::instance()->id, 'local_megamenu', 'menuimages',
                $menu->get('id'), ['subdirs' => 0, 'maxfiles' => 1]);

            \core\notification::success(get_string('menuedited', 'local_megamenu', $menu->to_record()));
            redirect(new moodle_url('/local/megamenu/menus.php'));
        } else if ($form->is_cancelled()) {
            redirect(new moodle_url('/local/megamenu/menus.php'));
        } else {
            $draftitemid = file_get_submitted_draft_itemid('image');

            file_prepare_draft_area($draftitemid, \context_system::instance()->id, 'local_megamenu', 'menuimages',
                $menu->get('id'), ['subdirs' => 0, 'maxfiles' => 1]);

            $form->set_data(['image' => $draftitemid]);
        }

        echo $OUTPUT->header();
        $form->display();

        break;

    case 'delete':
        $PAGE->set_title(get_string('deletemenu', 'local_megamenu'));
        $PAGE->set_heading(get_string('deletemenu', 'local_megamenu'));
        $PAGE->navbar->add(get_string('deletemenu', 'local_megamenu'));

        $id = required_param('id', PARAM_INT);
        $url = clone $PAGE->url;
        $url->params(['id' => $id]);
        $PAGE->set_url($url);

        $menu = new menu($id);

        if ($confirm = optional_param('confirm', 0, PARAM_BOOL)) {
            $menu->delete();
            \core\notification::success(get_string('menudeleted', 'local_megamenu', $menu->to_record()));
            redirect(new moodle_url('/local/megamenu/menus.php'));
        }

        echo $OUTPUT->header();
        $url = clone $PAGE->url;
        $url->param('confirm', 1);

        $message = get_string('deleteconfirm', 'local_megamenu', $menu->to_record());
        echo $OUTPUT->confirm($message, $url, new moodle_url('/local/megamenu/menus.php'));
        break;
}

echo $OUTPUT->footer();
