<?php

namespace Visitor;

use GrammarBaseVisitor;
use Semantic\Environment;
use Semantic\SymbolTable;

/**
 * Señales de control de flujo.
 * Se usan excepciones ligeras para propagar return/break/continue
 * a través del call stack del visitor sin romper el árbol de llamadas.
 */
class ReturnSignal extends \Exception {
    public mixed $value;
    public function __construct(mixed $value) {
        parent::__construct();
        $this->value = $value;
    }
}

class BreakSignal    extends \Exception {}
class ContinueSignal extends \Exception {}

// ─────────────────────────────────────────────────────────────────────────────

class Interpreter extends GrammarBaseVisitor {

    private Environment $env;
    private SymbolTable $symbolTable; // para leer las funciones registradas
    private string $output = "";      // acumula todo lo que imprime fmt.Println

    public function __construct(SymbolTable $symbolTable) {
        $this->symbolTable = $symbolTable;
        $this->env = new Environment();
    }

    // ─── Programa ─────────────────────────────────────────────────────────────

    public function visitProgram($ctx) {
        // Primero visitar declaraciones globales de variables/constantes
        foreach ($ctx->globalDeclaration() as $decl) {
            if ($decl->varDecl() !== null || $decl->constDecl() !== null) {
                $this->visit($decl);
            }
        }
        // Luego ejecutar main()
        $mainFn = $this->symbolTable->getFunction('main');
        if ($mainFn === null) return null;

        foreach ($ctx->globalDeclaration() as $decl) {
            if ($decl->funcDecl() !== null
                && $decl->funcDecl()->ID()->getText() === 'main'
            ) {
                try {
                    $this->visit($decl->funcDecl()->block());
                } catch (ReturnSignal $r) {
                    // main no retorna valores, ignorar
                }
                break;
            }
        }
        return null;
    }

    // ─── Scopes de bloque ─────────────────────────────────────────────────────

    public function visitBlock($ctx) {
        $this->env->enterScope();
        foreach ($ctx->statement() as $stmt) {
            $this->visit($stmt);
        }
        $this->env->exitScope();
        return null;
    }

    // ─── Statement dispatcher ────────────────────────────────────────────────
    // ANTLR genera un StatementContext que envuelve cada sentencia.
    // Sin este método, visitChildren retorna el objeto contexto (truthy) en lugar
    // del valor real, causando que todo se evalúe como true.

    public function visitStatement($ctx) {
        return $this->visitChildren($ctx);
    }

    // ForInitialization y ForPost son wrappers — delegar al hijo real
    public function visitForInitialization($ctx) {
        return $this->visitChildren($ctx);
    }

    public function visitForPost($ctx) {
        return $this->visitChildren($ctx);
    }

    // GlobalDeclaration wrapper
    public function visitGlobalDeclaration($ctx) {
        return $this->visitChildren($ctx);
    }

    // BuiltinCall wrapper — delega al hijo específico
    public function visitBuiltinCall($ctx) {
        return $this->visitChildren($ctx);
    }

    // ─── Declaraciones de variables ───────────────────────────────────────────

    public function visitVarDecl($ctx) {

        $ids = $ctx->idList()->ID();

        // Valores por defecto según tipo
        $defaultValue = null;
        if ($ctx->typeLiteral() !== null) {
            $defaultValue = $this->defaultForType($ctx->typeLiteral()->getText());
        }
        if ($ctx->arrayTypeLiteral() !== null) {
            $defaultValue = $this->defaultForArrayType($ctx->arrayTypeLiteral());
        }

        // Evaluar expresiones de inicialización
        $values = [];
        if ($ctx->expressionList() !== null) {
            foreach ($ctx->expressionList()->expression() as $expr) {
                $val = $this->visit($expr);
                // expandir múltiple retorno
                if (is_array($val) && isset($val['__multi'])) {
                    foreach ($val['__multi'] as $v) $values[] = $v;
                } else {
                    $values[] = $val;
                }
            }
        }

        foreach ($ids as $i => $idToken) {
            $name = $idToken->getText();
            $val  = $values[$i] ?? $defaultValue;
            $this->env->declare($name, $val);
        }

        return null;
    }

