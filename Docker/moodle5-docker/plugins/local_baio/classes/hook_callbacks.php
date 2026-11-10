<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_baio;

use core\hook\navigation\primary_extend;
use moodle_url;
use navigation_node;

/**
 * Respuestas de local_baio a los hooks (las clases de classes/ se cargan solas).
 *
 * @package    local_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Añade "Bar Baio" a la navegación principal si el usuario tiene permiso.
     *
     * @param primary_extend $hook
     */
    public static function primary_extend(primary_extend $hook): void {
        if (!isloggedin() || isguestuser()
                || !has_capability('local/baio:view', \core\context\system::instance())) {
            return;
        }
        $hook->get_primaryview()->add(
            get_string('pluginname', 'local_baio'),
            new moodle_url('/local/baio/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'local_baio'
        );
    }
}
