<?php
namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;

class CodeGenerator extends GrammarBaseVisitor {

    private SymbolTable $symbolTable; // instancia de la tabla de símbolos para acceder a información de variables y funciones
    private array $lines = []; // para almacenar las líneas de código ensamblador generadas
    private array $dataSection = []; // para almacenar variables y strings (sección .data)
    private int $labelCount = 0; // contador para generar etiquetas únicas
    private string $currentFun = ''; // para saber en qué función estamos generando código
    private int $strLitCount = 0; // contador de strings literales
    private array $stringLiterals = [];

    private array $loopBreakStack = [];
    private array $loopContinueStack = [];

    public function __construct(SymbolTable $symbolTable) {
        $this->symbolTable = $symbolTable;
    }

    // HELPERS --------------------------------------------------------------------------------------------------------------------------------


    // Método auxiliar para agregar una línea de código ensamblador a la salida
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

    // Para escribir instrucciones con espacios al inicio de instrucciones
    private function instr(string $op, string ...$args): void {
        $operands = implode(', ', $args);
        $this->lines[] = "    {$op} {$operands}";
    }


    // RESULTADO FINAL  -----------------------------------------------------------
    
    
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

    // PUNTO DE ENTRADA (.start_) -----------------------------------------------------------

    public function visitProgram($ctx): void {
        // Emitir _start que llama a main y hace exit
        $this->emit('.global _start');
        $this->emit('_start:');
        $this->instr('bl', 'main');
        $this->instr('mov', 'x0', '#0');
        $this->instr('mov', 'x8', '#93');   // syscall exit
        $this->instr('svc', '#0');
        $this->emit('');

        $this->visitChildren($ctx);
    }

    
    public function visitFuncDecl($ctx): void {
        $funcName = $ctx->ID()->getText();
        $frameSize = $this->symbolTable->getFunctionFrameSize($funcName);
        $this->currentFunc = $funcName;

        $this->comment("======== Función {$funcName} ========");
        $this->emitLabel($funcName);

        // Prologo: guardar LR y FP, establecer nuevo FP
        $this->comment('Prologo: reservar frame');
        $this->instr('sub', 'sp', 'sp', "#$frameSize");
        $this->instr('stp', 'x29, x30', "[sp, #0]");
        $this->instr('mov', 'x29', 'sp');

        // Guardar parámetros en el stack
        if ($ctx->paramList() !== null) {
            $regIdx = 0;
            $fregIdx = 0;
            foreach ($ctx->paramList()->paramGroup() as $group) {
                $typeText = $group->typeLiteral() ? $group->typeLiteral()->getText() : 'unknown';
                $isFloat  = ($typeText === 'float32' || $typeText === 'float64');
                $isPtr    = str_starts_with($typeText, '*') || $group->STAR() !== null;

                $ids = $group->ID();
                foreach ($ids as $idToken) {
                    $name   = $idToken->getText();
                    $offset = $this->symbolTable->getOffset($name);
                    if ($offset < 0) continue;

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

        // Epílogo (por si no hay return explícito)
        $this->emitLabel("{$funcName}_ret");
        $this->comment("epílogo");
        $this->instr('ldp', 'x29, x30', "[sp, #0]");
        $this->instr('add', 'sp', 'sp', "#$frameSize");
        $this->instr('ret');
        $this->emit('');

        $this->currentFunc = '';

    }

    // BLOQUE ------------------------------------------------------------

    public function visitBlock($ctx): void{
        $this->visitChildren($ctx);
    }

    // DECLARACIONES ------------------------------------------------------------

    // declaracion de variables
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
                    $this->instr('mov', 'x0', '#0');
                    $this->instr('str', 'x0', "[sp, #{$offset}]");
                }
            }
        } else {
            // Sin inicialización — poner 0
            foreach ($ids as $idToken) {
                $name   = $idToken->getText();
                $offset = $this->symbolTable->getOffset($name);
                if ($offset < 0) return;
                $this->instr('mov', 'x0', '#0');
                $this->instr('str', 'x0', "[sp, #{$offset}]");
            }
        }
    }

    // declaración de variables cortas con :=
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


    // declaracion de constantes
    public function visitConstDecl($ctx): void {
        $name   = $ctx->ID()->getText();
        $offset = $this->symbolTable->getOffset($name);
        if ($offset < 0) return;

        $this->visitExprIntoX0($ctx->expression());
        $this->instr('str', 'x0', "[sp, #{$offset}]");
    }

    // ASIGNACIONES ------------------------------------------------------------

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

    
    // CONTROL DE FLUJO ------------------------------------------------------------

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


    // LLAMADAS A FUNCIONES ------------------------------------------------------------

    public function visitFunctionCall($ctx): void {
        $name = $ctx->ID()->getText();
        $args = ($ctx->argumentList() !== null) ? $ctx->argumentList()->argument() : [];

        // Evaluar argumentos y cargarlos en registros
        $regIdx  = 0;
        $fregIdx = 0;
        foreach ($args as $arg) {
            if ($arg->AMP() !== null) {
                // Paso por referencia: calcular dirección
                $varName = $arg->ID()->getText();
                $offset  = $this->symbolTable->getOffset($varName);
                $this->instr('add', "x{$regIdx}", 'sp', "#$offset");
                $regIdx++;
            } else {
                $expr = $arg->expression();
                $this->visitExprIntoX0($expr);
                $type = $this->getExprType($expr);
                if ($type === 'float32') {
                    if ($regIdx > 0) $this->instr('fmov', "s{$fregIdx}", 's0');
                    $fregIdx++;
                } else {
                    if ($regIdx > 0) $this->instr('mov', "x{$regIdx}", 'x0');
                    $regIdx++;
                }
            }
        }

        $this->instr('bl', $name);
        // Resultado queda en x0 (o s0 para float)
    }


    // fmt.Println ------------------------------------------------------------

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


    // HELPERS DE PRINTLN ------------------------------------------------------------

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
        $spLabel = $this->getStringLabel(" ");
        $this->instr('adrp', 'x0', $spLabel);
        $this->instr('add',  'x0', 'x0', ":lo12:{$spLabel}");
        $this->instr('mov',  'x2', '#1');
        $this->instr('mov',  'x1', 'x0');
        $this->instr('mov',  'x0', '#1');
        $this->instr('mov',  'x8', '#64');
        $this->instr('svc',  '#0');
    }

    private function emitPrintNewline(): void {
        $nlLabel = $this->getStringLabel("\n");
        $this->instr('adrp', 'x0', $nlLabel);
        $this->instr('add',  'x0', 'x0', ":lo12:{$nlLabel}");
        $this->instr('mov',  'x2', '#1');
        $this->instr('mov',  'x1', 'x0');
        $this->instr('mov',  'x0', '#1');
        $this->instr('mov',  'x8', '#64');
        $this->instr('svc',  '#0');
    }


    // Strings en la sección .data ------------------------------------------------------------

    private function getStringLabel(string $raw): string {
        if (isset($this->stringLiterals[$raw])) {
            return $this->stringLiterals[$raw];
        }
        $label = '__str_' . $this->strLitCount++;
        $escaped = $this->escapeForAsm($raw);
        $this->dataSection[] = "{$label}: .ascii \"{$escaped}\"";
        $this->dataSection[] = "{$label}_len = . - {$label}";
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


    // EVALUACION DE EXPRESIONES ------------------------------------------------------------

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
        $this->instr('mov', 'x0', '#1');
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
        $this->instr('mov', 'x0', '#0');
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
            $this->instr('mov', 'x0', '#0');
            $this->instr('b', $labelE);
            $this->emitLabel($labelT);
            $this->instr('mov', 'x0', '#1');
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
            $this->instr('mov', 'x0', '#0');
            $this->instr('b', $labelE);
            $this->emitLabel($labelT);
            $this->instr('mov', 'x0', '#1');
            $this->emitLabel($labelE);
        }
    }

    public function visitAddition($ctx): void {
        $children = $ctx->multiplication();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        for ($i = 1; $i < count($children); $i++) {
            $this->instr('mov', 'x19', 'x0');
            $this->visit($children[$i]);
            $op = $ctx->getChild($i * 2 - 1)->getText();
            $this->instr($op === '+' ? 'add' : 'sub', 'x0', 'x19', 'x0');
        }
    }

    public function visitMultiplication($ctx): void {
        $children = $ctx->unary();
        $this->visit($children[0]);
        if (count($children) === 1) return;

        for ($i = 1; $i < count($children); $i++) {
            $this->instr('mov', 'x19', 'x0');
            $this->visit($children[$i]);
            $op = $ctx->getChild($i * 2 - 1)->getText();
            switch ($op) {
                case '*': $this->instr('mul', 'x0', 'x19', 'x0'); break;
                case '/': $this->instr('sdiv', 'x0', 'x19', 'x0'); break;
                case '%':
                    // a % b = a - (a/b)*b
                    $this->instr('mov', 'x20', 'x0'); // b
                    $this->instr('sdiv', 'x21', 'x19', 'x20');
                    $this->instr('mul',  'x21', 'x21', 'x20');
                    $this->instr('sub',  'x0',  'x19', 'x21');
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
            $this->instr('mov', 'x0', '#0');
            $this->instr('b', $labelE);
            $this->emitLabel($labelT);
            $this->instr('mov', 'x0', '#1');
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
            $this->instr('mov', 'x0', '#0');
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
            if ($offset >= 0) {
                $type = $this->symbolTable->lookup($name)['type'] ?? 'int32';
                if ($type === 'float32') {
                    $this->instr('ldr', 's0', "[sp, #{$offset}]");
                } else {
                    $this->instr('ldr', 'x0', "[sp, #{$offset}]");
                }
            } else {
                $this->instr('mov', 'x0', '#0');
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
            $this->instr('mov', 'x0', "#$val");
            return;
        }
        if ($ctx->FLOAT_LIT() !== null) {
            $val = $ctx->FLOAT_LIT()->getText();
            // Cargar float como bits en registro general, luego mover a s0
            $bits = unpack('L', pack('f', (float)$val))[1];
            $this->instr('mov', 'w0', "#$bits");
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
            $this->instr('mov', 'x0', '#1');
            return;
        }
        if ($ctx->FALSE() !== null) {
            $this->instr('mov', 'x0', '#0');
            return;
        }
        if ($ctx->RUNE_LIT() !== null) {
            $raw  = $ctx->RUNE_LIT()->getText();
            $char = substr($raw, 1, -1);
            $val  = strlen($char) === 1 ? ord($char) : ord(stripcslashes($char));
            $this->instr('mov', 'x0', "#$val");
            return;
        }
        if ($ctx->arrayLiteral() !== null) {
            $this->visit($ctx->arrayLiteral());
        }
    }


    public function visitArrayLiteral($ctx): void{
        $this->instr('mov', 'x0', '#0'); // tamaño del arreglo
    }

    // Built ins funciones enbebidas ------------------------------------------------------------

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


    // HELPERS ------------------------------------------------------------

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

    // FUNCIONES AUXILIARES DE RUNTIME ---------------------------------------------

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
        $this->instr('sub', 'sp', 'sp', '#32');
        $this->instr('stp', 'x29, x30', '[sp, #0]');
        $this->instr('mov', 'x29', 'sp');

        // Buffer de 20 bytes en stack
        $this->instr('add', 'x9', 'sp', '#16');  // buffer
        $this->instr('mov', 'x10', '#0');          // longitud
        $this->instr('mov', 'x11', 'x0');          // número

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
        $this->instr('add', 'x13', 'sp', '#36'); // puntero al final del buffer temp
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
        $this->instr('add', 'x1', 'sp', '#16');  // dirección buffer
        $this->instr('mov', 'x2', 'x10');          // longitud
        $this->instr('mov', 'x0', '#1');            // stdout
        $this->instr('mov', 'x8', '#64');           // write
        $this->instr('svc', '#0');

        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#32');
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
        $this->emit('');
        $this->comment('__print_bool: imprime x0 como true/false');
        $this->emit('__print_bool:');
        $this->instr('sub', 'sp', 'sp', '#16');
        $this->instr('stp', 'x29, x30', '[sp, #0]');

        $trueLabel  = $this->getStringLabel('true');
        $falseLabel = $this->getStringLabel('false');
        $labelT = $this->newLabel('pb_true');
        $labelE = $this->newLabel('pb_end');

        $this->instr('cmp', 'x0', '#0');
        $this->instr('b.ne', $labelT);

        // false
        $this->instr('adrp', 'x1', $falseLabel);
        $this->instr('add',  'x1', 'x1', ":lo12:{$falseLabel}");
        $this->instr('mov',  'x2', '#5');
        $this->instr('b', $labelE);

        $this->emitLabel($labelT);
        $this->instr('adrp', 'x1', $trueLabel);
        $this->instr('add',  'x1', 'x1', ":lo12:{$trueLabel}");
        $this->instr('mov',  'x2', '#4');

        $this->emitLabel($labelE);
        $this->instr('mov', 'x0', '#1');
        $this->instr('mov', 'x8', '#64');
        $this->instr('svc', '#0');

        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#16');
        $this->instr('ret');
    }


    private function emitPrintStrHelper(): void {
        $this->emit('');
        $this->comment('__print_str: imprime string en x0 (hasta null o longitud conocida)');
        $this->emit('__print_str:');
        $this->instr('sub', 'sp', 'sp', '#16');
        $this->instr('stp', 'x29, x30', '[sp, #0]');

        // Calcular strlen
        $this->instr('mov', 'x1', 'x0');
        $this->instr('mov', 'x2', '#0');
        $labelStrLen = $this->newLabel('strlen_loop');
        $this->emitLabel($labelStrLen);
        $this->instr('ldrb', 'w3', '[x1, x2]');
        $this->instr('cbz',  'w3', '__print_str_write');
        $this->instr('add',  'x2', 'x2', '#1');
        $this->instr('b', $labelStrLen);

        $this->emit('__print_str_write:');
        $this->instr('mov', 'x0', '#1');
        $this->instr('mov', 'x8', '#64');
        $this->instr('svc', '#0');

        $this->instr('ldp', 'x29, x30', '[sp, #0]');
        $this->instr('add', 'sp', 'sp', '#16');
        $this->instr('ret');
    }


}