    public function visitShortVarDecl($ctx) {

        $ids  = $ctx->idList()->ID();
        $values = [];

        foreach ($ctx->expressionList()->expression() as $expr) {
            $val = $this->visit($expr);
            if (is_array($val) && isset($val['__multi'])) {
                foreach ($val['__multi'] as $v) $values[] = $v;
            } else {
                $values[] = $val;
            }
        }

        foreach ($ids as $i => $idToken) {
            $name = $idToken->getText();
            $val  = $values[$i] ?? null;
            // := declara si no existe en scope actual, si no solo reasigna
            $this->env->assign($name, $val);
        }

        return null;
    }

    public function visitConstDecl($ctx) {
        $name  = $ctx->ID()->getText();
        $value = $this->visit($ctx->expression());
        $this->env->declare($name, $value);
        return null;
    }

    // ─── Asignaciones ─────────────────────────────────────────────────────────

    public function visitAssignStmt($ctx) {

        $lValues = $ctx->lValueList()->lValue();
        $op      = $ctx->assignOp()->getText();

        // Evaluar lado derecho primero
        $values = [];
        foreach ($ctx->expressionList()->expression() as $expr) {
            $val = $this->visit($expr);
            if (is_array($val) && isset($val['__multi'])) {
                foreach ($val['__multi'] as $v) $values[] = $v;
            } else {
                $values[] = $val;
            }
        }

        foreach ($lValues as $i => $lValue) {
            $newVal = $values[$i] ?? null;
            $this->assignLValue($lValue, $op, $newVal);
        }

        return null;
    }

    private function assignLValue($lValue, string $op, mixed $newVal): void {

        // caso *p (desreferencia)
        if ($lValue->STAR() !== null) {
            $ptrName = $lValue->ID()->getText();
            $ref     = $this->env->get($ptrName);
            if (is_array($ref) && isset($ref['__ref'])) {
                $target   = $ref['__ref'];
                $current  = $this->env->get($target);
                $computed = $this->applyOp($op, $current, $newVal);
                $this->env->assign($target, $computed);
            }
            return;
        }

        $name    = $lValue->ID()->getText();
        $indices = $lValue->expression(); // acceso a arreglo

        if ($indices !== null && count($indices) > 0) {
            $arr = $this->env->get($name);
            $arr = $this->setArrayValue($arr, $indices, 0, $op, $newVal);
            $this->env->assign($name, $arr);
        } else {
            $current  = $this->env->get($name);
            $computed = $this->applyOp($op, $current, $newVal);
            $this->env->assign($name, $computed);
        }
    }

    private function applyOp(string $op, mixed $current, mixed $newVal): mixed {
        return match($op) {
            '='  => $newVal,
            '+=' => $current + $newVal,
            '-=' => $current - $newVal,
            '*=' => $current * $newVal,
            '/=' => $newVal != 0 ? intdiv((int)$current, (int)$newVal) : 0,
            default => $newVal,
        };
    }

    private function setArrayValue(mixed $arr, array $indices, int $depth, string $op, mixed $newVal): mixed {
        $idx = $this->visit($indices[$depth]);
        if ($depth === count($indices) - 1) {
            $current     = $arr[$idx] ?? null;
            $arr[$idx]   = $this->applyOp($op, $current, $newVal);
        } else {
            $arr[$idx] = $this->setArrayValue($arr[$idx], $indices, $depth + 1, $op, $newVal);
        }
        return $arr;
    }

    public function visitIncDecStmt($ctx) {

        $lValue = $ctx->lValue();
        $isInc  = $ctx->INC() !== null;

        $name    = $lValue->ID()->getText();
        $indices = $lValue->expression();

        if ($indices !== null && count($indices) > 0) {
            $arr = $this->env->get($name);
            $arr = $this->setArrayValue($arr, $indices, 0, $isInc ? '+=' : '-=', 1);
            $this->env->assign($name, $arr);
        } else {
            $current = $this->env->get($name);
            $this->env->assign($name, $isInc ? $current + 1 : $current - 1);
        }

        return null;
    }

