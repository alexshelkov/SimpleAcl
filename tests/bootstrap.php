<?php
// Init composer autoloaders
require_once __DIR__ . '/../vendor/autoload.php';

$version = explode('.', phpversion());
$version = $version[0];

if ($version >= 8) {
    require_once __DIR__ . '/bootstrap8.php';
} else {
    require_once __DIR__ . '/bootstrap5.php';
}
