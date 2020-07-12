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
 * Represents a single menu to be rendered.
 *
 * @package    local_megamenu
 * @copyright  2020 NYIAJ LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_megamenu\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a single menu to be rendered.
 *
 * @package local_megamenu
 */
interface menu_interface {

    /**
     * Check if user can view mega menu.
     *
     * @param int $userid
     * @return bool
     */
    public function check_access(int $userid): bool;

    /**
     * Get required capability objects for viewing this menu.
     *
     * @return array
     */
    public function get_required_capabilities(): array;
}