    // ─── Control de flujo ─────────────────────────────────────────────────────

    public function visitIfStmt($ctx) {

        $condition = $this->visit($ctx->expression());

        if ($condition) {
            $this->visit($ctx->block(0));
        } else {
            // else if
            if ($ctx->ifStmt() !== null) {
                $this->visit($ctx->ifStmt());
            // else
            } elseif (count($ctx->block()) > 1) {
                $this->visit($ctx->block(1));
            }
        }

        return null;
    }

    public function visitForStmt($ctx) {

        if ($ctx->forHeader() === null) {
            // for infinito
            while (true) {
                try {
                    $this->env->enterScope();
                    $this->visitBlockStatements($ctx->block());
                    $this->env->exitScope();
                } catch (BreakSignal $b) {
                    $this->env->exitScope();
                    break;
                } catch (ContinueSignal $c) {
                    $this->env->exitScope();
                    continue;
                }
            }
            return null;
        }

        $header = $ctx->forHeader();




        

        // for condición (while)
        if ($header->forInitialization() === null) {
            //$lastRun = false;
            while (true) {
                $cond = $this->visit($header->expression());
                if (!$cond) break;
                $lastRun = true;
                try {
                    $this->env->enterScope();
                    $this->visitBlockStatements($ctx->block());
                    $this->env->exitScope();
                } catch (BreakSignal $b) {
                    $this->env->exitScope();
                    break;
                } catch (ContinueSignal $c) {
                    $this->env->exitScope();
                    continue;
                }
                $cond= $this->visit($header->expression());
                 if (!$cond) break;
            }
            /*
            if($lastRun == true){ 
                try {
                    $this->env->enterScope();
                    $this->visitBlockStatements($ctx->block());
                    $this->env->exitScope();
                } catch (BreakSignal $b) {
                    $this->env->exitScope();
                } catch (ContinueSignal $c) {
                    $this->env->exitScope();
                }
            }*/

            return null;
        }

        // for clásico: init; cond; post
        $this->env->enterScope();
        $this->visit($header->forInitialization());

        while (true) {
            // condición opcional
            if ($header->expression() !== null) {
                $cond = $this->visit($header->expression());
                if (!$cond) break;
            }
            try {
                $this->env->enterScope();
                $this->visitBlockStatements($ctx->block());
                $this->env->exitScope();
            } catch (BreakSignal $b) {
                $this->env->exitScope();
                break;
            } catch (ContinueSignal $c) {
                $this->env->exitScope();
            }
            $this->visit($header->forPost());
        }

        $this->env->exitScope();
        return null;
    }

    // Ejecuta sentencias del bloque SIN abrir scope propio
    // (usado por for que ya maneja su propio scope)
    private function visitBlockStatements($blockCtx): void {
        foreach ($blockCtx->statement() as $stmt) {
            $this->visit($stmt);
        }
    }

    public function visitSwitchStatement($ctx) {

        $switchVal = $this->visit($ctx->expression());
        $matched   = false;

        foreach ($ctx->switchCase() as $case) {
            foreach ($case->expressionList()->expression() as $expr) {
                $caseVal = $this->visit($expr);
                if ($switchVal == $caseVal) {
                    $matched = true;
                    try {
                        foreach ($case->statement() as $stmt) {
                            $this->visit($stmt);
                        }
                    } catch (BreakSignal $b) {}
                    break 2;
                }
            }
        }

        if (!$matched && $ctx->defaultCase() !== null) {
            try {
                foreach ($ctx->defaultCase()->statement() as $stmt) {
                    $this->visit($stmt);
                }
            } catch (BreakSignal $b) {}
        }

        return null;
    }

