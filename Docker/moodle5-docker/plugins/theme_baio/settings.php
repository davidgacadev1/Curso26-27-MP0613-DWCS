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
 * Ajustes del tema Baio (Administración del sitio > Apariencia > Temas > Baio).
 *
 * @package    theme_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new admin_settingpage('themesettingbaio', get_string('configtitle', 'theme_baio'));

    $ajuste = new admin_setting_configcolourpicker(
        'theme_baio/brandcolor',
        get_string('brandcolor', 'theme_baio'),
        get_string('brandcolor_desc', 'theme_baio'),
        '#0b5c8a'
    );
    // Al cambiar el color hay que recompilar el CSS del tema.
    $ajuste->set_updatedcallback('theme_reset_all_caches');
    $settings->add($ajuste);
}
