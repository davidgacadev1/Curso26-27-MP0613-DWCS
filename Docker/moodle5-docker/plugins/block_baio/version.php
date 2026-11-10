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
 * Versión del plugin block_baio.
 *
 * @package    block_baio
 * @copyright  2026 BK Programación
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_baio';     // tipo_nombre ("frankenstyle"): carpeta blocks/baio
$plugin->version   = 2026102400;       // AAAAMMDDXX: súbela cada vez que cambies db/, lang/ o settings
$plugin->requires  = 2025041400;       // Moodle 5.0 o posterior
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '1.0';
