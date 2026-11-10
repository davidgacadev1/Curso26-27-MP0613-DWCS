<?php  // Fichero de configuración de Moodle para el entorno Docker de DESARROLLO.
// compose.yaml lo monta como moodle/config.php (solo lectura). Los datos de
// conexión llegan en variables de entorno (fichero .env).

unset($CFG);
global $CFG;
$CFG = new stdClass();

// --- Base de datos ----------------------------------------------------------
$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('MOODLE_DB_HOST') ?: 'db';   // nombre del servicio, no "localhost"
$CFG->dbname    = getenv('MOODLE_DB_NAME') ?: 'moodle';
$CFG->dbuser    = getenv('MOODLE_DB_USER') ?: 'moodle';
$CFG->dbpass    = getenv('MOODLE_DB_PASSWORD') ?: 'moodle';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = [
    'dbpersist'   => 0,
    'dbport'      => 3306,
    'dbsocket'    => '',
    'dbcollation' => 'utf8mb4_unicode_ci',
];

// --- Rutas ------------------------------------------------------------------
$CFG->wwwroot   = getenv('MOODLE_URL') ?: 'http://localhost:8100';
$CFG->dataroot  = '/var/www/moodledata';               // volumen con nombre (fuera de la web)
$CFG->admin     = 'admin';
$CFG->directorypermissions = 02777;

// El servidor web ya envía a r.php lo que no es un fichero (docker/apache/moodle.conf)
$CFG->routerconfigured = true;

// --- Correo: todo a Mailpit (http://localhost:8027) --------------------------
$CFG->smtphosts      = 'mailpit:1025';
$CFG->noreplyaddress = 'noreply@bkprogramacion.test';

// --- Opciones de DESARROLLO (nunca en un servidor real) ----------------------
// OJO: el nivel de depuración (debug = DEBUG_DEVELOPER, debugdisplay) NO se pone aquí,
// sino en la base de datos (lo hace scripts/instalar.sh, o Administración del sitio >
// Desarrollo > Depuración). Si $CFG->debug está en config.php, Moodle vuelve a explorar
// todas las carpetas de plugins en CADA petición: con el código en una carpeta de
// Windows, cada página pasa de 1-2 segundos a más de 15.
$CFG->perfdebug        = 15;     // tiempo, memoria y consultas al pie de la página
$CFG->debugpageinfo    = 1;      // nombre de la página y de la plantilla al pie
$CFG->cachejs          = false;  // no cachear JavaScript (ves tus cambios sin purgar cachés)
$CFG->cachetemplates   = false;  // ni las plantillas Mustache
$CFG->langstringcache  = false;  // ni las cadenas de idioma
$CFG->passwordpolicy   = 0;      // permite contraseñas sencillas en pruebas
// Recompilar el SCSS del tema en cada petición: actívalo SOLO mientras cambies el SCSS
// (cada página pide más de 100 hojas de estilo y tarda minutos). Si no, purga cachés.
$CFG->themedesignermode = false;

// Pruebas automáticas (opcional): vendor/bin/phpunit
$CFG->phpunit_prefix   = 'phpu_';
$CFG->phpunit_dataroot = '/var/www/phpunitdata';

$CFG->pathtophp = '/usr/local/bin/php';

require_once(__DIR__ . '/lib/setup.php');

// No hay etiqueta de cierre de PHP a propósito: evita enviar espacios por error
