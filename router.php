<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si el archivo solicitado existe en public/, servirlo directamente
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false; // deja que el servidor PHP sirva el archivo estatico
}

// De lo contrario, pasa todo a Laravel
require_once __DIR__ . '/public/index.php';
