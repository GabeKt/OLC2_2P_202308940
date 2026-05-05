<?php
namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;


class CodeGenerator extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private array  $lines       = [];   // líneas de código ensamblador
    private array  $dataSection = [];   // .data strings
    private int    $labelCount  = 0;    // contador de etiquetas únicas
    private string $currentFunc = '';   // función actual
    private int    $strLitCount = 0;    // contador de string literals
    private array  $stringLiterals = []; // map 'texto' => label
    private array $floatVars = [];

    private bool $lastResultWasFloat = false;

    // Para loops: pila de etiquetas (break/continue)
    private array $loopBreakStack    = [];
    private array $loopContinueStack = [];

    public function __construct(SymbolTable $symbolTable) {
        $this->symbolTable = $symbolTable;
    }

    // ── Helpers de emisión ────────────────────────────────────────────────────

    private function emit(string $line): void {
        $this->lines[] = $line;
    }

    private function emitLabel(string $label): void {
        $this->lines[] = "{$label}:";
    }

    private function newLabel(string $prefix = 'L'): string {
        return $prefix . '_' . (++$this->labelCount);
    }

    private function comment(string $text): void {
        $this->lines[] = "    # $text";
    }

    private function instr(string $op, string ...$args): void {
        $operands = implode(', ', $args);
        $this->lines[] = "    {$op} {$operands}";
    }

    // ── Resultado final ───────────────────────────────────────────────────────

    public function getAssembly(): string {
        $out = [];

        // Sección de datos
        if (!empty($this->dataSection)) {
            $out[] = '.section .data';
            foreach ($this->dataSection as $line) {
                $out[] = $line;
            }
            $out[] = '';
        }

        // Sección de texto
        $out[] = '.section .text';
        $out[] = '.align 2';

        // Declarar todas las funciones como globales
        foreach ($this->symbolTable->getFunctions() as $name => $_) {
            $out[] = ".global {$name}";
        }
        $out[] = '';

        foreach ($this->lines as $line) {
            $out[] = $line;
        }

        return implode("\n", $out) . "\n";
    }

    // ── Programa ──────────────────────────────────────────────────────────────

    public function visitProgram($ctx): void {
        // Emitir _start que llama a main y hace exit
        $this->emit('.global _start');
        $this->emit('_start:');
        $this->instr('bl', 'main');
        $this->loadImmediate('x0', 0);
        $this->instr('mov', 'x8', '#93');   // syscall exit
        $this->instr('svc', '#0');
        $this->emit('');

        $this->visitChildren($ctx);
    }

    // ── Funciones ─────────────────────────────────────────────────────────────

    public function visitFuncDecl($ctx): void {
        $funcName  = $ctx->ID()->getText();
        $frameSize = $this->symbolTable->getFunctionFrameSize($funcName);
        $frameSize = ($frameSize + 15) & ~15;

        $this->comment("DEBUG frameSize=$funcName: $frameSize");
        $params = $this->symbolTable->getFunction($funcName)['paramOffsets'] ?? [];
        foreach ($params as $pname => $poff) {
            $this->comment("DEBUG param $pname offset=$poff");
        }

        $this->currentFunc = $funcName;

        //debug
        $params = $this->symbolTable->getFunction($funcName)['paramOffsets'] ?? [];
        $vars   = $this->symbolTable->getVariables();
        $this->comment("DEBUG $funcName frameSize=$frameSize");
        foreach ($params as $pn => $po) {
            $this->comment("  param $pn = offset $po");
        }

        $allVars = $this->symbolTable->getVariables();
        foreach ($allVars as $vname => $vinfo) {
            if (($vinfo['scope'] ?? '') !== 'global') {
                $this->comment("  var $vname = offset {$vinfo['offset']} scope={$vinfo['scope']}");
            }
        }

        $this->comment("=== función $funcName ===");
        $this->emitLabel($funcName);

        // Prólogo
        $this->instr('sub', 'sp', 'sp', "#$frameSize");
        $this->instr('stp', 'x29, x30', "[sp, #0]");
        $this->instr('mov', 'x29', 'sp');

        // Preservar callee-saved PRIMERO (antes de tocar params)
        $this->comment("preservar x19, x20, x21");
        $this->instr('stp', 'x19, x20', "[sp, #16]");
        $this->instr('str', 'x21',      "[sp, #32]");

        // Guardar parámetros DESPUÉS (sus offsets ≥ 40, no pisan lo anterior)
        if ($ctx->paramList() !== null) {
            $regIdx  = 0;
            $fregIdx = 0;
            foreach ($ctx->paramList()->paramGroup() as $group) {
                $typeText = $group->typeLiteral() ? $group->typeLiteral()->getText() : 'int32';
                $isFloat  = ($typeText === 'float32');

                foreach ($group->ID() as $idToken) {
                    $name   = $idToken->getText();
                    $offset = $this->symbolTable->getParamOffset($funcName, $name);
                    if ($offset < 0) {
                        if ($isFloat) $fregIdx++; else $regIdx++;
                        continue;
                    }
                    if ($isFloat) {
                        $this->instr('str', "s{$fregIdx}", "[sp, #{$offset}]");
                        $fregIdx++;
                    } else {
                        $this->instr('str', "x{$regIdx}", "[sp, #{$offset}]");
                        $regIdx++;
                    }
                }
            }
        }

        // Cuerpo
        $this->visit($ctx->block());

        // Epílogo
        $this->emitLabel("{$funcName}_ret");
        $this->instr('ldp', 'x19, x20', "[sp, #16]");
        $this->instr('ldr', 'x21',      "[sp, #32]");
        $this->instr('ldp', 'x29, x30', "[sp, #0]");
        $this->instr('add', 'sp', 'sp', "#$frameSize");
        $this->instr('ret');
        $this->emit('');

        $this->currentFunc = '';
    }

    // ── Bloque ────────────────────────────────────────────────────────────────

    public function visitBlock($ctx): void {
        $this->visitChildren($ctx);
    }

    // ── Declaraciones ─────────────────────────────────────────────────────────

    public function visitVarDecl($ctx): void {
        $ids = $ctx->idList() !== null ? $ctx->idList()->ID() : [];

        if ($ctx->expressionList() !== null) {
            $exprs = $ctx->expressionList()->expression();
            foreach ($ids as $i => $idToken) {
                $name   = $idToken->getText();
                $offset = $this->resolveOffset($name);
                if ($offset < 0) continue;

                $expr = $exprs[$i] ?? null;
                if ($expr === null) continue;

                if ($this->isArrayLiteralExpr($expr)) {
                    $this->emitArrayLiteralInit($name, $offset, $expr);
                    continue;
                }

                // Detectar llamada a función que retorna array
                $exprText = $expr->getText();
                if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\(/', $exprText, $m)) {
                    $calledFunc = $m[1];
                    $funcInfo   = $this->symbolTable->getFunction($calledFunc);
                    $retType    = $funcInfo['returnType'] ?? 'int32';
                    if (preg_match('/^\[\d+\]/', $retType)) {
                        $this->comment("llamada con retorno array: x8 = destino offset $offset");
                        $this->instr('add', 'x8', 'x29', "#$offset");
                        $this->visitExprIntoX0($expr);
                        continue;
                    }
                }

                $this->visitExprIntoX0($expr);

                // *** Capturar flag INMEDIATAMENTE ***
                $resultWasFloat = $this->lastResultWasFloat;

                $registeredType = $this->symbolTable->lookup($name)['type'] ?? 'int32';
                $isFloat = ($registeredType === 'float32') || $resultWasFloat || $this->isFloatVar($name);

                if ($isFloat) {
                    $this->floatVars[$this->currentFunc][$name] = true;
                    $this->instr('str', 's0', "[x29, #{$offset}]");
                } else {
                    $this->instr('str', 'x0', "[x29, #{$offset}]");
                }
            }
        } else {
            // Sin inicialización — valor por defecto según tipo
            foreach ($ids as $idToken) {
                $name   = $idToken->getText();
                $offset = $this->resolveOffset($name);
                if ($offset < 0) continue;

                $type = $this->symbolTable->lookup($name)['type'] ?? 'int32';

                if (preg_match('/^\[(\d+)\](.+)$/', $type, $m)) {
                    $totalSize = $this->symbolTable->typeSize($type);
                    $this->loadImmediate('x0', 0);
                    for ($b = 0; $b < $totalSize; $b += 8) {
                        $this->instr('str', 'x0', "[x29, #" . ($offset + $b) . "]");
                    }
                } elseif ($type === 'string') {
                    $label = $this->getStringLabel('');
                    $this->instr('adrp', 'x0', $label);
                    $this->instr('add',  'x0', 'x0', ":lo12:{$label}");
                    $this->instr('str', 'x0', "[x29, #{$offset}]");
                } elseif ($type === 'float32') {
                    // Inicializar float a 0.0
                    $this->floatVars[$this->currentFunc][$name] = true;
                    $this->instr('ldr', 'w0', '=0');
                    $this->instr('fmov', 's0', 'w0');
                    $this->instr('str', 's0', "[x29, #{$offset}]");
                } else {
                    $this->loadImmediate('x0', 0);
                    $this->instr('str', 'x0', "[x29, #{$offset}]");
                }
            }
        }
    }

    private function isArrayLiteralExpr($exprCtx): bool {
        // Navegar hasta literal -> arrayLiteral
        $text = $exprCtx->getText();
        return preg_match('/^\[\d+\]/', $text) === 1;
    }

    private function emitArrayLiteralInit(string $name, int $baseOffset, $exprCtx): void {
        $arrayLit = $this->findArrayLiteral($exprCtx);
        if ($arrayLit === null) return;

        // Determinar tipo del array desde la tabla de símbolos
        $info = $this->symbolTable->lookup($name);
        $arrayType = $info['type'] ?? 'int32';

        $this->emitArrayLiteralElements($baseOffset, $arrayType, $arrayLit->arrayElements());

        // Dejar x0 = dirección base del array
        $this->instr('add', 'x0', 'x29', "#$baseOffset");
    }


    private function emitArrayLiteralElements(int $baseOffset, string $arrayType, $elementsCtx): void {
        // Parsear [N]innerType
        if (!preg_match('/^\[(\d+)\](.+)$/', $arrayType, $m)) return;
        $innerType = $m[2]; // puede ser int32, float32, string, [2]int32, etc.

   
        $elemSize = $this->stackStrideFor($innerType);

        $elements = $elementsCtx->arrayElement();
        foreach ($elements as $idx => $elem) {
            $elemOffset = $baseOffset + $idx * $elemSize;

            if ($elem->arrayElements() !== null) {
                // Elemento es una fila de array multidimensional: { ... }
                $this->emitArrayLiteralElements($elemOffset, $innerType, $elem->arrayElements());
            } else {
                // Elemento es una expresión escalar
                $this->visitExprIntoX0($elem->expression());
                $this->emitStoreByType($innerType, $elemOffset);
            }
        }
    }

    /** Stride real en stack para un tipo (cuántos bytes ocupa cada elemento en el array). */
    private function stackStrideFor(string $type): int {
        if (preg_match('/^\[\d+\]/', $type)) {
            // Sub-array: usar typeSize completo
            return $this->symbolTable->typeSize($type);
        }
        return match($type) {
            'int32', 'bool', 'rune', 'int' => 4,
            'float32'                       => 4,
            'float64'                       => 8,
            'string'                        => 8,  // guardamos solo el puntero
            default                         => 8,
        };
    }

    /** Emite la instrucción store correcta según el tipo, en [x29, #offset]. */
    private function emitStoreByType(string $type, int $offset): void {
        if ($type === 'float32') {
            $this->instr('str', 's0', "[x29, #{$offset}]");
        } elseif ($type === 'string') {
            // string se guarda como puntero de 8 bytes
            $this->instr('str', 'x0', "[x29, #{$offset}]");
        } elseif ($type === 'int32' || $type === 'bool' || $type === 'rune' || $type === 'int') {
            // 4 bytes
            $this->instr('str', 'w0', "[x29, #{$offset}]");
        } else {
            // por defecto 8 bytes (puntero, etc.)
            $this->instr('str', 'x0', "[x29, #{$offset}]");
        }
    }


    private function findArrayLiteral($ctx) {
        if (method_exists($ctx, 'arrayLiteral') && $ctx->arrayLiteral() !== null) {
            return $ctx->arrayLiteral();
        }
        // Bajar por la jerarquía de expresión
        foreach (['logicalOr','logicalAnd','equality','comparison','addition',
                'multiplication','unary','primaryExpr','operand','literal'] as $method) {
            if (method_exists($ctx, $method)) {
                $child = $ctx->$method();
                if (is_array($child)) $child = $child[0] ?? null;
                if ($child !== null) {
                    $result = $this->findArrayLiteral($child);
                    if ($result !== null) return $result;
                }
            }
        }
        return null;
    }

