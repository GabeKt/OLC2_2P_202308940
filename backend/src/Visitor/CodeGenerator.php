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


}

