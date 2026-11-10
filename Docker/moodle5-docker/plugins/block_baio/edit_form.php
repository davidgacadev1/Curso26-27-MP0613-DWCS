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
 * Formulario de configuración de cada instancia del bloque.
 *
 * @package    block_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Los campos deben empezar por "config_": Moodle los guarda en $this->config.
 */
class block_baio_edit_form extends block_edit_form {

    /**
     * Campos propios del bloque.
     *
     * @param MoodleQuickForm $mform
     */
    protected function specific_definition($mform): void {
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_baio'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('textarea', 'config_menu', get_string('menu', 'block_baio'), ['rows' => 6, 'cols' => 40]);
        $mform->setType('config_menu', PARAM_TEXT);
        $mform->setDefault('config_menu', get_string('defaultmenu', 'block_baio'));
        $mform->addHelpButton('config_menu', 'menu', 'block_baio');
    }
}