public function visitShortVarDecl($ctx): void {
    $ids   = $ctx->idList()->ID();
    $exprs = $ctx->expressionList()->expression();

    // Caso especial: una sola expresión que es llamada a función con múltiple retorno escalar
    if (count($exprs) === 1 && count($ids) > 1) {
        $expr = $exprs[0];
        $this->visitExprIntoX0($expr);
        foreach ($ids as $i => $idToken) {
            $name   = $idToken->getText();
            $offset = $this->resolveOffset($name);
            if ($offset < 0) continue;
            if ($i === 0) {
                $this->instr('str', 'x0', "[x29, #{$offset}]");
            } else {
                $this->instr('str', "x{$i}", "[x29, #{$offset}]");
            }
        }
        return;
    }

    // Caso normal
    foreach ($ids as $i => $idToken) {
        $name   = $idToken->getText();
        $offset = $this->resolveOffset($name);
        if ($offset < 0) continue;

        $expr = $exprs[$i] ?? null;
        if ($expr === null) continue;

        if ($this->isArrayLiteralExpr($expr)) {
            $this->emitArrayLiteralInit($name, $offset, $expr);
            continue;
        }

        // Detectar llamada a función que retorna array
        $exprText = $expr->getText();
        if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\(/', $exprText, $m)) {
            $calledFunc = $m[1];
            $funcInfo   = $this->symbolTable->getFunction($calledFunc);
            $retType    = $funcInfo['returnType'] ?? 'int32';
            if (preg_match('/^\[\d+\]/', $retType)) {
                $this->comment("llamada con retorno array: x8 = destino offset $offset");
                $this->instr('add', 'x8', 'x29', "#$offset");
                $this->visitExprIntoX0($expr);
                continue;
            }
        }

        $this->visitExprIntoX0($expr);

        // *** Capturar flag INMEDIATAMENTE después de evaluar ***
        $resultWasFloat = $this->lastResultWasFloat;

        // Determinar tipo registrado
        $registeredType = $this->symbolTable->lookup($name)['type'] ?? 'int32';
        $isFloat = ($registeredType === 'float32') || $resultWasFloat || $this->isFloatVar($name);

        if ($isFloat) {
            $this->floatVars[$this->currentFunc][$name] = true;
            $this->instr('str', 's0', "[x29, #{$offset}]");
        } else {
            $this->instr('str', 'x0', "[x29, #{$offset}]");
        }
    }
}

    public function visitConstDecl($ctx): void {
        $name   = $ctx->ID()->getText();
        $offset = $this->resolveOffset($name);
        if ($offset < 0) return;

        $this->visitExprIntoX0($ctx->expression());
        $this->instr('str', 'x0', "[x29, #{$offset}]");
    }

    // ── Asignaciones ──────────────────────────────────────────────────────────

    public function visitAssignStmt($ctx): void {
        $lValues = $ctx->lValueList()->lValue();
        $op      = $ctx->assignOp()->getText();
        $exprs   = $ctx->expressionList()->expression();

        foreach ($lValues as $i => $lv) {
            $expr = $exprs[$i] ?? null;
            if ($expr === null) continue;

            $this->visitExprIntoX0($expr);

            // *** Capturar flag INMEDIATAMENTE después de evaluar ***
            $resultWasFloat = $this->lastResultWasFloat;

            // Asignación a través de puntero: *p = valor
            if ($lv->STAR() !== null) {
                $name   = $lv->ID()->getText();
                $offset = $this->resolveOffset($name);
                $this->instr('mov', 'x9', 'x0');
                $this->instr('ldr', 'x10', "[x29, #{$offset}]");
                $this->instr('str', 'x9', '[x10]');
                continue;
            }

            $name   = $lv->ID()->getText();
            $offset = $this->resolveOffset($name);
            if ($offset < 0) continue;

            if ($op !== '=') {
                if ($this->isFloatVar($name) || $resultWasFloat) {
                    // Operaciones compuestas float: RHS ya está en s0
                    $this->instr('sub', 'sp', 'sp', '#16');
                    $this->instr('str', 's0', '[sp, #0]');          // salvar RHS
                    $this->instr('ldr', 's0', "[x29, #{$offset}]"); // cargar LHS
                    $this->instr('ldr', 's1', '[sp, #0]');          // recuperar RHS
                    $this->instr('add', 'sp', 'sp', '#16');
                    switch ($op) {
                        case '+=': $this->instr('fadd', 's0', 's0', 's1'); break;
                        case '-=': $this->instr('fsub', 's0', 's0', 's1'); break;
                        case '*=': $this->instr('fmul', 's0', 's0', 's1'); break;
                        case '/=': $this->instr('fdiv', 's0', 's0', 's1'); break;
                    }
                    $resultWasFloat = true;
                    $this->lastResultWasFloat = true;
                } else {
                    $this->instr('ldr', 'x1', "[x29, #{$offset}]");
                    switch ($op) {
                        case '+=': $this->instr('add',  'x0', 'x1', 'x0'); break;
                        case '-=': $this->instr('sub',  'x0', 'x1', 'x0'); break;
                        case '*=': $this->instr('mul',  'x0', 'x1', 'x0'); break;
                        case '/=': $this->instr('sdiv', 'x0', 'x1', 'x0'); break;
                    }
                }
            }

            $indices = $lv->expression();
            if ($indices !== null && count($indices) > 0) {

                // Salvar valor ANTES de emitArrayAddress (puede destruir x0/s0)
                if ($resultWasFloat) {
                    $this->instr('fmov', 's9', 's0');
                } else {
                    $this->instr('mov', 'x9', 'x0');
                }

                $this->emitArrayAddress($name, $indices);

                // Resolver tipo del array: parámetros tienen prioridad
                $arrType = '';
                if ($this->currentFunc !== '') {
                    $paramOffset = $this->symbolTable->getParamOffset($this->currentFunc, $name);
                    if ($paramOffset >= 0) {
                        $func = $this->symbolTable->getFunction($this->currentFunc);
                        foreach ($func['params'] ?? [] as $p) {
                            if (($p['name'] ?? null) === $name) {
                                $arrType = $p['type'] ?? '';
                                break;
                            }
                        }
                    }
                }
                if ($arrType === '') {
                    $arrInfo = $this->symbolTable->lookup($name);
                    $arrType = $arrInfo['type'] ?? 'int32';
                }

                // Bajar tantos niveles como índices para obtener el tipo del elemento
                $depth    = count($indices);
                $elemType = $arrType;
                for ($d = 0; $d < $depth; $d++) {
                    $elemType = $this->getElementType($elemType);
                }

                if ($elemType === 'float32' || $resultWasFloat) {
                    $this->instr('str', 's9', '[x10]');
                } elseif ($elemType === 'int32' || $elemType === 'bool' || $elemType === 'rune' || $elemType === 'int') {
                    $this->instr('str', 'w9', '[x10]');
                } else {
                    $this->instr('str', 'x9', '[x10]');
                }

            } else {
                // Store simple (sin índices)
                // Para variables ya conocidas como float, usar isFloatVar()
                // Para variables nuevas, confiar en lastResultWasFloat
                $isFloat = $this->isFloatVar($name) || $resultWasFloat;

                if ($resultWasFloat) {
                    $this->floatVars[$this->currentFunc][$name] = true;
                }

                if ($isFloat) {
                    $this->instr('str', 's0', "[x29, #{$offset}]");
                } else {
                    $this->instr('str', 'x0', "[x29, #{$offset}]");
                }
            }
        }
    }
    public function visitIncDecStmt($ctx): void {
        $lv     = $ctx->lValue();
        $name   = $lv->ID()->getText();
        $offset = $this->resolveOffset($name);
        if ($offset < 0) return;

        $this->instr('ldr', 'x0', "[x29, #{$offset}]");
        $isInc = $ctx->INC() !== null;
        $this->instr($isInc ? 'add' : 'sub', 'x0', 'x0', '#1');
        $this->instr('str', 'x0', "[x29, #{$offset}]");
    }

    // ── Control de flujo ──────────────────────────────────────────────────────

    public function visitIfStmt($ctx): void {
        $labelElse = $this->newLabel('else');
        $labelEnd  = $this->newLabel('endif');

        $this->visitExprIntoX0($ctx->expression());
        $this->instr('cmp', 'x0', '#0');
        $this->instr('b.eq', $labelElse);

        // Bloque then
        $this->visit($ctx->block(0));
        $this->instr('b', $labelEnd);

        // Bloque else
        $this->emitLabel($labelElse);
        if ($ctx->block(1) !== null) {
            $this->visit($ctx->block(1));
        } elseif ($ctx->ifStmt() !== null) {
            $this->visit($ctx->ifStmt());
        }

        $this->emitLabel($labelEnd);
    }

    public function visitForStmt($ctx): void {
        $header = $ctx->forHeader();

        $labelStart    = $this->newLabel('for_start');
        $labelCond     = $this->newLabel('for_cond');
        $labelPost     = $this->newLabel('for_post');
        $labelEnd      = $this->newLabel('for_end');

        // Pila para break/continue
        $this->loopBreakStack[]    = $labelEnd;
        $this->loopContinueStack[] = $labelPost;

        if ($header === null) {
            // for { } — bucle infinito
            $this->emitLabel($labelStart);
            $this->visit($ctx->block());
            $this->instr('b', $labelStart);
            $this->emitLabel($labelEnd);

        } elseif ($header->forInitialization() === null) {
            // for condición (do-while según spec)
            $this->emitLabel($labelStart);
            $this->visit($ctx->block());

            $this->emitLabel($labelPost);
            $this->visitExprIntoX0($header->expression());
            $this->instr('cmp', 'x0', '#0');
            $this->instr('b.ne', $labelStart);
            $this->emitLabel($labelEnd);

        } else {
            // for init; cond; post
            $this->visit($header->forInitialization());

            $this->emitLabel($labelCond);
            if ($header->expression() !== null) {
                $this->visitExprIntoX0($header->expression());
                $this->instr('cmp', 'x0', '#0');
                $this->instr('b.eq', $labelEnd);
            }

            $this->emitLabel($labelStart);
            $this->visit($ctx->block());

            $this->emitLabel($labelPost);
            $this->visit($header->forPost());
            $this->instr('b', $labelCond);
            $this->emitLabel($labelEnd);
        }

        array_pop($this->loopBreakStack);
        array_pop($this->loopContinueStack);
    }

    public function visitSwitchStatement($ctx): void {
        $labelEnd = $this->newLabel('switch_end');
        $this->loopBreakStack[] = $labelEnd;

        $this->visitExprIntoX0($ctx->expression());
        $this->instr('mov', 'x19', 'x0'); // guardar en registro preservado

        $cases     = $ctx->switchCase();
        $caseLabels = [];

        foreach ($cases as $i => $case) {
            $caseLabels[$i] = $this->newLabel('case');
        }
        $defaultLabel = $this->newLabel('default');

        // Comparar y saltar
        foreach ($cases as $i => $case) {
            foreach ($case->expressionList()->expression() as $expr) {
                $this->visitExprIntoX0($expr);
                $this->instr('cmp', 'x19', 'x0');
                $this->instr('b.eq', $caseLabels[$i]);
            }
        }
        $this->instr('b', $defaultLabel);

        // Cuerpos de casos
        foreach ($cases as $i => $case) {
            $this->emitLabel($caseLabels[$i]);
            foreach ($case->statement() as $stmt) {
                $this->visit($stmt);
            }
            $this->instr('b', $labelEnd);
        }

        // Default
        $this->emitLabel($defaultLabel);
        if ($ctx->defaultCase() !== null) {
            foreach ($ctx->defaultCase()->statement() as $stmt) {
                $this->visit($stmt);
            }
        }

        $this->emitLabel($labelEnd);
        array_pop($this->loopBreakStack);
    }

    public function visitReturnStatement($ctx): void {
        if ($ctx->expressionList() !== null) {
            $exprs = $ctx->expressionList()->expression();
            if (count($exprs) === 1) {
                $expr = $exprs[0];
                // Verificar si el tipo de retorno de la función actual es un array
                $funcInfo  = $this->symbolTable->getFunction($this->currentFunc);
                $retType   = $funcInfo['returnType'] ?? 'int32';
                $isArrRet  = (bool)preg_match('/^\[\d+\]/', $retType);

                if ($isArrRet) {
                    // Evaluar la expresión (deja dirección en x0)
                    $this->visitExprIntoX0($expr);
                    // x0 = dirección fuente, x8 = dirección destino (pasada por caller)
                    $totalSize = $this->symbolTable->typeSize($retType);
                    $this->comment("copiar array retorno: $totalSize bytes de x0 a x8");
                    $this->instr('mov', 'x9',  'x0');   // fuente
                    $this->instr('mov', 'x10', 'x8');   // destino
                    $bytes = 0;
                    while ($bytes < $totalSize) {
                        $remaining = $totalSize - $bytes;
                        if ($remaining >= 8) {
                            $this->instr('ldr', 'x11', "[x9, #$bytes]");
                            $this->instr('str', 'x11', "[x10, #$bytes]");
                            $bytes += 8;
                        } elseif ($remaining >= 4) {
                            $this->instr('ldr', 'w11', "[x9, #$bytes]");
                            $this->instr('str', 'w11', "[x10, #$bytes]");
                            $bytes += 4;
                        } else {
                            $this->instr('ldrb', 'w11', "[x9, #$bytes]");
                            $this->instr('strb', 'w11', "[x10, #$bytes]");
                            $bytes += 1;
                        }
                    }
                    // x0 = destino (convención: retornar el puntero destino también)
                    $this->instr('mov', 'x0', 'x8');
                } else {
                    $this->visitExprIntoX0($expr);
                }
            } else {
                // Múltiples valores de retorno
                $staging = ['x9','x10','x11','x12'];
                foreach ($exprs as $i => $expr) {
                    $this->visitExprIntoX0($expr);
                    $this->instr('mov', $staging[$i], 'x0');
                }
                foreach ($exprs as $i => $_) {
                    $this->instr('mov', "x{$i}", $staging[$i]);
                }
            }
        }
        $this->instr('b', "{$this->currentFunc}_ret");
    }

    public function visitBreakStmt($ctx): void {
        if (!empty($this->loopBreakStack)) {
            $this->instr('b', end($this->loopBreakStack));
        }
    }

    public function visitContinueStmt($ctx): void {
        if (!empty($this->loopContinueStack)) {
            $this->instr('b', end($this->loopContinueStack));
        }
    }

    // ── Llamadas a función ────────────────────────────────────────────────────

    public function visitFunctionCall($ctx): void {
        $name = $ctx->ID()->getText();
        $args = ($ctx->argumentList() !== null) ? $ctx->argumentList()->argument() : [];
        $argCount = count($args);

        $funcInfo   = $this->symbolTable->getFunction($name);
        $paramTypes = [];
        if ($funcInfo) {
            foreach ($funcInfo['params'] ?? [] as $p) {
                $paramTypes[] = $p['type'];
            }
        }

        $stagingRegsInt   = ['x9','x10','x11','x12','x13','x14','x15','x16'];
        $stagingRegsFloat = ['s8','s9','s10','s11','s12','s13','s14','s15'];

        // Una sola pasada: evaluar y guardar en staging, recordar qué registro usó cada arg
        $assignments = []; // [['kind'=>'int'|'float', 'staging'=>'x9', 'dest'=>'x0'], ...]
        $intIdx   = 0;
        $floatIdx = 0;

        foreach ($args as $i => $arg) {
            $hasAmp = $arg->AMP() !== null;
            $this->comment("DEBUG arg $i hasAmp=$hasAmp text=" . $arg->getText());
        }

        foreach ($args as $i => $arg) {
            $paramType = $paramTypes[$i] ?? 'int32';
            $isFloat   = ($paramType === 'float32');
            $isPtr     = str_starts_with($paramType, '*');
            $hasAmp    = $arg->AMP() !== null;
            $exprText  = $arg->expression() !== null ? $arg->expression()->getText() : '';
            $exprHasAmp = str_starts_with($exprText, '&');

            if ($hasAmp) {
                $varName = $arg->ID()->getText();

                $localOffset = $this->symbolTable->getOffset($varName);
                $paramOffset = ($this->currentFunc !== '')
                    ? $this->symbolTable->getParamOffset($this->currentFunc, $varName)
                    : -1;

                if ($localOffset >= 0) {
                    $base = 'sp';
                    $offset = $localOffset;
                } elseif ($paramOffset >= 0) {
                    $base = 'x29';
                    $offset = $paramOffset;
                } else {
                    continue; // evita usar basura
                }

                $staging = $stagingRegsInt[$intIdx];
                $this->instr('add', $staging, $base, "#$offset");

                $assignments[] = ['kind' => 'int', 'staging' => $staging, 'dest' => "x{$intIdx}"];
                $intIdx++;
            } elseif ($exprHasAmp) {
                // &var dentro de la expresión (unary en el árbol)
                $expr = $arg->expression();
                if ($expr === null) continue;

                $text = $expr->getText();
                if ($text[0] !== '&') continue;

                $varName = substr($text, 1);
                $localOffset = $this->symbolTable->getOffset($varName);
                $paramOffset = ($this->currentFunc !== '')
                    ? $this->symbolTable->getParamOffset($this->currentFunc, $varName)
                    : -1;

                if ($localOffset >= 0) {
                    $base = 'sp';
                    $offset = $localOffset;
                } elseif ($paramOffset >= 0) {
                    $base = 'x29';
                    $offset = $paramOffset;
                } else {
                    // fallback seguro
                    continue;
                }


                $staging = $stagingRegsInt[$intIdx];

                $this->instr('add', $staging, $base, "#$offset");
                $assignments[] = ['kind' => 'int', 'staging' => $staging, 'dest' => "x{$intIdx}"];
                $intIdx++;

            } elseif ($isFloat) {
                $this->visitExprIntoX0($arg->expression());
                $staging = $stagingRegsFloat[$floatIdx];
                $this->instr('fmov', $staging, 's0');
                $assignments[] = ['kind' => 'float', 'staging' => $staging, 'dest' => "s{$floatIdx}"];
                $floatIdx++;

            } else {
                $this->visitExprIntoX0($arg->expression());
                $staging = $stagingRegsInt[$intIdx];
                $this->instr('mov', $staging, 'x0');
                $assignments[] = ['kind' => 'int', 'staging' => $staging, 'dest' => "x{$intIdx}"];
                $intIdx++;
            }
        }

        // Segunda pasada: mover de staging a registros ABI finales
        foreach ($assignments as $a) {
            if ($a['kind'] === 'float') {
                $this->instr('fmov', $a['dest'], $a['staging']);
            } else {
                $this->instr('mov', $a['dest'], $a['staging']);
            }
        }
        $retType = $this->symbolTable->getFunction($name)['returnType'] ?? 'int32';
        $this->lastResultWasFloat = ($retType === 'float32');
        $this->instr('bl', $name);
    
    }


    // ── fmt.Println ───────────────────────────────────────────────────────────

    public function visitFmtPrintln($ctx): void {
        if ($ctx->argumentList() === null) {
            // Println vacío — solo newline
            $this->emitPrintNewline();
            return;
        }

        $args = $ctx->argumentList()->argument();
        $first = true;

        foreach ($args as $arg) {
            if (!$first) $this->emitPrintSpace();
            $first = false;

            $expr = $arg->expression();
            $type = $this->getExprType($expr);

            if ($type === 'nil') {
                $label = $this->getStringLabel('<nil>');
                $this->instr('adrp', 'x0', $label);
                $this->instr('add',  'x0', 'x0', ":lo12:{$label}");
                $this->emitPrintString();
                continue;
            }
            $this->visitExprIntoX0($expr);

            switch ($type) {
                case 'int32':
                case 'rune':
                    $this->emitPrintInt();
                    break;
                case 'float32':
                    $this->emitPrintFloat();
                    break;
                case 'bool':
                    $this->emitPrintBool();
                    break;
                case 'string':
                    $this->emitPrintString();
                    break;
                case 'nil':
                    $label = $this->getStringLabel('<nil>');
                    $this->instr('adrp', 'x0', $label);
                    $this->instr('add',  'x0', 'x0', ":lo12:{$label}");
                    $this->emitPrintString();
                    break;
                default:
                    $this->emitPrintInt();
                    break;
            }
        }

        $this->emitPrintNewline();
    }

    // ── Helpers de print (syscall write) ─────────────────────────────────────

    private function emitPrintInt(): void {
        // x0 tiene el entero — convertir a string y escribir
        $this->comment("print int (x0)");
        // Usar función auxiliar __print_int que generamos al final
        $this->instr('bl', '__print_int');
    }

    private function emitPrintFloat(): void {
        $this->comment("print float (s0)");
        $this->instr('bl', '__print_float');
    }

    private function emitPrintBool(): void {
        $this->comment("print bool (x0)");
        $this->instr('bl', '__print_bool');
    }

    private function emitPrintString(): void {
        // x0 tiene la dirección del string
        $this->comment("print string (x0 = ptr)");
        // x0 ya es la dirección — calcular longitud con strlen auxiliar
        $this->instr('bl', '__print_str');
    }

    private function emitPrintSpace(): void {
        if (!isset($this->stringLiterals[" "])) {
            $this->dataSection[] = '__rt_sp: .asciz " "';
            $this->stringLiterals[" "] = '__rt_sp';
        }
        $this->instr('adrp', 'x1', '__rt_sp');
        $this->instr('add',  'x1', 'x1', ':lo12:__rt_sp');
        $this->instr('mov',  'x2', '#1');
        $this->instr('mov',  'x0', '#1');
        $this->instr('mov',  'x8', '#64');
        $this->instr('svc',  '#0');
    }

    private function emitPrintNewline(): void {
        if (!isset($this->stringLiterals["\n"])) {
            $this->dataSection[] = '__rt_nl: .asciz "\\n"';
            $this->stringLiterals["\n"] = '__rt_nl';
        }
        $this->instr('adrp', 'x1', '__rt_nl');
        $this->instr('add',  'x1', 'x1', ':lo12:__rt_nl');
        $this->instr('mov',  'x2', '#1');
        $this->instr('mov',  'x0', '#1');
        $this->instr('mov',  'x8', '#64');
        $this->instr('svc',  '#0');
    }

    // ── Strings en .data ─────────────────────────────────────────────────────

    private function getStringLabel(string $raw): string {
        if (isset($this->stringLiterals[$raw])) {
            return $this->stringLiterals[$raw];
        }
        $label = '__str_' . $this->strLitCount++;
        $escaped = $this->escapeForAsm($raw);
        $this->dataSection[] = "{$label}: .asciz \"{$escaped}\"";
        // .asciz includes null terminator — no need for _len
        $this->stringLiterals[$raw] = $label;
        return $label;
    }

    private function escapeForAsm(string $s): string {
        $s = str_replace('\\', '\\\\', $s);
        $s = str_replace('"', '\\"', $s);
        $s = str_replace("\n", '\\n', $s);
        $s = str_replace("\t", '\\t', $s);
        $s = str_replace("\r", '\\r', $s);
        return $s;
    }

    // ── Evaluación de expresiones → resultado en x0 (o s0 para float) ────────

    private function visitExprIntoX0($ctx): void {
        $this->visit($ctx);
        // El visitor de cada sub-expresión deja el resultado en x0/s0
    }

    public function visitExpression($ctx): void {
        $this->visit($ctx->logicalOr());
    }

    public function visitLogicalOr($ctx): void {
        $children = $ctx->logicalAnd();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        $labelTrue = $this->newLabel('or_true');
        $labelEnd  = $this->newLabel('or_end');

        for ($i = 1; $i < count($children); $i++) {
            $this->instr('cmp', 'x0', '#0');
            $this->instr('b.ne', $labelTrue);
            $this->visit($children[$i]);
        }
        $this->instr('cmp', 'x0', '#0');
        $this->instr('b.eq', $labelEnd);
        $this->emitLabel($labelTrue);
        $this->loadImmediate('x0', 1);
        $this->emitLabel($labelEnd);
    }

    public function visitLogicalAnd($ctx): void {
        $children = $ctx->equality();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        $labelFalse = $this->newLabel('and_false');
        $labelEnd   = $this->newLabel('and_end');

        for ($i = 1; $i < count($children); $i++) {
            $this->instr('cmp', 'x0', '#0');
            $this->instr('b.eq', $labelFalse);
            $this->visit($children[$i]);
        }
        $this->instr('cmp', 'x0', '#0');
        $this->instr('b.ne', $labelEnd);
        $this->emitLabel($labelFalse);
        $this->loadImmediate('x0', 0);
        $this->emitLabel($labelEnd);
    }

    public function visitEquality($ctx): void {
        $children = $ctx->comparison();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        for ($i = 1; $i < count($children); $i++) {
            $this->instr('mov', 'x19', 'x0');
            $this->visit($children[$i]);
            $op = $ctx->getChild($i * 2 - 1)->getText();
            $this->instr('cmp', 'x19', 'x0');
            $labelT = $this->newLabel('eq_t');
            $labelE = $this->newLabel('eq_e');
            $this->instr($op === '==' ? 'b.eq' : 'b.ne', $labelT);
            $this->loadImmediate('x0', 0);
            $this->instr('b', $labelE);
            $this->emitLabel($labelT);
            $this->loadImmediate('x0', 1);
            $this->emitLabel($labelE);
        }
    }

    public function visitComparison($ctx): void {
        $children = $ctx->addition();
        $this->visit($children[0]);  

 
        if ($ctx->IN() !== null) {
            $isNot  = ($ctx->NOT_KW() !== null);
            $exprs  = $ctx->expression();   
            $labelTrue  = $this->newLabel('range_t');
            $labelFalse = $this->newLabel('range_f');
            $labelEnd   = $this->newLabel('range_end');

 
            $this->instr('mov', 'x19', 'x0');

    
            $this->visitExprIntoX0($exprs[0]);
            $this->instr('cmp', 'x19', 'x0');
            $this->instr('b.lt', $labelFalse); 

  
            $this->visitExprIntoX0($exprs[1]);
            $this->instr('cmp', 'x19', 'x0');
            $this->instr('b.gt', $labelFalse);   

  
            $this->emitLabel($labelTrue);
            $this->loadImmediate('x0', $isNot ? 0 : 1);
            $this->instr('b', $labelEnd);

 
            $this->emitLabel($labelFalse);
            $this->loadImmediate('x0', $isNot ? 1 : 0);

            $this->emitLabel($labelEnd);
            $this->lastResultWasFloat = false;
            return;
        }

 
        if (count($children) === 1) return;

        for ($i = 1; $i < count($children); $i++) {
            $isFloat = $this->lastResultWasFloat;
            $op = $ctx->getChild($i * 2 - 1)->getText();
            $labelT = $this->newLabel('cmp_t');
            $labelE = $this->newLabel('cmp_e');

            if ($isFloat) {
                $this->instr('sub', 'sp', 'sp', '#16');
                $this->instr('str', 's0', '[sp, #0]');
                $this->visit($children[$i]);
                $this->instr('ldr', 's19', '[sp, #0]');
                $this->instr('add', 'sp', 'sp', '#16');
                $this->instr('fcmp', 's19', 's0');
                $branch = match($op) {
                    '<'  => 'b.lt',
                    '<=' => 'b.le',
                    '>'  => 'b.gt',
                    '>=' => 'b.ge',
                    default => 'b.eq',
                };
                $this->instr($branch, $labelT);
                $this->loadImmediate('x0', 0);
                $this->instr('b', $labelE);
                $this->emitLabel($labelT);
                $this->loadImmediate('x0', 1);
                $this->emitLabel($labelE);
                $this->lastResultWasFloat = false;
            } else {
                $this->instr('mov', 'x19', 'x0');
                $this->visit($children[$i]);
                $this->instr('cmp', 'x19', 'x0');
                $branch = match($op) {
                    '<'  => 'b.lt',
                    '<=' => 'b.le',
                    '>'  => 'b.gt',
                    '>=' => 'b.ge',
                    default => 'b.eq',
                };
                $this->instr($branch, $labelT);
                $this->loadImmediate('x0', 0);
                $this->instr('b', $labelE);
                $this->emitLabel($labelT);
                $this->loadImmediate('x0', 1);
                $this->emitLabel($labelE);
            }
        }
    }

    public function visitAddition($ctx): void {
        $children = $ctx->multiplication();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        $isFloat = $this->lastResultWasFloat;

        for ($i = 1; $i < count($children); $i++) {
            $op = $ctx->getChild($i * 2 - 1)->getText();
            if ($isFloat) {
                $this->instr('sub', 'sp', 'sp', '#16');
                $this->instr('str', 's0', '[sp, #0]');
                $this->visit($children[$i]);
                $this->instr('ldr', 's19', '[sp, #0]');
                $this->instr('add', 'sp', 'sp', '#16');
                $this->instr($op === '+' ? 'fadd' : 'fsub', 's0', 's19', 's0');
                $this->lastResultWasFloat = true;
            } else {
                $this->instr('sub', 'sp', 'sp', '#16');
                $this->instr('str', 'x0', '[sp, #0]');
                $this->visit($children[$i]);
                $this->instr('ldr', 'x19', '[sp, #0]');
                $this->instr('add', 'sp', 'sp', '#16');
                $this->instr($op === '+' ? 'add' : 'sub', 'x0', 'x19', 'x0');
            }
        }
    }

    public function visitMultiplication($ctx): void {
        $children = $ctx->unary();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        $isFloat = $this->lastResultWasFloat;

        for ($i = 1; $i < count($children); $i++) {
            $op = $ctx->getChild($i * 2 - 1)->getText();
            if ($isFloat) {
                $this->instr('sub', 'sp', 'sp', '#16');
                $this->instr('str', 's0', '[sp, #0]');
                $this->visit($children[$i]);
                $this->instr('ldr', 's19', '[sp, #0]');
                $this->instr('add', 'sp', 'sp', '#16');
                switch ($op) {
                    case '*': $this->instr('fmul', 's0', 's19', 's0'); break;
                    case '/': $this->instr('fdiv', 's0', 's19', 's0'); break;
                }
                $this->lastResultWasFloat = true;
            } else {
                $this->instr('sub', 'sp', 'sp', '#16');
                $this->instr('str', 'x0', '[sp, #0]');
                $this->visit($children[$i]);
                $this->instr('ldr', 'x19', '[sp, #0]');
                $this->instr('add', 'sp', 'sp', '#16');
                switch ($op) {
                    case '*': $this->instr('mul', 'x0', 'x19', 'x0'); break;
                    case '/':
                        $labelOk = $this->newLabel('div_ok');
                        $this->instr('cmp', 'x0', '#0');
                        $this->instr('b.ne', $labelOk);
                        $this->loadImmediate('x0', 0);
                        $this->instr('b', $labelOk);
                        $this->emitLabel($labelOk);
                        $this->instr('sdiv', 'x0', 'x19', 'x0');
                        break;
                    case '%':
                        $this->instr('sub', 'sp', 'sp', '#16');
                        $this->instr('str', 'x0', '[sp, #0]');
                        $this->instr('sdiv', 'x0', 'x19', 'x0');
                        $this->instr('ldr', 'x20', '[sp, #0]');
                        $this->instr('add', 'sp', 'sp', '#16');
                        $this->instr('mul',  'x0', 'x0', 'x20');
                        $this->instr('sub',  'x0', 'x19', 'x0');
                        break;
                }
            }
        }
    }

    public function visitUnary($ctx): void {
        if ($ctx->primaryExpr() !== null) {
            $this->visit($ctx->primaryExpr());
            return;
        }

        $this->visit($ctx->unary());

        if ($ctx->NOT() !== null) {
            $this->instr('cmp', 'x0', '#0');
            $labelT = $this->newLabel('not_t');
            $labelE = $this->newLabel('not_e');
            $this->instr('b.eq', $labelT);
            $this->loadImmediate('x0', 0);
            $this->instr('b', $labelE);
            $this->emitLabel($labelT);
            $this->loadImmediate('x0', 1);
            $this->emitLabel($labelE);
        } elseif ($ctx->MINUS() !== null) {
            $this->instr('neg', 'x0', 'x0');
        } elseif ($ctx->AMP() !== null) {
            // &var — obtener dirección
            // El resultado de visit(unary) ya tiene el valor — buscar la dirección
            // Esto se maneja mejor en operand
        } elseif ($ctx->STAR() !== null) {
            // *ptr — desreferenciar
            $this->instr('ldr', 'x0', '[x0]');
        }
    }

    public function visitPrimaryExpr($ctx): void {
        if ($ctx->operand() !== null) {
            $this->visit($ctx->operand());
            return;
        }
        if ($ctx->functionCall() !== null) {
            $this->visit($ctx->functionCall());
            return;
        }
        if ($ctx->builtinCall() !== null) {
            $this->visit($ctx->builtinCall());
            return;
        }
        // primaryExpr[index]
        if ($ctx->primaryExpr() !== null && $ctx->expression() !== null) {

            // Evaluar base (arr) → x0 = dirección base (puede ser fila de 2D)
            $this->visit($ctx->primaryExpr());

            // Guardar base
            $this->instr('sub', 'sp', 'sp', '#16');
            $this->instr('str', 'x0', '[sp, #0]');

            // Evaluar índice
            $this->visitExprIntoX0($ctx->expression());

            // Obtener el nombre REAL del array (navegar hasta el operand más profundo)
            // y contar cuántas dimensiones de indexación hay entre este nodo y el operand
            $baseCtx    = $ctx->primaryExpr();
            $indexDepth = 1; // este nodo añade 1
            while ($baseCtx->primaryExpr() !== null) {
                $indexDepth++;
                $baseCtx = $baseCtx->primaryExpr();
            }
            $name = $baseCtx->getText();

            // Obtener tipo del array completo
            $arrayType = 'int32';
            $info = $this->symbolTable->lookup($name);
            if ($info) {
                $arrayType = $info['type'];
            } elseif ($this->currentFunc !== '') {
                $paramOffset = $this->symbolTable->getParamOffset($this->currentFunc, $name);
                if ($paramOffset >= 0) {
                    $func = $this->symbolTable->getFunction($this->currentFunc);
                    foreach ($func['params'] ?? [] as $p) {
                        if (($p['name'] ?? null) === $name) {
                            $arrayType = $p['type'] ?? 'int32';
                            break;
                        }
                    }
                }
            }

            // Bajar $indexDepth niveles para obtener el tipo del elemento en ESTE nivel
            // p.ej. [2][2]int32 con depth=1 → [2]int32; depth=2 → int32
            $currentType = $arrayType;
            for ($d = 0; $d < $indexDepth; $d++) {
                $currentType = $this->getElementType($currentType);
            }
            $elemType  = $currentType;
            $elemSize  = $this->stackStrideFor($elemType);
            $elemShift = $this->sizeToShift($elemSize);

            $this->instr('lsl', 'x0', 'x0', "#$elemShift");

            // Recuperar base
            $this->instr('ldr', 'x9', '[sp, #0]');
            $this->instr('add', 'sp', 'sp', '#16');

            // Dirección final del elemento
            $this->instr('add', 'x0', 'x9', 'x0');

            // Cargar valor según tipo del elemento
            if ($elemType === 'float32') {
                $this->instr('ldr', 's0', '[x0]');
                $this->lastResultWasFloat = true;
            } elseif (preg_match('/^\[\d+\]/', $elemType)) {
                // Elemento es un sub-array (fila de 2D): dejar x0 como dirección base
                $this->lastResultWasFloat = false;
            } elseif ($elemType === 'string') {
                $this->instr('ldr', 'x0', '[x0]');
                $this->lastResultWasFloat = false;
            } else {
                // int32, bool, rune: cargar 32 bits y sign-extend
                $this->instr('ldr', 'w0', '[x0]');
                $this->instr('sxtw', 'x0', 'w0');
                $this->lastResultWasFloat = false;
            }
        }
    }

    public function visitOperand($ctx): void {
        if ($ctx->literal() !== null) {
            $this->visit($ctx->literal());
            return;
        }
        if ($ctx->NIL() !== null) {
            $this->loadImmediate('x0', 0);
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->AMP() !== null && $ctx->ID() !== null) {
            $name   = $ctx->ID()->getText();
            $offset = $this->resolveOffset($name);
            if ($offset >= 0) {
                $this->instr('add', 'x0', 'x29', "#$offset");
            } else {
                $this->loadImmediate('x0', 0);
            }
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->STAR() !== null && $ctx->ID() !== null) {
            $name   = $ctx->ID()->getText();
            $offset = $this->resolveOffset($name);
            if ($offset >= 0) {
                $this->instr('ldr', 'x0', "[x29, #{$offset}]");
            } else {
                $this->loadImmediate('x0', 0);
                return;
            }
            $this->instr('ldr', 'x0', '[x0]');
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->ID() !== null) {
    $name   = $ctx->ID()->getText();
    $type   = 'int32';
    $offset = -1;

    // 1. Buscar en parámetros
    if ($this->currentFunc !== '') {
        $paramOffset = $this->symbolTable->getParamOffset($this->currentFunc, $name);
        if ($paramOffset >= 0) {
            $offset = $paramOffset;
            $func   = $this->symbolTable->getFunction($this->currentFunc);
            foreach ($func['params'] ?? [] as $p) {
                if (($p['name'] ?? null) === $name) {
                    $type = $p['type'] ?? 'int32';
                    break;
                }
            }
        }
    }

    // 2. Variables locales de la función actual
    if ($offset < 0 && $this->currentFunc !== '') {
        $localOffset = $this->symbolTable->getLocalOffset($this->currentFunc, $name);
        if ($localOffset >= 0) {
            $offset = $localOffset;
            $info = $this->symbolTable->lookup($name);
            if ($info) $type = $info['type'];
        }
    }

    // 3. Fallback global
    if ($offset < 0) {
        $info = $this->symbolTable->lookup($name);
        if ($info) {
            $type   = $info['type'];
            $offset = $info['offset'];
        }
    }

    // Usar isFloatVar para detectar variables float con tipo mal inferido
    $isFloat = ($type === 'float32') || $this->isFloatVar($name);

    if ($offset >= 0) {
        if ($isFloat) {
            $this->instr('ldr', 's0', "[x29, #{$offset}]");
            $this->lastResultWasFloat = true;
        } elseif (preg_match('/^\[\d+\]/', $type)) {
            $isParam = ($this->currentFunc !== '' &&
                        $this->symbolTable->getParamOffset($this->currentFunc, $name) >= 0);
            if ($isParam) {
                $this->instr('ldr', 'x0', "[x29, #{$offset}]");
            } else {
                $this->instr('add', 'x0', 'x29', "#$offset");
            }
            $this->lastResultWasFloat = false;
        } else {
            $this->instr('ldr', 'x0', "[x29, #{$offset}]");
            $this->lastResultWasFloat = false;
        }
    } else {
        $this->loadImmediate('x0', 0);
        $this->lastResultWasFloat = false;
    }
    return;
}
        if ($ctx->expression() !== null) {
            $this->visitExprIntoX0($ctx->expression());
        }
    }

    public function visitLiteral($ctx): void {
        if ($ctx->INT_LIT() !== null) {
            $val = (int)$ctx->INT_LIT()->getText();
            $this->loadImmediate('x0', $val);
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->FLOAT_LIT() !== null) {
            $val = $ctx->FLOAT_LIT()->getText();
            // Cargar float como bits en registro general, luego mover a s0
            $bits = unpack('L', pack('f', (float)$val))[1];
            $this->instr('ldr', 'w0', "=$bits");
            $this->instr('fmov', 's0', 'w0');
            $this->lastResultWasFloat = true;
            return;
        }
        if ($ctx->STRING_LIT() !== null) {
            $raw   = $ctx->STRING_LIT()->getText();
            $inner = substr($raw, 1, -1); // quitar comillas
            $inner = stripcslashes($inner);
            $label = $this->getStringLabel($inner);
            $this->instr('adrp', 'x0', $label);
            $this->instr('add', 'x0', 'x0', ":lo12:{$label}");
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->TRUE() !== null) {
            $this->loadImmediate('x0', 1);
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->FALSE() !== null) {
            $this->loadImmediate('x0', 0);
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->RUNE_LIT() !== null) {
            $raw  = $ctx->RUNE_LIT()->getText();
            $char = substr($raw, 1, -1);
            $val  = strlen($char) === 1 ? ord($char) : ord(stripcslashes($char));
            $this->loadImmediate('x0', $val);
            $this->lastResultWasFloat = false;
            return;
        }
        if ($ctx->arrayLiteral() !== null) {
            $this->visit($ctx->arrayLiteral());
        }
    }

    public function visitArrayLiteral($ctx): void {
        $elements = $ctx->arrayElements()->arrayElement();
        $typeText  = $ctx->arrayTypeLiteral()->getText(); // ej: [5]int32
        
        // Determinar tamaño de elemento (int32 = 4 bytes)
        $elemSize  = 4; // simplificado para int32
        $elemShift = 2; // lsl #2
        

        $this->loadImmediate('x0', 0); // placeholder — ver abajo
    }

    // ── Built-ins ─────────────────────────────────────────────────────────────

    public function visitLenCall($ctx): void {
        $type = $this->getExprType($ctx->expression());
        if (preg_match('/^\[(\d+)\]/', $type, $m)) {
            // Array: longitud conocida en compilación
            $this->instr('mov', 'x0', '#' . $m[1]);
        } else {
            // String: calcular strlen
            $this->visitExprIntoX0($ctx->expression()); // x0 = puntero
            // strlen inline: contar hasta null terminator
            $labelLoop = $this->newLabel('strlen');
            $labelEnd  = $this->newLabel('strlen_end');
            $this->instr('mov', 'x1', '#0');           // contador
            $this->emitLabel($labelLoop);
            $this->instr('ldrb', 'w2', '[x0, x1]');
            $this->instr('cbz',  'w2', $labelEnd);
            $this->instr('add',  'x1', 'x1', '#1');
            $this->instr('b',    $labelLoop);
            $this->emitLabel($labelEnd);
            $this->instr('mov', 'x0', 'x1');           // resultado en x0
        }
        $this->lastResultWasFloat = false;
    }

    public function visitTypeOfCall($ctx): void {
        $type  = $this->getExprType($ctx->expression());
        $label = $this->getStringLabel($type);
        $this->instr('adrp', 'x0', $label);
        $this->instr('add', 'x0', 'x0', ":lo12:{$label}");
    }

    public function visitCastCall($ctx): void {
        $this->visitExprIntoX0($ctx->expression());
        $target = strtolower($ctx->getChild(0)->getText());
        if ($target === 'float32') {
            $this->instr('scvtf', 's0', 'x0');
        } elseif ($target === 'int32' || $target === 'int') {
            $this->instr('fcvtzs', 'x0', 's0');
        }
        $this->lastResultWasFloat = ($target === 'float32');
        // bool/string/rune: x0 ya tiene el valor
    }

    public function visitNowCall($ctx): void {
        // Retorna string vacío por ahora (requiere libc)
        $label = $this->getStringLabel('');
        $this->instr('adrp', 'x0', $label);
        $this->instr('add', 'x0', 'x0', ":lo12:{$label}");
    }

    public function visitSubstrCall($ctx): void {
        // Buffer estático para substr
        if (!isset($this->stringLiterals['__substr_buf'])) {
            $this->dataSection[] = '__substr_buf: .space 256';
            $this->stringLiterals['__substr_buf'] = '__substr_buf';
        }

        $this->visitExprIntoX0($ctx->expression(0));
        $this->instr('mov', 'x19', 'x0');

        $this->visitExprIntoX0($ctx->expression(1));
        $this->instr('add', 'x19', 'x19', 'x0');   // ptr + start

        $this->visitExprIntoX0($ctx->expression(2));
        $this->instr('mov', 'x20', 'x0');           // length

        // Cargar dirección del buffer
        $this->instr('adrp', 'x21', '__substr_buf');
        $this->instr('add',  'x21', 'x21', ':lo12:__substr_buf');

        // Copiar length bytes
        $labelCopy = $this->newLabel('substr_cp');
        $labelDone = $this->newLabel('substr_dn');
        $this->instr('mov', 'x9', '#0');
        $this->emitLabel($labelCopy);
        $this->instr('cmp',  'x9', 'x20');
        $this->instr('b.ge', $labelDone);
        $this->instr('ldrb', 'w10', '[x19, x9]');
        $this->instr('strb', 'w10', '[x21, x9]');
        $this->instr('add',  'x9', 'x9', '#1');
        $this->instr('b', $labelCopy);
        $this->emitLabel($labelDone);
        $this->instr('strb', 'wzr', '[x21, x9]');  // null terminator

        $this->instr('mov', 'x0', 'x21');
        $this->lastResultWasFloat = false;
    }
    // ── Helpers ───────────────────────────────────────────────────────────────

    private function isFloatVar(string $name): bool {
        // Primero: tipo registrado en tabla de símbolos
        $type = '';

        if ($this->currentFunc !== '') {
            // Parámetro
            $poff = $this->symbolTable->getParamOffset($this->currentFunc, $name);
            if ($poff >= 0) {
                $func = $this->symbolTable->getFunction($this->currentFunc);
                foreach ($func['params'] ?? [] as $p) {
                    if (($p['name'] ?? null) === $name) { $type = $p['type'] ?? ''; break; }
                }
            }
        }
        if ($type === '') {
            $info = $this->symbolTable->lookup($name);
            $type = $info['type'] ?? 'int32';
        }

        if ($type === 'float32') return true;

        // Segundo: el generador marcó esta variable como float (tipo inferido incorrectamente)
        return isset($this->floatVars[$this->currentFunc][$name]);
    }
    private function resolveOffset(string $name): int {
        if ($this->currentFunc !== '') {
            $poff = $this->symbolTable->getParamOffset($this->currentFunc, $name);
            if ($poff >= 0) return $poff;
            $loff = $this->symbolTable->getLocalOffset($this->currentFunc, $name);
            if ($loff >= 0) return $loff;
        }
        return $this->symbolTable->getOffset($name);
    }

    private function loadImmediate(string $reg, int $value): void {
        if ($value >= -65535 && $value <= 65535) {
            $this->instr('mov', $reg, "#$value");
        } else {
            $this->instr('ldr', $reg, "=$value");
        }
    }
    
    
    private function getExprType($ctx): string {
        if ($ctx === null) return 'int32';

        $text = $ctx->getText();
        

        // Literal float directo
        if (preg_match('/^\d+\.\d*$/', $text) || preg_match('/^\.\d+$/', $text)) return 'float32';
        // String literal
        if (str_starts_with($text, '"')) return 'string';
        // Bool literal
        if ($text === 'true' || $text === 'false') return 'bool';
        // Nil
        if ($text === 'nil') return 'nil';
        if (preg_match("/^'.*'$/u", $text)) return 'rune';


        if (str_contains($text, 'nil')) return 'nil';

        // Variable o parámetro conocido (solo si el texto es un identificador simple)
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $text)) {
            $info = $this->symbolTable->lookup($text);
            if ($info) return $info['type'];
            // Puede ser parámetro de función actual
            if ($this->currentFunc !== '') {
                $offset = $this->symbolTable->getParamOffset($this->currentFunc, $text);
                if ($offset >= 0) {
                    // Buscar el tipo desde los params de la función
                    $func = $this->symbolTable->getFunction($this->currentFunc);
                    foreach ($func['params'] ?? [] as $p) {
                        if ($p['name'] === $text) return $p['type'];
                    }
                }
            }
        }

        // Indexación: arr[i] o arr[i][j] — tipo del elemento
        // Detectar patrón: identificador seguido de uno o más [...]
        if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)(\[.+)$/', $text, $m)) {
            $varName = $m[1];
            $indexPart = $m[2];
            // Contar cuántos índices hay (cuántos niveles de [] hay)
            $depth = preg_match_all('/\[/', $indexPart);

            $baseType = 'int32';
            $info = $this->symbolTable->lookup($varName);
            if ($info) {
                $baseType = $info['type'];
            }
            // Quitar $depth dimensiones del tipo
            for ($d = 0; $d < $depth; $d++) {
                $baseType = $this->getElementType($baseType);
            }
            return $baseType;
        }

        if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\(/', $text, $m)) {
            $funcInfo = $this->symbolTable->getFunction($m[1]);
            if ($funcInfo) return $funcInfo['returnType'] ?? 'int32';
        }
        // Builtins que retornan string
        if (str_starts_with($text, 'now()')) return 'string';
        if (str_starts_with($text, 'substr(')) return 'string';
        if (str_starts_with($text, 'typeOf(')) return 'string';
        if (str_starts_with($text, 'len(')) return 'int32';

        // Expresiones con cast explícito: float32(...) -> float32
        if (str_starts_with($text, 'float32(')) return 'float32';
        if (str_starts_with($text, 'int32(') || str_starts_with($text, 'int(')) return 'int32';
        if (str_starts_with($text, 'bool(')) return 'bool';
        if (str_starts_with($text, 'string(')) return 'string';



        // Expresiones compuestas
        if (str_contains($text, '&&') || str_contains($text, '||') || str_starts_with($text, '!')) return 'bool';
        if (preg_match('/[<>]=?|==|!=/', $text)) return 'bool';

        return 'int32';
    }

    private function lastExprWasFloat($ctx): bool {
        if ($ctx === null) return false;
        $text = $ctx->getText();
        
        if (preg_match('/^\d+\.\d*$/', $text) || preg_match('/^\.\d+$/', $text)) return true;
        if (str_starts_with($text, 'float32(')) return true;
        
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $text)) {
            // Buscar en symbol table (variables locales, params registrados como vars)
            $info = $this->symbolTable->lookup($text);
            if ($info) return $info['type'] === 'float32';
            
            // Buscar en paramOffsets: si tiene offset, intentar inferir tipo
            // como fallback buscar en el scope actual si fue registrado
            if ($this->currentFunc !== '') {
                $offset = $this->symbolTable->getParamOffset($this->currentFunc, $text);
                if ($offset >= 0) {
                    // El parámetro existe — buscar su tipo en params[]
                    $func = $this->symbolTable->getFunction($this->currentFunc);
                    foreach ($func['params'] ?? [] as $p) {
                        // params puede tener distintas estructuras según FunctionCollector
                        $pname = $p['name'] ?? $p[0] ?? null;
                        $ptype = $p['type'] ?? $p[1] ?? 'int32';
                        if ($pname === $text) return $ptype === 'float32';
                    }
                }
            }
        }
        
        if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\(/', $text, $m)) {
            $funcInfo = $this->symbolTable->getFunction($m[1]);
            if ($funcInfo) return ($funcInfo['returnType'] ?? '') === 'float32';
        }
        
        return false;
    }

    /** Dado [N]T devuelve T. Dado *[N]T devuelve T. Dado T devuelve T. */
    private function getElementType(string $arrayType): string {
        // Quitar puntero inicial si lo hay
        if (str_starts_with($arrayType, '*')) {
            $arrayType = substr($arrayType, 1);
        }
        if (preg_match('/^\[\d+\](.+)$/', $arrayType, $m)) {
            return $m[1];
        }
        return $arrayType; // ya es tipo escalar
    }

    /** Convierte tamaño en bytes a shift para lsl (potencia de 2). */
    private function sizeToShift(int $size): int {
        return match($size) {
            1  => 0,
            2  => 1,
            4  => 2,
            8  => 3,
            16 => 4,
            default => 3,
        };
    }

    private function emitArrayAddress(string $name, array $indices): void {
        $offset = -1;
        $type   = '';

        // 1. Buscar primero en parámetros (tienen prioridad)
        if ($this->currentFunc !== '') {
            $paramOffset = $this->symbolTable->getParamOffset($this->currentFunc, $name);
            if ($paramOffset >= 0) {
                $offset = $paramOffset;
                $func   = $this->symbolTable->getFunction($this->currentFunc);
                foreach ($func['params'] ?? [] as $p) {
                    if (($p['name'] ?? null) === $name) {
                        $type = $p['type'] ?? '';
                        break;
                    }
                }
            }
        }

        // 2. Si no es parámetro, buscar en variables locales de la función actual
        if ($offset < 0 && $this->currentFunc !== '') {
            $localOffset = $this->symbolTable->getLocalOffset($this->currentFunc, $name);
            if ($localOffset >= 0) {
                $offset = $localOffset;
                $info = $this->symbolTable->lookup($name);
                if ($info) $type = $info['type'] ?? '';
            }
        }

        // 3. Fallback: búsqueda global
        if ($offset < 0) {
            $info = $this->symbolTable->lookup($name);
            if ($info) {
                $offset = $info['offset'];
                $type   = $info['type'] ?? '';
            }
        }

        // Tamaño de elemento — se calcula por nivel dentro del loop de índices

        // Calcular dirección base
        $isParam = ($this->currentFunc !== '' &&
                    $this->symbolTable->getParamOffset($this->currentFunc, $name) >= 0);
        if (str_starts_with($type, '*') || ($isParam && preg_match('/^\[\d+\]/', $type))) {
            // Parámetro array o puntero: el slot contiene un puntero, cargar con ldr
            $this->instr('ldr', 'x10', "[x29, #{$offset}]");
        } else {
            // Array local: dirección base directa
            $this->instr('add', 'x10', 'x29', "#$offset");
        }

        // Iterar índices, recalculando el stride por nivel
        $currentType = $type;
        foreach ($indices as $idx) {
            $elemType  = $this->getElementType($currentType);
            $elemSize  = $this->stackStrideFor($elemType);
            $elemShift = $this->sizeToShift($elemSize);

            $this->visitExprIntoX0($idx);
            $this->instr('lsl', 'x0', 'x0', "#$elemShift");
            $this->instr('add', 'x10', 'x10', 'x0');

            $currentType = $elemType; // bajar un nivel de dimensión
        }
    }

    // ── Funciones auxiliares de runtime ──────────────────────────────────────
    // Se emiten al final del archivo .s
    

    public function emitRuntimeHelpers(): void {
        $this->emit('');
        $this->comment('=== Runtime Helpers ===');
        $this->emitPrintIntHelper();
        $this->emitPrintFloatHelper();
        $this->emitPrintBoolHelper();
        $this->emitPrintStrHelper();
    }

    private function emitPrintIntHelper(): void {
        $this->emit('');
        $this->comment('__print_int: imprime x0 como entero decimal');
        $this->emit('__print_int:');
        // Frame: [0]=x29/x30(16) [16]=x19(8) [24]=output_buf(20) [44]=temp_buf(20) -> 64 bytes
        $this->instr('sub', 'sp', 'sp', '#64');
        $this->instr('stp', 'x29, x30', '[sp, #0]');
        $this->instr('mov', 'x29', 'sp');
        $this->instr('str', 'x19', '[sp, #16]');   // preservar x19

        // Buffer de salida en sp+24
        $this->instr('add', 'x9', 'sp', '#24');   // buffer salida
        $this->instr('mov', 'x10', '#0');           // longitud
        $this->instr('mov', 'x11', 'x0');           // número

        // Manejo de negativos
        $labelPos = $this->newLabel('pi_pos');
        $this->instr('cmp', 'x11', '#0');
        $this->instr('b.ge', $labelPos);
        $this->instr('neg', 'x11', 'x11');
        $this->instr('mov', 'x12', '#45');         // '-'
        $this->instr('strb', 'w12', '[x9]');
        $this->instr('add', 'x9', 'x9', '#1');
        $this->instr('mov', 'x10', '#1');
        $this->emitLabel($labelPos);

        // Caso especial: 0
        $labelNotZero = $this->newLabel('pi_nz');
        $this->instr('cmp', 'x11', '#0');
        $this->instr('b.ne', $labelNotZero);
        $this->instr('mov', 'x12', '#48');
        $this->instr('strb', 'w12', '[x9]');
        $this->instr('add', 'x10', 'x10', '#1');
        $this->instr('b', '__pi_write');
        $this->emitLabel($labelNotZero);

        // Convertir dígitos (en reversa)
        $this->instr('add', 'x13', 'sp', '#44'); // puntero al buffer temporal de dígitos
        $this->instr('mov', 'x14', '#0');          // dígitos escritos
        $labelDigit = $this->newLabel('pi_digit');
        $this->emitLabel($labelDigit);
        $this->instr('cmp', 'x11', '#0');
        $this->instr('b.eq', '__pi_reverse');
        $this->instr('mov', 'x15', '#10');
        $this->instr('udiv', 'x12', 'x11', 'x15');
        $this->instr('msub', 'x12', 'x12', 'x15', 'x11'); // x12 = x11 % 10
        $this->instr('add',  'x12', 'x12', '#48');
        $this->instr('strb', 'w12', '[x13]');
        $this->instr('add',  'x13', 'x13', '#1');
        $this->instr('add',  'x14', 'x14', '#1');
        $this->instr('udiv', 'x11', 'x11', 'x15');
        $this->instr('b', $labelDigit);

        // Invertir dígitos
        $this->emit('__pi_reverse:');
        $this->instr('sub', 'x13', 'x13', '#1');
        $labelRev = $this->newLabel('pi_rev');
        $this->emitLabel($labelRev);
        $this->instr('cmp', 'x14', '#0');
        $this->instr('b.eq', '__pi_write');
        $this->instr('ldrb', 'w12', '[x13]');
        $this->instr('strb', 'w12', '[x9]');
        $this->instr('add',  'x9',  'x9',  '#1');
        $this->instr('sub',   'x13', 'x13', '#1');
        $this->instr('add',  'x10', 'x10', '#1');
        $this->instr('sub',  'x14', 'x14', '#1');
        $this->instr('b', $labelRev);

        // Escribir
        $this->emit('__pi_write:');
        $this->instr('add', 'x1', 'sp', '#24');  // dirección buffer
        $this->instr('mov', 'x2', 'x10');          // longitud
        $this->loadImmediate('x0', 1);            // stdout
        $this->instr('mov', 'x8', '#64');           // write
        $this->instr('svc', '#0');

        $this->instr('ldr', 'x19', '[sp, #16]');
        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#64');
        $this->instr('ret');
    }

    private function emitPrintFloatHelper(): void {
        $this->emit('');
        $this->comment('__print_float: imprime s0 como decimal');
        $this->emit('__print_float:');
        // Frame: [0]=x29/x30, [16]=x19/x20, [32]=x21, [40]=s0 guardado
        $this->instr('sub', 'sp', 'sp', '#48');
        $this->instr('stp', 'x29, x30', '[sp, #0]');
        $this->instr('stp', 'x19, x20', '[sp, #16]');
        $this->instr('str', 'x21',      '[sp, #32]');
        $this->instr('mov', 'x29', 'sp');

        // Manejar negativos
        $labelPos = $this->newLabel('pf_pos');
        $this->instr('fmov',  'w19', 's0');         // bits de s0 en w19
        $this->instr('lsr',   'w19', 'w19', '#31'); // bit signo
        $this->instr('cbz',   'w19', $labelPos);
        $this->instr('fneg',  's0', 's0');
        // imprimir '-'
        $this->instr('sub',  'sp', 'sp', '#16');
        $this->instr('mov',  'w0', '#45');
        $this->instr('strb', 'w0', '[sp]');
        $this->instr('mov',  'x1', 'sp');
        $this->instr('mov',  'x2', '#1');
        $this->instr('mov',  'x0', '#1');
        $this->instr('mov',  'x8', '#64');
        $this->instr('svc',  '#0');
        $this->instr('add',  'sp', 'sp', '#16');
        $this->emitLabel($labelPos);

        // Guardar s0 original
        $this->instr('str', 's0', '[sp, #40]');

        // --- Parte entera ---
        $this->instr('fcvtzs', 'x19', 's0');        // x19 = floor(s0)
        $this->instr('mov', 'x0', 'x19');
        $this->instr('bl', '__print_int');

        // --- Parte fraccionaria ---
        $this->instr('ldr',   's0', '[sp, #40]');   // restaurar s0
        $this->instr('scvtf', 's1', 'x19');          // s1 = float(parte_entera)
        $this->instr('fsub',  's0', 's0', 's1');     // s0 = fracción [0,1)

        // Multiplicar por 1000000 para obtener 6 decimales
        $this->instr('ldr',    'w0', '=1000000');
        $this->instr('scvtf',  's1', 'w0');
        $this->instr('fmul',   's0', 's0', 's1');
        $this->instr('fcvtzs', 'x20', 's0');         // x20 = dígitos fraccionarios

        $this->instr('fcvtzs', 'x20', 's0');

        // Solo imprimir fracción si no es cero
        $labelNoFrac = $this->newLabel('pf_nofrac');
        $this->instr('cbz', 'x20', $labelNoFrac);

        // imprimir '.' solo si hay fracción
        $this->instr('sub',  'sp', 'sp', '#16');
        $this->instr('mov',  'w0', '#46');
        $this->instr('strb', 'w0', '[sp]');
        $this->instr('mov',  'x1', 'sp');
        $this->instr('mov',  'x2', '#1');
        $this->instr('mov',  'x0', '#1');
        $this->instr('mov',  'x8', '#64');
        $this->instr('svc',  '#0');
        $this->instr('add',  'sp', 'sp', '#16');

        $this->instr('mov', 'x0', 'x20');
        $this->instr('bl', '__print_frac6');

        $this->emitLabel($labelNoFrac);


        $this->instr('ldr', 'x21',      '[sp, #32]');
        $this->instr('ldp', 'x19, x20', '[sp, #16]');
        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#48');
        $this->instr('ret');

        // --- Helper __print_frac6: imprime x0 con 6 dígitos, sin ceros finales ---
        $this->emit('');
        $this->comment('__print_frac6: imprime x0 como fracción de 6 dígitos sin ceros finales');
        $this->emit('__print_frac6:');
        // Frame: [0]=x29/x30, [16]=buf(8 bytes), total=32
        $this->instr('sub', 'sp', 'sp', '#32');
        $this->instr('stp', 'x29, x30', '[sp, #0]');
        $this->instr('mov', 'x29', 'sp');

        // Llenar buffer de 6 caracteres en [sp+16]
        // x0 = valor (0-999999)
        // Generamos los 6 dígitos de derecha a izquierda y luego recortamos ceros finales
        $this->instr('add', 'x9', 'sp', '#16');   // buffer
        $this->instr('mov', 'x10', '#6');          // contador
        $this->instr('mov', 'x11', 'x0');          // valor

        $labelFrac = $this->newLabel('frac_digit');
        $this->emitLabel($labelFrac);
        $this->instr('cmp', 'x10', '#0');
        $this->instr('b.eq', '__pf6_trim');
        $this->instr('mov',  'x12', '#10');
        $this->instr('udiv', 'x13', 'x11', 'x12');
        $this->instr('msub', 'x14', 'x13', 'x12', 'x11'); // dígito = x11 % 10
        $this->instr('add',  'x14', 'x14', '#48');
        // guardar dígito en posición [x10-1] del buffer
        $this->instr('sub',  'x15', 'x10', '#1');
        $this->instr('strb', 'w14', '[x9, x15]');
        $this->instr('mov',  'x11', 'x13');         // x11 /= 10
        $this->instr('sub',  'x10', 'x10', '#1');
        $this->instr('b', $labelFrac);

        // Recortar ceros finales
        $this->emit('__pf6_trim:');
        $this->instr('mov', 'x10', '#6');           // longitud inicial
        $labelTrim = $this->newLabel('pf6_trim_loop');
        $this->emitLabel($labelTrim);
        $this->instr('cmp', 'x10', '#1');           // mínimo 1 dígito
        $this->instr('b.eq', '__pf6_write');
        $this->instr('sub',  'x15', 'x10', '#1');
        $this->instr('ldrb', 'w14', '[x9, x15]');
        $this->instr('cmp',  'w14', '#48');         // '0'
        $this->instr('b.ne', '__pf6_write');
        $this->instr('sub',  'x10', 'x10', '#1');
        $this->instr('b', $labelTrim);

        $this->emit('__pf6_write:');
        $this->instr('mov', 'x1', 'x9');            // buffer
        $this->instr('mov', 'x2', 'x10');           // longitud
        $this->instr('mov', 'x0', '#1');
        $this->instr('mov', 'x8', '#64');
        $this->instr('svc', '#0');

        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#32');
        $this->instr('ret');
    }
    private function emitPrintBoolHelper(): void {
        // Emitir strings de true/false directamente con labels fijos
        if (!isset($this->stringLiterals['true'])) {
            $this->dataSection[] = '__rt_true: .asciz "true"';
            $this->stringLiterals['true'] = '__rt_true';
        }
        if (!isset($this->stringLiterals['false'])) {
            $this->dataSection[] = '__rt_false: .asciz "false"';
            $this->stringLiterals['false'] = '__rt_false';
        }

        $this->emit('');
        $this->comment('__print_bool: imprime x0 como true/false');
        $this->emit('__print_bool:');
        $this->instr('sub', 'sp', 'sp', '#16');
        $this->instr('stp', 'x29, x30', '[sp, #0]');

        $labelT = $this->newLabel('pb_true');
        $labelE = $this->newLabel('pb_end');

        $this->instr('cmp', 'x0', '#0');
        $this->instr('b.ne', $labelT);

        // false
        $this->instr('adrp', 'x1', '__rt_false');
        $this->instr('add',  'x1', 'x1', ':lo12:__rt_false');
        $this->instr('mov',  'x2', '#5');
        $this->instr('b', $labelE);

        $this->emitLabel($labelT);
        $this->instr('adrp', 'x1', '__rt_true');
        $this->instr('add',  'x1', 'x1', ':lo12:__rt_true');
        $this->instr('mov',  'x2', '#4');

        $this->emitLabel($labelE);
        $this->loadImmediate('x0', 1);
        $this->instr('mov', 'x8', '#64');
        $this->instr('svc', '#0');

        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#16');
        $this->instr('ret');
    }

    private function emitPrintStrHelper(): void {
        $this->emit('');
        $this->comment('__print_str: imprime string en x0 (null-terminated)');
        $this->emit('__print_str:');

        // Reservar stack (32 bytes alineados)
        $this->instr('sub', 'sp', 'sp', '#32');
        $this->instr('stp', 'x29, x30', '[sp, #0]');
        $this->instr('str', 'x19', '[sp, #16]');

        // x0 = puntero string
        $this->instr('mov', 'x19', 'x0');   // guardar base
        $this->instr('mov', 'x2', '#0');    // longitud

        $labelStrLen = $this->newLabel('strlen_loop');
        $this->emitLabel($labelStrLen);

        $this->instr('ldrb', 'w3', '[x19, x2]');
        $this->instr('cbz',  'w3', '__print_str_write');
        $this->instr('add',  'x2', 'x2', '#1');
        $this->instr('b', $labelStrLen);

        $this->emit('__print_str_write:');
        $this->instr('mov', 'x1', 'x19');   // buffer
        $this->loadImmediate('x0', 1);      // stdout
        $this->instr('mov', 'x8', '#64');   // write
        $this->instr('svc', '#0');

        // Restaurar registros
        $this->instr('ldr', 'x19', '[sp, #16]');
        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#32');
        $this->instr('ret');
    }
}