    public function visitReturnStatement($ctx) {
        if ($ctx->expressionList() === null) {
            throw new ReturnSignal(null);
        }

        $values = [];
        foreach ($ctx->expressionList()->expression() as $expr) {
            $val = $this->visit($expr);
            if (is_array($val) && isset($val['__multi'])) {
                foreach ($val['__multi'] as $v) $values[] = $v;
            } else {
                $values[] = $val;
            }
        }

        if (count($values) === 1) {
            throw new ReturnSignal($values[0]);
        }

        throw new ReturnSignal(['__multi' => $values]);
    }

    public function visitBreakStmt($ctx) {
        throw new BreakSignal();
    }

    public function visitContinueStmt($ctx) {
        throw new ContinueSignal();
    }

    // ─── Llamadas a función ───────────────────────────────────────────────────

    public function visitFunctionCall($ctx) {

        $name     = $ctx->ID()->getText();
        $fnSymbol = $this->symbolTable->getFunction($name);
        if ($fnSymbol === null) return null;

        // Evaluar argumentos
        $argValues = [];
        if ($ctx->argumentList() !== null) {
            foreach ($ctx->argumentList()->argument() as $arg) {
                $argValues[] = $this->visit($arg);
            }
        }

        // Buscar el nodo funcDecl en el árbol
        return $this->callFunction($name, $argValues);
    }

    public function visitArgument($ctx) {
        // Caso: argument → AMP ID  (referencia explícita en la gramática)
        if ($ctx->AMP() !== null && $ctx->ID() !== null) {
            return ['__ref' => $ctx->ID()->getText()];
        }
        // Caso: argument → expression
        // Si la expresión es &ID (operand con AMP), visitOperand ya devuelve ['__ref' => nombre]
        return $this->visit($ctx->expression());
    }

    private function callFunction(string $name, array $argValues): mixed {

        $fnSymbol = $this->symbolTable->getFunction($name);
        if ($fnSymbol === null) return null;

        $savedEnv = $this->env;
        $this->env = new Environment();

        $params = $fnSymbol['params'] ?? [];

        // Registrar parámetros en el nuevo environment.
        // Para referencias (&var): copiar el valor actual al nuevo env con el nombre
        // del parámetro, y recordar la correspondencia para escribir de vuelta al salir.
        $refMap = []; // paramName => refName en savedEnv

        foreach ($params as $i => $param) {
            $argVal    = $argValues[$i] ?? null;
            $paramName = $param['name'];

            if (is_array($argVal) && isset($argVal['__ref']) && is_string($argVal['__ref'])) {
                // Paso por referencia: copiar valor actual del llamador al nuevo env
                $refName            = $argVal['__ref'];
                $currentVal         = $savedEnv->get($refName);
                $refMap[$paramName] = $refName;
                $this->env->declare($paramName, $currentVal);
            } else {
                $this->env->declare($paramName, $argVal);
            }
        }

        $block = $this->findFuncBlock($name);

        $returnValue = null;
        if ($block !== null) {
            try {
                $this->visit($block);
            } catch (ReturnSignal $r) {
                $returnValue = $r->value;
            }
        }

        // Copiar de vuelta los valores de parámetros por referencia al env del llamador
        foreach ($refMap as $paramName => $refName) {
            $newVal = $this->env->get($paramName);
            $savedEnv->assign($refName, $newVal);
        }

        $this->env = $savedEnv;

        return $returnValue;
    }

    private function findFuncBlock(string $name): mixed {
        // Necesitamos el árbol — lo guardamos en visitProgram
        foreach ($this->programCtx->globalDeclaration() as $decl) {
            if ($decl->funcDecl() !== null
                && $decl->funcDecl()->ID()->getText() === $name
            ) {
                return $decl->funcDecl()->block();
            }
        }
        return null;
    }

    // ─── Expresiones ──────────────────────────────────────────────────────────

    public function visitExpression($ctx) {
        // expression tiene un solo hijo: logicalOr
        return $this->visit($ctx->logicalOr());
    }

    // ExpressionList — no se visita directamente, se itera desde el padre
    // ArgumentList — idem

