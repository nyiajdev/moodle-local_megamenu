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
 * List all menu objects.
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_megamenu\local\table\menu_table;

require(__DIR__.'/../../config.php');
require_once("$CFG->libdir/adminlib.php");

global $USER;

$context = context_system::instance();

admin_externalpage_setup('localmegamenumenus');

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/megamenu/menus.php'));
$PAGE->set_title(get_string('managemenus', 'local_megamenu'));
$PAGE->set_heading(get_string('managemenus', 'local_megamenu'));
$PAGE->navbar->add(get_string('managemenus', 'local_megamenu'));

require_login();
require_capability('local/megamenu:managemenus', $context);

$table = new menu_table('menus');
$table->define_baseurl($PAGE->url);

echo $OUTPUT->header();
echo $OUTPUT->single_button(new moodle_url('/local/megamenu/menu.php', ['action' => 'create']),
    get_string('createmenu', 'local_megamenu'));
$table->out(25, false);
echo $OUTPUT->footer();
