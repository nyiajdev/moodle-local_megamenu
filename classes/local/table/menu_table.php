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
 * Table to list menus.
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_megamenu\local\table;

use coding_exception;
use dml_exception;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Table to list menus.
 *
 * @package local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class menu_table extends \table_sql
{
    /**
     * Build new table.
     *
     * @param string $uniqueid
     * @throws coding_exception
     */
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        $headers[] = get_string('name');
        $headers[] = get_string('enabled', 'local_megamenu');
        $headers[] = get_string('label', 'local_megamenu');
        $headers[] = get_string('actions');
        $columns[] = 'name';
        $columns[] = 'enabled';
        $columns[] = 'label';
        $columns[] = 'actions';

        $this->define_columns($columns);
        $this->define_headers($headers);
    }

    /**
     * Menu name.
     *
     * @param stdClass $data
     * @return string
     * @throws moodle_exception
     */
    public function col_name($data) {
        return \html_writer::link(new \moodle_url('/local/megamenu/menu.php', [
            'id' => $data->id,
            'action' => 'edit'
        ]), $data->name);
    }

    /**
     * If menu is enabled.
     *
     * @param stdClass $data
     * @return string
     * @throws coding_exception
     */
    public function col_enabled($data) {
        if ($data->enabled) {
            return sprintf('<span class="badge badge-success">%s</span>', get_string('enabled', 'local_megamenu'));
        } else {
            return sprintf('<span class="badge badge-danger">%s</span>', get_string('disabled', 'local_megamenu'));
        }
    }

    /**
     * Actions for tags.
     *
     * @param stdClass $data
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function col_actions($data) {
        global $OUTPUT;

        return $OUTPUT->single_button(
                new \moodle_url('/local/megamenu/menu.php', ['action' => 'edit', 'id' => $data->id]),
                get_string('edit', 'local_megamenu'), 'get') .
            $OUTPUT->single_button(
                new \moodle_url('/local/megamenu/menu.php', ['action' => 'delete', 'id' => $data->id]),
                get_string('delete', 'local_megamenu'), 'get');
    }

    /**
     * Get menu data for display in table.
     *
     * @param int $pagesize
     * @param bool $useinitialsbar
     * @throws dml_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        list($wsql, $params) = $this->get_sql_where();

        $sql = 'SELECT * FROM {local_megamenu_menu} m ' . $wsql;

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sql = $sql . ' ORDER BY ' . $sort;
        }

        if ($pagesize != -1) {
            $countsql = 'SELECT COUNT(DISTINCT m.id) FROM {local_megamenu_menu} m ' . $wsql;
            $total = $DB->count_records_sql($countsql, $params);
            $this->pagesize($pagesize, $total);
        } else {
            $this->pageable(false);
        }

        $this->rawdata = $DB->get_recordset_sql($sql, $params, $this->get_page_start(), $this->get_page_size());
    }
}