    public function visitLogicalOr($ctx) {
        $children = $ctx->logicalAnd();
        $result   = $this->visit($children[0]);
        if (count($children) === 1) return $result;
        $result = (bool)$result;
        for ($i = 1; $i < count($children); $i++) {
            if ($result) return true; // corto circuito
            $result = $result || (bool)$this->visit($children[$i]);
        }
        return $result;
    }

    public function visitLogicalAnd($ctx) {
        $children = $ctx->equality();
        $result   = $this->visit($children[0]);
        if (count($children) === 1) return $result;
        $result = (bool)$result;
        for ($i = 1; $i < count($children); $i++) {
            if (!$result) return false; // corto circuito
            $result = $result && (bool)$this->visit($children[$i]);
        }
        return $result;
    }

    public function visitEquality($ctx) {
        $children = $ctx->comparison();
        $result   = $this->visit($children[0]);
        if (count($children) === 1) return $result;
        for ($i = 1; $i < count($children); $i++) {
            $right = $this->visit($children[$i]);
            // operador está en posición 2*i-1 en los hijos del ctx
            // children[0] op children[1] op children[2] ...
            // getChild(0)=comp, getChild(1)=op, getChild(2)=comp ...
            $op = $ctx->getChild($i * 2 - 1)->getText();
            // nil comparado con cualquier cosa devuelve nil
            if ($result === null || $right === null) {
                $result = null;
            } else {
                $result = ($op === '==') ? ($result == $right) : ($result != $right);
            }
        }
        return $result;
    }

    public function visitComparison($ctx) {
        $children = $ctx->addition();
        $result   = $this->visit($children[0]);
        if (count($children) === 1) return $result;
        for ($i = 1; $i < count($children); $i++) {
            $right = $this->visit($children[$i]);
            $op    = $ctx->getChild($i * 2 - 1)->getText();
            $result = match($op) {
                '<'  => $result < $right,
                '<=' => $result <= $right,
                '>'  => $result > $right,
                '>=' => $result >= $right,
                default => false,
            };
        }
        return $result;
    }

    public function visitAddition($ctx) {
        $children = $ctx->multiplication();
        $result   = $this->visit($children[0]);
        if (count($children) === 1) return $result;
        for ($i = 1; $i < count($children); $i++) {
            $right = $this->visit($children[$i]);
            $op    = $ctx->getChild($i * 2 - 1)->getText();
            $result = ($op === '+') ? $result + $right : $result - $right;
        }
        return $result;
    }

    public function visitMultiplication($ctx) {
        $children = $ctx->unary();
        $result   = $this->visit($children[0]);
        if (count($children) === 1) return $result;
        for ($i = 1; $i < count($children); $i++) {
            $right = $this->visit($children[$i]);
            $op    = $ctx->getChild($i * 2 - 1)->getText();
            $result = match($op) {
                '*'  => $result * $right,
                '/'  => $right != 0 ? (is_int($result) && is_int($right) ? intdiv($result, $right) : $result / $right) : 0,
                '%'  => $right != 0 ? $result % $right : 0,
                default => $result,
            };
        }
        return $result;
    }

    public function visitUnary($ctx) {
        if ($ctx->primaryExpr() !== null) {
            return $this->visit($ctx->primaryExpr());
        }
        $val = $this->visit($ctx->unary());
        if ($ctx->NOT()   !== null) return !$val;
        if ($ctx->MINUS() !== null) return -$val;
        if ($ctx->STAR()  !== null) {
            // desreferencia *ptr
            if (is_array($val) && isset($val['__ref'])) {
                return $this->env->get($val['__ref']);
            }
            return $val;
        }
        if ($ctx->AMP() !== null) {
            // &ID — necesitamos el nombre de la variable, no su valor.
            // Bajar por unary → primaryExpr → operand → ID para obtener el nombre.
            $idName = $this->extractIdName($ctx->unary());
            if ($idName !== null) {
                return ['__ref' => $idName];
            }
            return ['__ref' => $val]; // fallback
        }
        return $val;
    }

