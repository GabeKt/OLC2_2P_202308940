<?php
namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;

/**
 * CodeGenerator — genera código ensamblador ARM64 (AArch64).
 *
 * Convenciones:
 *  - x29 = frame pointer, x30 = link register
 *  - Parámetros en x0-x7 (enteros/punteros), s0-s7 (float)
 *  - Variables locales en stack con offset desde sp
 *  - Strings en sección .data
 *  - fmt.Println usa syscall write (x8=64) para stdout
 */
class CodeGenerator extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private array  $lines       = [];   // líneas de código ensamblador
    private array  $dataSection = [];   // .data strings
    private int    $labelCount  = 0;    // contador de etiquetas únicas
    private string $currentFunc = '';   // función actual
    private int    $strLitCount = 0;    // contador de string literals
    private array  $stringLiterals = []; // map 'texto' => label

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
        // Asegurar alineación a 16 bytes (ABI ARM64)
        $frameSize = ($frameSize + 15) & ~15;
        $this->currentFunc = $funcName;

        $this->comment("=== función $funcName ===");
        $this->emitLabel($funcName);

        // Prólogo
        $this->comment("prólogo: reservar frame");
        $this->instr('sub', 'sp', 'sp', "#$frameSize");
        $this->instr('stp', 'x29, x30', "[sp, #0]");
        $this->instr('mov', 'x29', 'sp');

        // Guardar parámetros en el stack (leer offset desde SymbolTable.getParamOffset)
        if ($ctx->paramList() !== null) {
            $regIdx  = 0;
            $fregIdx = 0;
            foreach ($ctx->paramList()->paramGroup() as $group) {
                $typeText = $group->typeLiteral() ? $group->typeLiteral()->getText() : 'int32';
                $isFloat  = ($typeText === 'float32' || $typeText === 'float64');

                foreach ($group->ID() as $idToken) {
                    $name   = $idToken->getText();
                    $offset = $this->symbolTable->getParamOffset($funcName, $name);
                    if ($offset < 0) { $regIdx++; continue; }

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

        // Preservar registros callee-saved que usamos como temporales
        // x19, x20, x21 se usan en expresiones — guardarlos tras x29/x30
        $this->comment("preservar x19, x20, x21");
        $this->instr('stp', 'x19, x20', "[sp, #16]");
        $this->instr('str', 'x21',      "[sp, #32]");

        // Cuerpo
        $this->visit($ctx->block());

        // Epílogo (por si no hay return explícito)
        $this->emitLabel("{$funcName}_ret");
        $this->comment("epílogo: restaurar callee-saved");
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
                $offset = $this->symbolTable->getOffset($name);
                if ($offset < 0) return; // global — no manejamos globals por ahora

                $expr = $exprs[$i] ?? null;
                if ($expr !== null) {
                    $this->visitExprIntoX0($expr);
                    $type = $this->symbolTable->lookup($name)['type'] ?? 'int32';
                    if ($type === 'float32') {
                        $this->instr('str', 's0', "[sp, #{$offset}]");
                    } else {
                        $this->instr('str', 'x0', "[sp, #{$offset}]");
                    }
                } else {
                    // Valor por defecto: 0
                    $this->loadImmediate('x0', 0);
                    $this->instr('str', 'x0', "[sp, #{$offset}]");
                }
            }
        } else {
            // Sin inicialización — poner 0
            // Sin inicialización — valor por defecto según tipo
            foreach ($ids as $idToken) {
                $name   = $idToken->getText();
                $offset = $this->symbolTable->getOffset($name);
                if ($offset < 0) return;

                $type = $this->symbolTable->lookup($name)['type'] ?? 'int32';

                if ($type === 'string') {
                    // string vacío válido (no NULL)
                    $label = $this->getStringLabel('');
                    $this->instr('adrp', 'x0', $label);
                    $this->instr('add',  'x0', 'x0', ":lo12:{$label}");
                } elseif ($type === 'bool') {
                    $this->loadImmediate('x0', 0);
                } else {
                    $this->loadImmediate('x0', 0);
                }

                $this->instr('str', 'x0', "[sp, #{$offset}]");
            }
        }
    }

    public function visitShortVarDecl($ctx): void {
        $ids   = $ctx->idList()->ID();
        $exprs = $ctx->expressionList()->expression();

        foreach ($ids as $i => $idToken) {
            $name   = $idToken->getText();
            $offset = $this->symbolTable->getOffset($name);
            if ($offset < 0) continue;

            $expr = $exprs[$i] ?? null;
            if ($expr !== null) {
                $this->visitExprIntoX0($expr);
                $type = $this->symbolTable->lookup($name)['type'] ?? 'int32';
                if ($type === 'float32') {
                    $this->instr('str', 's0', "[sp, #{$offset}]");
                } else {
                    $this->instr('str', 'x0', "[sp, #{$offset}]");
                }
            }
        }
    }

    public function visitConstDecl($ctx): void {
        $name   = $ctx->ID()->getText();
        $offset = $this->symbolTable->getOffset($name);
        if ($offset < 0) return;

        $this->visitExprIntoX0($ctx->expression());
        $this->instr('str', 'x0', "[sp, #{$offset}]");
    }

    // ── Asignaciones ──────────────────────────────────────────────────────────

    public function visitAssignStmt($ctx): void {
        $lValues = $ctx->lValueList()->lValue();
        $op      = $ctx->assignOp()->getText();
        $exprs   = $ctx->expressionList()->expression();

        foreach ($lValues as $i => $lv) {
            $expr   = $exprs[$i] ?? null;
            if ($expr === null) continue;

            $this->visitExprIntoX0($expr); // resultado en x0

            $name = $lv->ID()->getText();
            $offset = $this->symbolTable->getOffset($name);
            if ($offset < 0) continue;

            if ($op !== '=') {
                // Operador compuesto: leer valor actual
                $this->instr('ldr', 'x1', "[sp, #{$offset}]");
                switch ($op) {
                    case '+=': $this->instr('add', 'x0', 'x1', 'x0'); break;
                    case '-=': $this->instr('sub', 'x0', 'x1', 'x0'); break;
                    case '*=': $this->instr('mul', 'x0', 'x1', 'x0'); break;
                    case '/=': $this->instr('sdiv', 'x0', 'x1', 'x0'); break;
                }
            }

            // Manejar índices de arreglo
            $indices = $lv->expression();
            if ($indices !== null && count($indices) > 0) {
                $this->instr('mov', 'x9', 'x0'); // guardar valor
                // Calcular dirección del elemento
                $this->emitArrayAddress($name, $indices);
                $this->instr('str', 'x9', '[x10]');
            } else {
                $this->instr('str', 'x0', "[sp, #{$offset}]");
            }
        }
    }

    public function visitIncDecStmt($ctx): void {
        $lv     = $ctx->lValue();
        $name   = $lv->ID()->getText();
        $offset = $this->symbolTable->getOffset($name);
        if ($offset < 0) return;

        $this->instr('ldr', 'x0', "[sp, #{$offset}]");
        $isInc = $ctx->INC() !== null;
        $this->instr($isInc ? 'add' : 'sub', 'x0', 'x0', '#1');
        $this->instr('str', 'x0', "[sp, #{$offset}]");
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
                $this->visitExprIntoX0($exprs[0]);
                // resultado ya en x0
            } else {
                // Múltiple retorno: x0, x1, x2...
                foreach ($exprs as $i => $expr) {
                    $this->visitExprIntoX0($expr);
                    if ($i > 0) $this->instr('mov', "x{$i}", 'x0');
                    // x0 ya tiene el primero
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

        // Evaluar cada argumento y moverlo al registro correcto.
        // Para evitar que la evaluación del arg[i] sobreescriba x19 (que puede
        // contener el operando izquierdo de una expresión pendiente), usamos
        // registros x9-x15 como staging area antes de mover a x0-x7.
        $stagingRegs = ['x9','x10','x11','x12','x13','x14','x15','x16'];

        foreach ($args as $i => $arg) {
            if ($arg->AMP() !== null) {
                $varName = $arg->ID()->getText();
                $offset  = $this->symbolTable->getOffset($varName);
                if ($offset < 0 && $this->currentFunc !== '') {
                    $offset = $this->symbolTable->getParamOffset($this->currentFunc, $varName);
                }
                $this->instr('add', $stagingRegs[$i] ?? 'x9', 'sp', "#$offset");
            } else {
                $this->visitExprIntoX0($arg->expression());
                $this->instr('mov', $stagingRegs[$i] ?? 'x9', 'x0');
            }
        }

        // Mover de staging a registros de argumento ABI (x0-x7)
        for ($i = 0; $i < $argCount; $i++) {
            $this->instr('mov', "x{$i}", $stagingRegs[$i] ?? 'x9');
        }

        $this->instr('bl', $name);
        // Resultado en x0
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
        if (count($children) === 1) return;

        for ($i = 1; $i < count($children); $i++) {
            $this->instr('mov', 'x19', 'x0');
            $this->visit($children[$i]);
            $op = $ctx->getChild($i * 2 - 1)->getText();
            $this->instr('cmp', 'x19', 'x0');
            $labelT = $this->newLabel('cmp_t');
            $labelE = $this->newLabel('cmp_e');
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

    public function visitAddition($ctx): void {
        $children = $ctx->multiplication();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        for ($i = 1; $i < count($children); $i++) {
            // Guardar operando izquierdo en stack para evitar clobber por expresiones anidadas
            $this->instr('sub', 'sp', 'sp', '#16');
            $this->instr('str', 'x0', '[sp, #0]');
            $this->visit($children[$i]);
            $this->instr('ldr', 'x19', '[sp, #0]');
            $this->instr('add', 'sp', 'sp', '#16');
            $op = $ctx->getChild($i * 2 - 1)->getText();
            $this->instr($op === '+' ? 'add' : 'sub', 'x0', 'x19', 'x0');
        }
    }

    public function visitMultiplication($ctx): void {
        $children = $ctx->unary();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        for ($i = 1; $i < count($children); $i++) {
            // Guardar operando izquierdo en stack para evitar clobber
            $this->instr('sub', 'sp', 'sp', '#16');
            $this->instr('str', 'x0', '[sp, #0]');
            $this->visit($children[$i]);
            $this->instr('ldr', 'x19', '[sp, #0]');
            $this->instr('add', 'sp', 'sp', '#16');
            $op = $ctx->getChild($i * 2 - 1)->getText();
            switch ($op) {
                case '*': $this->instr('mul', 'x0', 'x19', 'x0'); break;
                case '/':
                    $labelOk = $this->newLabel('div_ok');
                    $this->instr('cmp', 'x0', '#0');
                    $this->instr('b.ne', $labelOk);
                    $this->loadImmediate('x0', 0); // fallback
                    $this->instr('b', $labelOk);
                    $this->emitLabel($labelOk);
                    $this->instr('sdiv', 'x0', 'x19', 'x0');
                    break;
                case '%':
                    $this->instr('sub', 'sp', 'sp', '#16');
                    $this->instr('str', 'x0', '[sp, #0]');   // guardar b
                    
                    $this->instr('sdiv', 'x0', 'x19', 'x0');  // a/b
                    $this->instr('ldr', 'x20', '[sp, #0]');  // b
                    $this->instr('add', 'sp', 'sp', '#16');
                    $this->instr('mul',  'x0', 'x0', 'x20');   // (a/b)*b
                    $this->instr('sub',  'x0', 'x19', 'x0');   // a - (a/b)*b
                    break;
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
            $this->visit($ctx->primaryExpr());
            // x0 tiene la dirección base del arreglo
            $this->instr('mov', 'x19', 'x0');
            $this->visitExprIntoX0($ctx->expression());
            // offset = index * 8 (tamaño elemento simplificado)
            $this->instr('lsl', 'x0', 'x0', '#3');
            $this->instr('add', 'x0', 'x19', 'x0');
            $this->instr('ldr', 'x0', '[x0]');
        }
    }

    public function visitOperand($ctx): void {
        if ($ctx->literal() !== null) {
            $this->visit($ctx->literal());
            return;
        }
        if ($ctx->NIL() !== null) {
            $this->loadImmediate('x0', 0);
            return;
        }
        if ($ctx->AMP() !== null && $ctx->ID() !== null) {
            $name   = $ctx->ID()->getText();
            $offset = $this->symbolTable->getOffset($name);
            $this->instr('add', 'x0', 'sp', "#$offset");
            return;
        }
        if ($ctx->STAR() !== null && $ctx->ID() !== null) {
            $name   = $ctx->ID()->getText();
            $offset = $this->symbolTable->getOffset($name);
            $this->instr('ldr', 'x0', "[sp, #{$offset}]");
            $this->instr('ldr', 'x0', '[x0]');
            return;
        }
        if ($ctx->ID() !== null) {
            $name   = $ctx->ID()->getText();
            $offset = $this->symbolTable->getOffset($name);
            // Si no hay offset local, buscar en parámetros de la función actual
            if ($offset < 0 && $this->currentFunc !== '') {
                $offset = $this->symbolTable->getParamOffset($this->currentFunc, $name);
            }
            if ($offset >= 0) {
                $info = $this->symbolTable->lookup($name);
                $type = $info['type'] ?? 'int32';
                // Si no se encontró en SymbolTable, asumir int32 (es un parámetro)
                if ($type === 'float32') {
                    $this->instr('ldr', 's0', "[sp, #{$offset}]");
                } else {
                    $this->instr('ldr', 'x0', "[sp, #{$offset}]");
                }
            } else {
                $this->loadImmediate('x0', 0);
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
            return;
        }
        if ($ctx->FLOAT_LIT() !== null) {
            $val = $ctx->FLOAT_LIT()->getText();
            // Cargar float como bits en registro general, luego mover a s0
            $bits = unpack('L', pack('f', (float)$val))[1];
            $this->instr('ldr', 'w0', "=$bits");
            $this->instr('fmov', 's0', 'w0');
            return;
        }
        if ($ctx->STRING_LIT() !== null) {
            $raw   = $ctx->STRING_LIT()->getText();
            $inner = substr($raw, 1, -1); // quitar comillas
            $inner = stripcslashes($inner);
            $label = $this->getStringLabel($inner);
            $this->instr('adrp', 'x0', $label);
            $this->instr('add', 'x0', 'x0', ":lo12:{$label}");
            return;
        }
        if ($ctx->TRUE() !== null) {
            $this->loadImmediate('x0', 1);
            return;
        }
        if ($ctx->FALSE() !== null) {
            $this->loadImmediate('x0', 0);
            return;
        }
        if ($ctx->RUNE_LIT() !== null) {
            $raw  = $ctx->RUNE_LIT()->getText();
            $char = substr($raw, 1, -1);
            $val  = strlen($char) === 1 ? ord($char) : ord(stripcslashes($char));
            $this->loadImmediate('x0', $val);
            return;
        }
        if ($ctx->arrayLiteral() !== null) {
            $this->visit($ctx->arrayLiteral());
        }
    }

    public function visitArrayLiteral($ctx): void {
        // Arreglos: reservar espacio en stack e inicializar
        // Para simplificar, usamos una región temporal
        // En una implementación completa, esto iría en el frame
        // Por ahora dejamos x0 = 0 como placeholder
        $this->loadImmediate('x0', 0);
    }

    // ── Built-ins ─────────────────────────────────────────────────────────────

    public function visitLenCall($ctx): void {
        // len(arr) — para arreglos fijos, el tamaño es conocido en compilación
        // Para strings, leer la longitud del string object
        $this->visitExprIntoX0($ctx->expression());
        // Por ahora retornar tamaño del tipo
        $type = $this->getExprType($ctx->expression());
        if (preg_match('/^\[(\d+)\]/', $type, $m)) {
            $this->instr('mov', 'x0', "#" . $m[1]);
        }
        // Para string: x0 = longitud (simplificado)
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
        // bool/string/rune: x0 ya tiene el valor
    }

    public function visitNowCall($ctx): void {
        // Retorna string vacío por ahora (requiere libc)
        $label = $this->getStringLabel('');
        $this->instr('adrp', 'x0', $label);
        $this->instr('add', 'x0', 'x0', ":lo12:{$label}");
    }

    public function visitSubstrCall($ctx): void {
        // substr(str, start, len) — simplificado
        $this->visitExprIntoX0($ctx->expression(0));
        $this->instr('mov', 'x19', 'x0'); // base ptr
        $this->visitExprIntoX0($ctx->expression(1));
        $this->instr('add', 'x0', 'x19', 'x0'); // ptr + start
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function loadImmediate(string $reg, int $value): void {
        if ($value >= -65535 && $value <= 65535) {
            $this->instr('mov', $reg, "#$value");
        } else {
            $this->instr('ldr', $reg, "=$value");
        }
    }
    
    
    private function getExprType($ctx): string {
        // Intento básico de inferir tipo desde el contexto
        // En una versión completa se usaría el TypeChecker
        if ($ctx === null) return 'int32';

        $text = $ctx->getText();

        // Literal float
        if (preg_match('/^\d+\.\d*$/', $text)) return 'float32';
        // String literal
        if (str_starts_with($text, '"')) return 'string';
        // Bool
        if ($text === 'true' || $text === 'false') return 'bool';

        // Variable conocida
        $info = $this->symbolTable->lookup($text);
        if ($info) return $info['type'];

        return 'int32';
    }

    private function emitArrayAddress(string $name, array $indices): void {
        $offset = $this->symbolTable->getOffset($name);
        $this->instr('add', 'x10', 'sp', "#$offset");
        foreach ($indices as $idx) {
            $this->visitExprIntoX0($idx);
            $this->instr('lsl', 'x0', 'x0', '#3');
            $this->instr('add', 'x10', 'x10', 'x0');
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
        $this->comment('__print_float: imprime s0 como float');
        $this->emit('__print_float:');
        $this->instr('sub', 'sp', 'sp', '#16');
        $this->instr('stp', 'x29, x30', '[sp, #0]');
        // Convertir float a int y imprimir (simplificado: solo parte entera)
        $this->instr('fcvtzs', 'x0', 's0');
        $this->instr('bl', '__print_int');
        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#16');
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