<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$code = $body['code'] ?? '';

if (trim($code) === '') {
    echo json_encode([
        'output'  => '',
        'errors'  => [],
        'symbols' => [],
        'success' => false,
        'message' => 'No se recibió código fuente.',
    ]);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Error/CompilerError.php';
require_once __DIR__ . '/../src/Error/ErrorReporter.php';
require_once __DIR__ . '/../src/Error/SyntaxErrorListener.php';
require_once __DIR__ . '/../src/Semantic/SymbolTable.php';
require_once __DIR__ . '/../src/Semantic/Environment.php';
require_once __DIR__ . '/../src/Visitor/FunctionCollector.php';
require_once __DIR__ . '/../src/Visitor/ConstantCollector.php';
require_once __DIR__ . '/../src/Visitor/VariableCollector.php';
require_once __DIR__ . '/../src/Visitor/AssignmentChecker.php';
require_once __DIR__ . '/../src/Visitor/TypeChecker.php';
require_once __DIR__ . '/../src/Visitor/Interpreter.php';
require_once __DIR__ . '/../src/Compiler/Compiler.php';

try {
    $compiler = new Compiler();
    $result   = $compiler->compile($code);

    echo json_encode([
        'output'  => $result['output']  ?? '',
        'errors'  => $result['errors']  ?? [],
        'symbols' => $result['symbols'] ?? [],
        'success' => empty($result['errors']),
        'message' => empty($result['errors']) ? 'Compilación exitosa' : 'Compilación con errores',
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'output'  => '',
        'errors'  => [[
            'tipo'        => 'Error Interno',
            'descripcion' => $e->getMessage(),
            'linea'       => $e->getLine(),
            'columna'     => 0,
        ]],
        'symbols' => [],
        'success' => false,
        'message' => 'Error interno del servidor',
    ], JSON_UNESCAPED_UNICODE);
}