    public function visitPrimaryExpr($ctx) {

        // indexación: primaryExpr[expression]
        if ($ctx->primaryExpr() !== null && $ctx->expression() !== null) {
            $arr = $this->visit($ctx->primaryExpr());
            $idx = $this->visit($ctx->expression());
            return $arr[$idx] ?? null;
        }

        if ($ctx->functionCall() !== null) return $this->visit($ctx->functionCall());
        if ($ctx->builtinCall()  !== null) return $this->visit($ctx->builtinCall());
        if ($ctx->operand()      !== null) return $this->visit($ctx->operand());

        return null;
    }

    public function visitOperand($ctx) {

        if ($ctx->literal()    !== null) return $this->visit($ctx->literal());
        if ($ctx->NIL()        !== null) return null;
        if ($ctx->expression() !== null) return $this->visit($ctx->expression());

        if ($ctx->AMP() !== null && $ctx->ID() !== null) {
            return ['__ref' => $ctx->ID()->getText()];
        }

        if ($ctx->STAR() !== null && $ctx->ID() !== null) {
            $ref = $this->env->get($ctx->ID()->getText());
            if (is_array($ref) && isset($ref['__ref'])) {
                return $this->env->get($ref['__ref']);
            }
            return $ref;
        }

        if ($ctx->ID() !== null) {
            return $this->env->get($ctx->ID()->getText());
        }

        return null;
    }

    // ─── Literales ────────────────────────────────────────────────────────────

    public function visitLiteral($ctx) {

        if ($ctx->INT_LIT()    !== null) return (int)$ctx->INT_LIT()->getText();
        if ($ctx->FLOAT_LIT()  !== null) return (float)$ctx->FLOAT_LIT()->getText();
        if ($ctx->TRUE()       !== null) return true;
        if ($ctx->FALSE()      !== null) return false;
        if ($ctx->RUNE_LIT()   !== null) return $this->parseRune($ctx->RUNE_LIT()->getText());
        if ($ctx->STRING_LIT() !== null) return $this->parseString($ctx->STRING_LIT()->getText());
        if ($ctx->arrayLiteral() !== null) return $this->visit($ctx->arrayLiteral());

        return null;
    }

    public function visitArrayLiteral($ctx) {
        $elements = $ctx->arrayElements();
        return $this->visit($elements);
    }

    public function visitArrayElements($ctx) {
        $result = [];
        foreach ($ctx->arrayElement() as $el) {
            $result[] = $this->visit($el);
        }
        return $result;
    }

    public function visitArrayElement($ctx) {
        // fila multidimensional: { elements }
        if ($ctx->arrayElements() !== null) {
            return $this->visit($ctx->arrayElements());
        }
        return $this->visit($ctx->expression());
    }

    // ─── Builtins ─────────────────────────────────────────────────────────────

    public function visitFmtPrintln($ctx) {

        $parts = [];

        if ($ctx->argumentList() !== null) {
            foreach ($ctx->argumentList()->argument() as $arg) {
                $val    = $this->visit($arg);
                $parts[] = $this->valueToString($val);
            }
        }

        $line = implode(" ", $parts);
        $this->output .= $line . "\n";

        return null;
    }

    public function visitLenCall($ctx) {
        $val = $this->visit($ctx->expression());
        if (is_string($val)) return strlen($val);
        if (is_array($val))  return count($val);
        return 0;
    }

    public function visitNowCall($ctx) {
        return date('Y-m-d H:i:s');
    }

    public function visitSubstrCall($ctx) {
        $str   = $this->visit($ctx->expression(0));
        $start = $this->visit($ctx->expression(1));
        $len   = $this->visit($ctx->expression(2));
        return substr((string)$str, (int)$start, (int)$len);
    }

    public function visitTypeOfCall($ctx) {
        $val = $this->visit($ctx->expression());
        return match(true) {
            is_int($val)   => "int32",
            is_float($val) => "float32",
            is_bool($val)  => "bool",
            is_string($val) => "string",
            is_null($val)  => "nil",
            is_array($val) => "array",
            default        => "unknown",
        };
    }

