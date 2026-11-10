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
 * Bloque "Menú del día" del Bar Baio.
 *
 * @package    block_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Un bloque es una clase que hereda de block_base y rellena $this->content.
 */
class block_baio extends block_base {

    /**
     * Título por defecto del bloque.
     */
    public function init(): void {
        $this->title = get_string('pluginname', 'block_baio');
    }

    /**
     * Si el profesor ha puesto un título propio en la configuración del bloque, se usa.
     */
    public function specialization(): void {
        if (!empty($this->config->title)) {
            $this->title = format_string($this->config->title);
        }
    }

    /**
     * Contenido del bloque (Moodle lo llama al dibujar la página).
     *
     * @return stdClass
     */
    public function get_content(): stdClass {
        if ($this->content !== null) {
            return $this->content;          // Ya calculado en esta petición.
        }
        $this->content = new stdClass();
        // Sin configurar todavía (bloque recién añadido): el menú por defecto.
        $menu = $this->config->menu ?? get_string('defaultmenu', 'block_baio');
        $platos = array_filter(array_map('trim', explode("\n", $menu)));

        $this->content->text = $platos
            ? html_writer::alist(array_map('s', $platos), ['class' => 'block-baio-menu'])
            : get_string('nomenu', 'block_baio');
        $this->content->footer = html_writer::link(
            new moodle_url('/local/baio/index.php'),
            get_string('morebaio', 'block_baio')
        );
        return $this->content;
    }

    /**
     * Dónde se puede añadir el bloque.
     *
     * @return array
     */
    public function applicable_formats(): array {
        return ['all' => true];
    }

    /**
     * Varias instancias en la misma página.
     *
     * @return bool
     */
    public function instance_allow_multiple(): bool {
        return true;
    }
}
