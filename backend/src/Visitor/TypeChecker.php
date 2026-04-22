<?php

namespace Visitor;

use GrammarBaseVisitor;
use GrammarParser;
use Semantic\SymbolTable;
use Error\ErrorReporter;
use Error\CompilerError;

use Context\LiteralContext;
use Context\AssignmentContext;
use Context\AddSubExprContext;
use Context\IdentifierExprContext;
use Context\ReturnStatementContext;
use Context\FuncDeclContext;
use Context\AssignStmtContext;
use Context\AdditionContext;
use Context\MultiplicationContext;
use Context\ComparisonContext;
use Context\LogicalAndContext;
use Context\PrimaryContext;


class TypeChecker extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private ErrorReporter $errorReporter;
    private string|array $currentFunctionReturn = "void";

    // fullPass = true  → pasada completa, reporta errores de args y return-en-void
    // fullPass = false → helper de inferencia para VariableCollector, no reporta esos errores
    private bool $fullPass = false;

    public function __construct(SymbolTable $symbolTable, ErrorReporter $errorReporter) {
        $this->symbolTable = $symbolTable;
        $this->errorReporter = $errorReporter;
    }

    public function setFullPass(bool $value): void {
        $this->fullPass = $value;
    }


    // COMPATIBILIDAD DE TIPOS
    private function compatible(string $a, string $b): bool {

        if ($a === $b) {
            return true;
        }

        // tipos numéricos compatibles entre sí
        if ($this->isNumeric($a) && $this->isNumeric($b)) {
            return true;
        }

        // nil compatible con punteros
        if ($a === "nil" && str_starts_with($b, "*")) {
            return true;
        }

        if ($b === "nil" && str_starts_with($a, "*")) {
            return true;
        }

        // arrays compatibles entre sí (el tipo exacto lo garantiza el análisis previo)
        if (str_starts_with($a, "[") && str_starts_with($b, "[")) {
            return true;
        }

        return false;
    }

    // __________________________ VISIT METHODS ___________________________________________________________________________________

    // ASIGNACIONES
    public function visitAssignStmt(AssignStmtContext $ctx) {


        $lValues     = $ctx->lValueList()->lValue();
        $expressions = $ctx->expressionList()->expression();

        // ── Construir lista plana de tipos del lado derecho ───────────────────
        // Maneja tanto el caso normal como el de múltiple retorno "(int32,int32)"
        $rightTypes = [];

        foreach ($expressions as $expr) {

            $t = $this->visit($expr);

            if (is_string($t)
                && str_starts_with($t, "(")
                && str_ends_with($t, ")")
            ) {
                // múltiple retorno: expandir
                $inner = trim($t, "()");
                foreach (explode(",", $inner) as $p) {
                    $rightTypes[] = $this->baseType(trim($p));
                }
            } elseif (is_array($t)) {
                foreach ($t as $rt) {
                    $rightTypes[] = $this->baseType($rt);
                }
            } else {
                $rightTypes[] = $this->baseType($t);
            }
        }

        // ── Validar cada lValue contra su tipo correspondiente ────────────────
        foreach ($lValues as $i => $leftNode) {

            $rightType = $rightTypes[$i] ?? "unknown";

            // Si el tipo derecho es desconocido o erróneo, omitir
            // (el error ya fue reportado donde se originó)
            if ($rightType === "unknown" || $rightType === "error") {
                continue;
            }

            $leftType = $this->visit($leftNode);
            $leftType = $this->baseType($leftType);


            if ($leftType === "unknown" || $leftType === "error") {
                continue;
            }

            if (!$this->compatible($leftType, $rightType)) {
                $this->errorReporter->add(
                    new CompilerError(
                        "Error de Tipo",
                        "No se puede asignar '$rightType' a '$leftType'",
                        $ctx->getStart()->getLine(),
                        $ctx->getStart()->getCharPositionInLine()
                    )
                );
            }
        }

        return null;
    }


    public function visitLValue($ctx) {

        // caso: *p
        if ($ctx->STAR() !== null) {

            $type = $this->getIdentifierType($ctx->ID());

            if (!str_starts_with($type, "*")) {
                return "error";
            }

            return substr($type, 1);
        }

        // caso: ID o ID[index]
        $type = $this->getIdentifierType($ctx->ID());

        $indices = $ctx->expression();

        if ($indices === null || count($indices) === 0) {
            return $type;
        }

        foreach ($indices as $expr) {

            $indexType = $this->visit($expr);

            if ($indexType !== "int" && $indexType !== "int32") {
                return "error";
            }

            // diferenciar punteros
            while (str_starts_with($type, "*")) {
                $type = substr($type, 1);
            }

            // remover una dimensión del array
            if (preg_match('/^\[[^\]]+\](.+)$/', $type, $m)) {
                $type = $m[1];
            } else {
                return "error";
            }
        }

        return $type;
    }


    public function visitArrayLiteral($ctx) {

        $type = $this->visit($ctx->arrayTypeLiteral());


        return $type;
    }

    public function visitArrayTypeLiteral($ctx) {

        $dimensions = $ctx->expression();
        $baseType = $ctx->typeLiteral()->getText();

        $type = "";

        foreach ($dimensions as $dim) {
            $size = $dim->getText();
            $type .= "[$size]";
        }

        return $type . $baseType;
    }

    // VALIDACION DE TIPOS EN EXPRESIONES ARITMETICAS
    public function visitAddition($ctx) {

        return $this->checkBinaryChain($ctx, $ctx->multiplication(), "numeric");

    }

    public function visitMultiplication($ctx) {

        return $this->checkBinaryChain($ctx, $ctx->unary(), "numeric");

    }

    // VALIDACION DE TIPOS EN EXPRESIONES LOGICAS
    public function visitComparison($ctx) {

        return $this->checkBinaryChain($ctx, $ctx->addition(), "bool");

    }


    // AND LÓGICO
    public function visitLogicalAnd($ctx) {

        return $this->checkBinaryChain($ctx, $ctx->equality(), "bool");

    }

    // IGUALDAD
    public function visitEquality($ctx) { // == y !=

        return $this->checkBinaryChain($ctx, $ctx->comparison(), "bool");

    }

    // OR LÓGICO
    public function visitLogicalOr($ctx) {

        return $this->checkBinaryChain($ctx, $ctx->logicalAnd(), "bool");

    }

    // DECLARACIONES CORTAS
    public function visitNilLiteral($ctx) {
        return "nil";
    }



    // LITERALES

    public function visitLiteral(LiteralContext $ctx) {

        if ($ctx->INT_LIT() !== null) {
            return "int32";
        }

        if ($ctx->FLOAT_LIT() !== null) {
            return "float32";
        }

        if ($ctx->STRING_LIT() !== null) {
            return "string";
        }

        if ($ctx->RUNE_LIT() !== null) {
            return "rune";
        }

        if ($ctx->TRUE() !== null || $ctx->FALSE() !== null) {
            return "bool";
        }

        if ($ctx->arrayLiteral() !== null) {
            return $this->visit($ctx->arrayLiteral());
        }

        return "error";
    }

    // IDENTIFICADORES
    public function visitIdentifierExpr(IdentifierExprContext $ctx) {

        return $this->getIdentifierType($ctx->ID());

    }


    // RETURN
    public function visitReturnStatement(ReturnStatementContext $ctx) {

        if ($ctx->expressionList() === null) {
            return null;
        }

        $types = [];

        foreach ($ctx->expressionList()->expression() as $expr) {
            $types[] = $this->visit($expr);
        }

        // VALIDACIÓN 5 — return con valor en función void
        // Solo en pasada completa para evitar falsos positivos durante VariableCollector
        if ($this->fullPass && $this->currentFunctionReturn === "void") {
            $this->errorReporter->add(new CompilerError(
                "Error de Tipo",
                "La función es void y no puede retornar valores.",
                $ctx->getStart()->getLine(),
                $ctx->getStart()->getCharPositionInLine()
            ));
            return null;
        }

        // Si la función retorna un solo valor
        if (!is_array($this->currentFunctionReturn)) {

            // Skipear si algún tipo recibido es unknown/error
            $hasUnknown = array_filter($types, fn($t) => $t === "unknown" || $t === "error");
            if (!empty($hasUnknown)) {
                return null;
            }

            if (count($types) != 1 || !$this->compatible($this->currentFunctionReturn, $types[0])) {

                $this->errorReporter->add(
                    new CompilerError(
                        "Error de Tipo",
                        "Return esperaba '{$this->currentFunctionReturn}' pero recibió '".implode(",", $types)."'",
                        $ctx->getStart()->getLine(),
                        $ctx->getStart()->getCharPositionInLine()
                    )
                );
            }

            return null;
        }

        // Múltiple retorno
        foreach ($this->currentFunctionReturn as $i => $expected) {

            $received = $types[$i] ?? "unknown";

            // Skipear si el tipo recibido es unknown/error — error ya reportado en origen
            if ($received === "unknown" || $received === "error") {
                continue;
            }

            if (!$this->compatible($expected, $received)) {

                $this->errorReporter->add(
                    new CompilerError(
                        "Error de Tipo",
                        "Return esperaba '$expected' pero recibió '$received'",
                        $ctx->getStart()->getLine(),
                        $ctx->getStart()->getCharPositionInLine()
                    )
                );
            }
        }

        return null;
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────
    // TypeChecker también debe replicar la estructura de scopes de VariableCollector
    // para que getIdentifierType() encuentre las variables en lookup().

    public function visitBlock($ctx) {

        $label = $this->inferBlockLabel($ctx);
        $this->symbolTable->enterScope($label);

        $result = $this->visitChildren($ctx);

        $this->symbolTable->exitScope();

        return $result;
    }

    private function inferBlockLabel($blockCtx): string {
        $parent = $blockCtx->parentCtx ?? null;
        if ($parent === null) return 'block';
        $class = get_class($parent);
        if (str_contains($class, 'FuncDecl')) {
            $id = $parent->ID();
            return 'func:' . ($id ? $id->getText() : 'anon');
        }
        if (str_contains($class, 'ForStmt'))     return 'loop';
        if (str_contains($class, 'IfStmt'))      return 'block';
        if (str_contains($class, 'SwitchCase'))  return 'block';
        if (str_contains($class, 'DefaultCase')) return 'block';
        return 'block';
    }

    // FUNCIONES
    public function visitFuncDecl(FuncDeclContext $ctx) {

        $funcName = $ctx->ID()->getText();

        $function = $this->symbolTable->getFunction($funcName);

        if ($function === null) {
            return null;
        }

        $previousReturn = $this->currentFunctionReturn;

        // Normalizar retorno múltiple: si viene como "(int32,int32)" convertir a array
        $rawReturn = $function['returnType'];
        if (is_string($rawReturn)
            && str_starts_with($rawReturn, "(")
            && str_ends_with($rawReturn, ")")
        ) {
            $inner = trim($rawReturn, "()");
            $this->currentFunctionReturn = array_map('trim', explode(",", $inner));
        } else {
            $this->currentFunctionReturn = $rawReturn;
        }

        $this->symbolTable->enterScope("func:$funcName");

        // REGISTRAR PARÁMETROS
        if ($ctx->paramList() !== null) {

            foreach ($ctx->paramList()->paramGroup() as $group) {

                $typeNode = $group->typeLiteral();
                $type = $typeNode ? $typeNode->getText() : "unknown";

                foreach ($group->ID() as $idToken) {

                    $name = $idToken->getText();
                    $token = $idToken->getSymbol();
                    $this->symbolTable->addVariable($name, $type, "var", $token->getLine(), "func:$funcName");
                }
            }
        }

        $this->visitChildren($ctx);

        $this->symbolTable->exitScope();

        $this->currentFunctionReturn = $previousReturn;

        return null;
    }
        

    // EXPRESIONES PRIMARIAS Y UNARIAS
    public function visitUnary(\Context\UnaryContext $ctx) {

        if ($ctx->primaryExpr() !== null) {
            return $this->visit($ctx->primaryExpr());
        }

        $type = $this->visit($ctx->unary());

        if ($ctx->NOT() !== null) {

            if ($type !== "bool") {
                $this->errorReporter->add(
                    new CompilerError(
                        "Error de Tipo",
                        "Operador ! requiere bool",
                        $ctx->getStart()->getLine(),
                        $ctx->getStart()->getCharPositionInLine()
                    )
                );
            }

            return "bool";
        }

        if ($ctx->MINUS() !== null) {

            if (!$this->isNumeric($type)) {
                return "error";
            }

            return $type;
        }

        if ($ctx->STAR() !== null) {

            if (!str_starts_with($type, "*")) {
                return "error";
            }

            return substr($type, 1);
        }

        if ($ctx->AMP() !== null) {

            return "*" . $type;
        }

        return "error";
    }

    public function visitPrimaryExpr(\Context\PrimaryExprContext $ctx) {

        if ($ctx->operand() !== null) {
            return $this->visit($ctx->operand());
        }

        if ($ctx->functionCall() !== null) {
            return $this->visit($ctx->functionCall());
        }

        if ($ctx->builtinCall() !== null) {
            return $this->visit($ctx->builtinCall());
        }

        // indexación array
        if ($ctx->primaryExpr() !== null && $ctx->expression() !== null) {

            $arrayType = $this->visit($ctx->primaryExpr());
            $indexType = $this->visit($ctx->expression());


            if ($indexType !== "int" && $indexType !== "int32") {
                return "error";
            }

            // quitar la primera dimensión
            if (preg_match('/^\[\d+\](.+)$/', $arrayType, $m)) {
                return $m[1];
            }

            return "error";
        }

        return "error";
    }

    public function visitFunctionCall($ctx) {

        $name = $ctx->ID()->getText();


        // FIX: usar getFunction() que es el método real en SymbolTable
        $function = $this->symbolTable->getFunction($name);

        if ($function === null) {
            // FIX: retornar "unknown" en vez de "error" para no contaminar
            // expresiones que dependen de este llamado (ej: funciones recursivas
            // durante la primera pasada del VariableCollector)
            return "unknown";
        }


        // VALIDACIONES 6 y 7 — número y tipos de argumentos
        // Solo en pasada completa para evitar errores duplicados desde VariableCollector
        if ($this->fullPass) {
            $params = $function['params'] ?? [];
            $args   = ($ctx->argumentList() !== null)
                      ? $ctx->argumentList()->argument()
                      : [];

            // Validación 6 — número de argumentos
            if (count($args) !== count($params)) {
                $this->errorReporter->add(new CompilerError(
                    "Error Semántico",
                    "La función '$name' espera " . count($params) . " argumento(s) pero recibió " . count($args) . ".",
                    $ctx->getStart()->getLine(),
                    $ctx->getStart()->getCharPositionInLine()
                ));
            } else {
                // Validación 7 — tipos de argumentos (solo si el número es correcto)
                foreach ($args as $i => $arg) {
                    $argType      = $this->visit($arg);
                    $expectedType = $params[$i]['type'] ?? "unknown";

                    if ($argType === "unknown" || $argType === "error") {
                        continue;
                    }

                    if (!$this->compatible($expectedType, $argType)) {
                        $this->errorReporter->add(new CompilerError(
                            "Error de Tipo",
                            "Argumento " . ($i + 1) . " de '$name': se esperaba '$expectedType' pero se recibió '$argType'.",
                            $ctx->getStart()->getLine(),
                            $ctx->getStart()->getCharPositionInLine()
                        ));
                    }
                }
            }
        } else {
            // En modo inferencia: visitar argumentos sin validar
            if ($ctx->argumentList() !== null) {
                foreach ($ctx->argumentList()->argument() as $arg) {
                    $this->visit($arg);
                }
            }
        }

        $ret = $function['returnType'];

        // manejar retornos múltiples "(int32,int32)"
        if (is_string($ret) && str_starts_with($ret, "(")) {


            $inner = trim($ret, "()");
            $parts = explode(",", $inner);

            $types = array_map('trim', $parts);


            return "(" . implode(",", $types) . ")";
        }


        return $ret;
    }


    public function visitOperand(\Context\OperandContext $ctx) {

        if ($ctx->literal() !== null) {
            return $this->visit($ctx->literal());
        }

        if ($ctx->AMP() !== null && $ctx->ID() !== null) {
            return "*" . $this->getIdentifierType($ctx->ID());
        }

        if ($ctx->STAR() !== null && $ctx->ID() !== null) {

            $type = $this->getIdentifierType($ctx->ID());

            if (!str_starts_with($type, "*")) {
                return "error";
            }

            return substr($type, 1);
        }

        if ($ctx->ID() !== null) {
            return $this->getIdentifierType($ctx->ID());
        }

        if ($ctx->expression() !== null) {
            return $this->visit($ctx->expression());
        }

        if ($ctx->NIL() !== null) {
            return "nil";
        }

        return "error";
    }

    public function visitFmtPrintln($ctx) {

        if ($ctx->argumentList() !== null) {

            foreach ($ctx->argumentList()->argument() as $arg) {
                $this->visit($arg);
            }

        }

        return "void";
    }

    public function visitLenCall($ctx) {

        $type = $this->visit($ctx->expression());

        if (!preg_match('/\[\d+\].+/', $type) && $type !== "string") {

            $this->errorReporter->add(
                new CompilerError(
                    "Error de Tipo",
                    "len() solo acepta arrays o strings",
                    $ctx->getStart()->getLine(),
                    $ctx->getStart()->getCharPositionInLine()
                )
            );

            return "error";
        }

        return "int32";
    }


    public function visitNowCall($ctx) {

        return "string";

    }

    public function visitSubstrCall($ctx) {

        $strType = $this->visit($ctx->expression(0));
        $startType = $this->visit($ctx->expression(1));
        $lenType = $this->visit($ctx->expression(2));

        if ($strType !== "string" || $startType !== "int32" || $lenType !== "int32") {
            return "error";
        }

        return "string";
    }

    public function visitTypeOfCall($ctx) {

        $this->visit($ctx->expression());

        return "string";
    }

    public function visitCastCall($ctx) {

        $this->visit($ctx->expression());

        // El tipo destino es el primer token (INT32, FLOAT32, BOOL, STRING, RUNE)
        $targetType = strtolower($ctx->getChild(0)->getText());

        // Normalizar alias
        if ($targetType === 'int') $targetType = 'int32';

        return $targetType;
    }

    // _________________________ HELPERS _____________________________________________________________________________________

    // El resultado de la operación toma el tipo del operando de mayor "jerarquía"

    private function promote(string $a, string $b): string {

        if ($a === "float32" || $b === "float32") {
            return "float32";
        }

        if ($a === "int32" || $b === "int32") {
            return "int32";
        }

        if ($this->isNumeric($a) && $this->isNumeric($b)) {
            return "int32";
        }

        if ($a === "string" && $b === "string") {
            return "string";
        }

        return "error";
    }

    private function isNumeric(string $t): bool {
        return $t === "float32" || $t === "rune" || $t === "int32";
    }

    // Helper para validar cadenas de operaciones binarias (e.g. a + b + c + d)

    private function checkBinaryChain($ctx, $children, $operatorType) {

        $resultType = $this->visit($children[0]);

        if (str_starts_with($resultType, "(")) {
            return "error";
        }

        if (count($children) == 1) {
            return $resultType;
        }

        for ($i = 1; $i < count($children); $i++) {

            $right = $this->visit($children[$i]);

            if (str_starts_with($right, "(")) {
                return "error";
            }

            $resultType = $this->baseType($resultType);
            $right = $this->baseType($right);

            // FIX: si alguno es "unknown", no reportar error de tipo —
            // el error ya fue reportado en su origen. Solo propagar "unknown".
            if ($resultType === "unknown" || $right === "unknown") {
                return "unknown";
            }

            if ($resultType === "error" || $right === "error") {
                return "error";
            }

            if (!$this->compatible($resultType, $right)) {

                $this->errorReporter->add(
                    new CompilerError(
                        "Error de Tipo",
                        "Operación inválida entre '$resultType' y '$right'",
                        $ctx->getStart()->getLine(),
                        $ctx->getStart()->getCharPositionInLine()
                    )
                );

                return "error";
            }

            if ($operatorType === "bool") {
                $resultType = "bool";
            } else {
                $resultType = $this->promote($resultType, $right);
            }
        }

        return $resultType;
    }


    // FIX: retornar "unknown" cuando la variable no existe en lugar de "error".
    // "error" se reserva para incompatibilidades de tipo reales.
    // "unknown" indica que el tipo no puede determinarse (variable no declarada,
    //  primera pasada de recursión, etc.) y NO debe propagarse como error de tipo.
    private function getIdentifierType($idNode): string {

        $name = $idNode->getText();

        $symbol = $this->symbolTable->lookup($name);

        if (!$symbol) {
            return "unknown";   // era "error" — corregido
        }

        return $symbol['type'];
    }

    // Helper para normalizar tipos quitando niveles de punteros (e.g. ***int -> int)
    private function baseType(string $type): string {

        while (str_starts_with($type, "*")) {
            $type = substr($type, 1);
        }

        return $type;
    }

}