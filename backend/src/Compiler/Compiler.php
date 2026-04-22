<?php

use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\CommonTokenStream;
use Visitor\FunctionCollector;
use Error\ErrorReporter;
use Error\SyntaxErrorListener;
use Semantic\SymbolTable;
use Visitor\VariableCollector;
use Visitor\AssignmentChecker;
use Visitor\TypeChecker;
use Visitor\ConstantCollector;
use Visitor\Interpreter;
use Visitor\OffsetCalculator;
use Visitor\CodeGenerator;

class Compiler
{
    public function compile(string $input): array
    {
        $errorReporter = new ErrorReporter();
        $symbolTable   = new SymbolTable();
        $typeChecker   = new TypeChecker($symbolTable, $errorReporter);

        $inputStream = InputStream::fromString($input);
        $lexer       = new GrammarLexer($inputStream);
        $tokenStream = new CommonTokenStream($lexer);
        $parser      = new GrammarParser($tokenStream);

        $parser->removeErrorListeners();
        $lexer->removeErrorListeners();

        $parser->addErrorListener(new SyntaxErrorListener($errorReporter));
        $lexer->addErrorListener(new SyntaxErrorListener($errorReporter));

        $tree = $parser->program();

        // ── Pasada 1: registrar funciones (hoisting) ──────────────────────────
        $functionCollector = new FunctionCollector($symbolTable, $errorReporter);
        $functionCollector->visit($tree);

        // ── Pasada 2a: registrar constantes ───────────────────────────────────
        $constantCollector = new ConstantCollector($symbolTable, $errorReporter);
        $constantCollector->visit($tree);

        // ── Pasada 2b: registrar variables e inferir tipos ────────────────────
        $variableCollector = new VariableCollector($symbolTable, $errorReporter, $typeChecker);
        $variableCollector->visit($tree);

        // ── Pasada 3: validar asignaciones ────────────────────────────────────
        $assignmentChecker = new AssignmentChecker($symbolTable, $errorReporter);
        $assignmentChecker->visit($tree);

        // ── Pasada 4: verificar tipos ─────────────────────────────────────────
        $typeChecker = new TypeChecker($symbolTable, $errorReporter);
        $typeChecker->setFullPass(true);
        $typeChecker->visit($tree);

        // ── Verificar existencia de main ──────────────────────────────────────
        if (!$functionCollector->hasMainFunction()) {
            $errorReporter->add(new \Error\CompilerError(
                "Error Semántico",
                "La función 'main' no está definida.",
                0, 0
            ));
        }

        // ── Serializar errores ────────────────────────────────────────────────
        $errors = [];
        foreach ($errorReporter->getErrors() as $error) {
            $errors[] = [
                'tipo'        => $error->type,
                'descripcion' => $error->message,
                'linea'       => $error->line,
                'columna'     => $error->column,
            ];
        }

        if ($errorReporter->hasErrors()) {
            return [
                'output'  => '',
                'arm64'   => '', 
                'execution_output' => '',
                'errors'  => $errors,
                'symbols' => $this->buildSymbolTable($symbolTable),
            ];
        }

        // ── Pasada 5: calcular offsets para variables locales ─────────────────
        $offsetCalculator = new OffsetCalculator($symbolTable);
        $offsetCalculator->visit($tree);

        // ── Pasada 6: generar código ensamblador ───────────────────────────────
        $codeGenerator = new CodeGenerator($symbolTable);
        $codeGenerator->visit($tree);
        $codeGenerator->emitRuntimeHelpers();
        $arm64Code = $codeGenerator->getAssembly();

        // ── Pasada 7: ejecutar código ARM64 ────────────────────────────────────
        $execResult = $this->executeArm64($arm64Code);

        // Leer valores finales del Environment (runtime)
        $interpreter = new Interpreter($symbolTable);
        $interpreter->run($tree);
        $globalValues = $interpreter->getEnv()->getGlobalValues();
        foreach ($globalValues as $name => $value) {
            $symbolTable->setValue($name, $value);
        }

        return [
            'output' => $execResult['output'],
            'arm64'  => $arm64Code,
            'execution_output' => $execResult['output'],
            'errors' => [],
            'symbols' => $this->buildSymbolTable($symbolTable),
        ];

        // ── Ejecutar ──────────────────────────────────────────────────────────
        $interpreter = new Interpreter($symbolTable);
        $interpreter->run($tree);

        // Leer valores finales del Environment (runtime) y volcarlos al SymbolTable
        $globalValues = $interpreter->getEnv()->getGlobalValues();
        foreach ($globalValues as $name => $value) {
            $symbolTable->setValue($name, $value);
        }

        return [
            'output'  => $interpreter->getOutput(),
            'errors'  => [],
            'symbols' => $this->buildSymbolTable($symbolTable),
        ];
    }

    private function buildSymbolTable(SymbolTable $symbolTable): array
    {
        $rows = [];

        foreach ($symbolTable->getVariables() as $name => $info) {
            $val = $info['value'] ?? null;
            if (is_array($val))      $val = '[' . implode(', ', $val) . ']';
            elseif (is_bool($val))   $val = $val ? 'true' : 'false';
            elseif ($val === null)   $val = 'nil';
            $rows[] = [
                'name'  => $name,
                'kind'  => $info['kind']  ?? 'var',
                'type'  => $info['type']  ?? 'unknown',
                'scope' => $info['scope'] ?? 'global',
                'value' => (string)$val,
                'line'  => $info['line']  ?? 0,
            ];
        }

        foreach ($symbolTable->getFunctions() as $name => $info) {
            $paramStr = implode(', ', array_map(
                fn($p) => "{$p['name']}: {$p['type']}",
                $info['params'] ?? []
            ));
            $rows[] = [
                'name'  => $name,
                'kind'  => 'function',
                'type'  => $info['returnType'] ?? 'void',
                'scope' => 'global',
                'value' => '(' . $paramStr . ')',
                'line'  => 0,
            ];
        }

        return $rows;
    }
}