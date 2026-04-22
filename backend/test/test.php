<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Compiler/Compiler.php';

$compiler = new Compiler();

$input = file_get_contents(__DIR__ . '/test.txt');

if ($compiler->compile($input)) {
    echo "Compilación exitosa\n";
} else {
    echo "Error de compilación\n";
}