    public function visitCastCall($ctx) {
        $val        = $this->visit($ctx->expression());
        $targetType = strtolower($ctx->getChild(0)->getText());
        if ($targetType === 'int') $targetType = 'int32';

        return match($targetType) {
            'int32'   => (int)$val,
            'float32' => (float)$val,
            'bool'    => (bool)$val,
            'string'  => (string)$val,
            'rune'    => is_string($val) ? ord($val[0]) : (int)$val,
            default   => $val,
        };
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function defaultForType(string $type): mixed {
        return match($type) {
            'int32', 'int' => 0,
            'float32'      => 0.0,
            'bool'         => false,
            'string'       => "",
            'rune'         => 0,
            default        => null,
        };
    }

    private function defaultForArrayType($ctx): array {
        // Construir arreglo multidimensional con ceros
        $dimensions = $ctx->expression();
        $baseType   = $ctx->typeLiteral()->getText();
        return $this->buildArray($dimensions, 0, $baseType);
    }

    private function buildArray(array $dimensions, int $depth, string $baseType): array {
        $size = (int)$this->visit($dimensions[$depth]);
        $arr  = [];
        for ($i = 0; $i < $size; $i++) {
            if ($depth < count($dimensions) - 1) {
                $arr[] = $this->buildArray($dimensions, $depth + 1, $baseType);
            } else {
                $arr[] = $this->defaultForType($baseType);
            }
        }
        return $arr;
    }

    private function valueToString(mixed $val): string {
        if (is_bool($val))   return $val ? 'true' : 'false';
        if (is_null($val))   return 'nil';
        if (is_array($val))  return $this->arrayToString($val);
        if (is_string($val)) return $val;
        // rune: mostrar como carácter si es entero en rango ASCII imprimible
        return (string)$val;
    }

    private function arrayToString(array $arr): string {
        return '[' . implode(' ', array_map([$this, 'valueToString'], $arr)) . ']';
    }

    private function parseRune(string $lit): int {
        // 'A' -> 65
        $inner = substr($lit, 1, -1);
        if ($inner[0] === '\\') {
            return match($inner) {
                '\\n'  => 10,
                '\\t'  => 9,
                '\\r'  => 13,
                '\\\\' => 92,
                "\\'"  => 39,
                '\\"'  => 34,
                '\\0'  => 0,
                default => ord($inner[1]),
            };
        }
        return ord($inner[0]);
    }

    private function parseString(string $lit): string {
        // Quitar comillas y procesar escapes
        $inner = substr($lit, 1, -1);
        return stripcslashes($inner);
    }

    // Extrae el nombre de la variable de un nodo unary → primaryExpr → operand → ID
    private function extractIdName($ctx): ?string {
        if ($ctx === null) return null;
        // unary → primaryExpr
        if (method_exists($ctx, 'primaryExpr') && $ctx->primaryExpr() !== null) {
            return $this->extractIdName($ctx->primaryExpr());
        }
        // primaryExpr → operand
        if (method_exists($ctx, 'operand') && $ctx->operand() !== null) {
            return $this->extractIdName($ctx->operand());
        }
        // operand → ID
        if (method_exists($ctx, 'ID') && $ctx->ID() !== null) {
            return $ctx->ID()->getText();
        }
        // unary → unary (recursivo)
        if (method_exists($ctx, 'unary') && $ctx->unary() !== null) {
            return $this->extractIdName($ctx->unary());
        }
        return null;
    }

    // ─── Getter de salida ────────────────────────────────────────────────────
    public function getEnv(): \Semantic\Environment
    {
        return $this->env;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    // ─── Punto de entrada externo ─────────────────────────────────────────────

    // Guardamos el ctx del programa para que findFuncBlock pueda buscarlo
    private mixed $programCtx = null;

    public function run($tree): void {
        $this->programCtx = $tree;
        $this->visitProgram($tree);
    }
}