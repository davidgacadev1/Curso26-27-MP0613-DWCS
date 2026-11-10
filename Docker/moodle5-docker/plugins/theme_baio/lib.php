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
 * Funciones del tema Baio: de dónde sale su SCSS.
 *
 * @package    theme_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * SCSS principal: el preset por defecto de Boost (Bootstrap + estilos de Moodle).
 *
 * @param theme_config $theme
 * @return string
 */
function theme_baio_get_main_scss_content($theme): string {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
}

/**
 * Variables SCSS que se inyectan antes del preset: aquí se cambian los colores de Bootstrap.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_baio_get_pre_scss($theme): string {
    $marca = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#0b5c8a';
    // Comillas simples: en PHP "$primary" se tomaría como una variable.
    return '$primary: ' . $marca . ";\n"
        . '$secondary: #b5452f;' . "\n"
        . '$body-bg: #f7f3ea;' . "\n";
}

/**
 * SCSS extra que se añade al final (reglas propias).
 *
 * @param theme_config $theme
 * @return string
 */
function theme_baio_get_extra_scss($theme): string {
    return file_get_contents(__DIR__ . '/scss/post.scss');
}
