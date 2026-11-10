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
 * Página del Bar Baio: /local/baio/index.php
 *
 * @package    local_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// 1) Seguridad: usuario identificado y con permiso.
require_login();
$context = \core\context\system::instance();
require_capability('local/baio:view', $context);

// 2) Datos de la página.
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/baio/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_baio'));
$PAGE->set_heading(get_string('pluginname', 'local_baio'));

// 3) Datos para la plantilla: saludo y cursos del usuario.
$cursos = [];
foreach (enrol_get_my_courses(['fullname'], 'fullname ASC') as $curso) {
    $cursos[] = [
        'nombre' => format_string($curso->fullname),
        'url'    => (new moodle_url('/course/view.php', ['id' => $curso->id]))->out(false),
    ];
}
$saludo = get_config('local_baio', 'greeting') ?: get_string('greeting_default', 'local_baio');
$datos = [
    'saludo'    => format_string($saludo),
    'hola'      => get_string('hello', 'local_baio', fullname($USER)),
    'cursos'    => $cursos,
    'haycursos' => !empty($cursos),
];

// 4) Salida: cabecera, plantilla Mustache y pie.
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_baio/saludo', $datos);
echo $OUTPUT->footer();
