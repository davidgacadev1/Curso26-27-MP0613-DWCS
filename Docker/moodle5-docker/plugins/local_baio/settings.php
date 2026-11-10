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

/**
 * Ajustes de local_baio (Administración del sitio > Extensiones > Extensiones locales).
 *
 * @package    local_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_baio', get_string('pluginname', 'local_baio'));
    $settings->add(new admin_setting_configtext(
        'local_baio/greeting',                  // Se lee con get_config('local_baio', 'greeting').
        get_string('greeting', 'local_baio'),
        get_string('greeting_desc', 'local_baio'),
        get_string('greeting_default', 'local_baio'),
        PARAM_TEXT
    ));
    $ADMIN->add('localplugins', $settings);
}
