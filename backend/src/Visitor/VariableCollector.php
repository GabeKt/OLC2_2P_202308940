<?php

namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;
use Error\ErrorReporter;
use Error\CompilerError;
use Visitor\TypeChecker;
use Semantic\VariableSymbol;
use Context\ExpressionContext;
use Context\FunctionCallContext;

class VariableCollector extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private ErrorReporter $errorReporter;
    private TypeChecker $typeChecker;

    public function __construct(SymbolTable $symbolTable, ErrorReporter $errorReporter, TypeChecker $typeChecker) {
        $this->symbolTable = $symbolTable;
        $this->errorReporter = $errorReporter;
        $this->typeChecker = $typeChecker;
    }

    // SCOPES

    public function visitBlock($ctx) {

        $label = $this->blockLabel($ctx);
        $this->symbolTable->enterScope($label);

        return $this->visitChildren($ctx);
    }

    private function blockLabel($ctx): string {
        $parent = $ctx->parentCtx ?? null;
        if ($parent === null) return 'block';
        $class = get_class($parent);
        if (str_contains($class, 'ForStmt')) return 'loop';
        if (str_contains($class, 'FuncDecl')) return 'func:' . ($parent->ID() ? $parent->ID()->getText() : 'anon');
        return 'block';
    }

    // DECLARACION DE VARIABLES NORMAL Y ARREGLOS (incluye multidimensionales)

    public function visitVarDecl($ctx) {

        $ids = [];

        if ($ctx->idList() !== null) {
            $ids = $ctx->idList()->ID();
        }

        $declaredType = "unknown";

        if ($ctx->typeLiteral() !== null) {
            $declaredType = $ctx->typeLiteral()->getText();
        }

        if ($ctx->arrayTypeLiteral() !== null) {
            $declaredType = $this->visit($ctx->arrayTypeLiteral());
        }

        // ── Inferir tipos desde las expresiones ───────────────────────────────
        $inferredTypes = [];

        if ($ctx->expressionList() !== null) {

            foreach ($ctx->expressionList()->expression() as $expr) {

                $t = $this->typeChecker->visit($expr);

                if (is_string($t)
                    && str_starts_with($t, "(")
                    && str_ends_with($t, ")")
                ) {
                    $inner = trim($t, "()");
                    foreach (explode(",", $inner) as $p) {
                        $inferredTypes[] = trim($p);
                    }
                } elseif (is_array($t)) {
                    $inferredTypes = array_merge($inferredTypes, $t);
                } else {
                    $inferredTypes[] = $t;
                }
            }
        }


        $typeIndex = 0;

        foreach ($ids as $idToken) {

            $name  = $idToken->getText();
            $token = $idToken->getSymbol();

            // Prioridad: tipo inferido de la expresión > tipo declarado explícito
            if (count($inferredTypes) > 0) {
                $varType = $inferredTypes[$typeIndex] ?? $declaredType;
            } else {
                $varType = $declaredType;
            }
            $typeIndex++;

            // Si el tipo inferido es "unknown" pero hay tipo declarado, usar el declarado
            if ($varType === "unknown" && $declaredType !== "unknown") {
                $varType = $declaredType;
            }

            $added = $this->symbolTable->addVariable($name, $varType, 'var', $token->getLine());

            if (!$added) {

                $token = $idToken->getSymbol();

                $this->errorReporter->add(
                    new CompilerError(
                        "Error Semántico",
                        "La variable '$name' ya fue declarada en este ámbito.",
                        $token->getLine(),
                        $token->getCharPositionInLine()
                    )
                );
            }
        }

        return $this->visitChildren($ctx);
    }

    // Declaración corta :=

    public function visitShortVarDecl($ctx) {

        $ids         = $ctx->idList()->ID();
        $expressions = $ctx->expressionList()->expression();

        // ── 1. Construir lista plana de tipos ─────────────────────────────────
        $types = [];

        foreach ($expressions as $expr) {

            $t = $this->typeChecker->visit($expr);

            // múltiple retorno: "(int32,int32)"
            if (is_string($t)
                && str_starts_with($t, "(")
                && str_ends_with($t, ")")
            ) {
                $inner = trim($t, "()");
                foreach (explode(",", $inner) as $p) {
                    $types[] = trim($p);
                }

            } elseif (is_array($t)) {
                $types = array_merge($types, $t);

            } else {
                $types[] = $t;
            }
        }

        // ── 2. Verificar que al menos un ID es nuevo en el scope actual ───────
        $hasNew = false;
        foreach ($ids as $idToken) {
            if (!$this->symbolTable->hasVariableInCurrentScope($idToken->getText())) {
                $hasNew = true;
                break;
            }
        }

        if (!$hasNew) {
            $token = $ids[0]->getSymbol();
            $this->errorReporter->add(
                new CompilerError(
                    "Error Semántico",
                    "En una declaración corta (:=), al menos una variable debe ser nueva.",
                    $token->getLine(),
                    $token->getCharPositionInLine()
                )
            );
        }

        // ── 3. FIX: usar $typeIndex plano — NO el $index del foreach ──────────

        $typeIndex = 0;

        foreach ($ids as $idToken) {

            $name      = $idToken->getText();
            $exprType  = $types[$typeIndex] ?? "unknown";
            $typeIndex++;

            if (!$this->symbolTable->hasVariableInCurrentScope($name)) {

                // Variable nueva — registrar en la tabla de símbolos
                $token2 = $idToken->getSymbol();
                $this->symbolTable->addVariable($name, $exprType, 'var', $token2->getLine());

            } else {


                $existingType = $this->symbolTable->lookup($name)['type'];

                if ($exprType !== "unknown"
                    && $exprType !== "error"
                    && $existingType !== $exprType
                ) {
                    $token = $idToken->getSymbol();
                    $this->errorReporter->add(
                        new CompilerError(
                            "Error de Tipo",
                            "No se puede asignar '$exprType' a '$existingType'",
                            $token->getLine(),
                            $token->getCharPositionInLine()
                        )
                    );
                }
            }
        }

        return null;
    }

    public function visitArrayTypeLiteral($ctx) {

        // tipo base (int, float32, etc)
        $baseType = $ctx->typeLiteral()->getText();

        // todas las dimensiones
        $dimensions = $ctx->expression();

        $type = "";

        foreach ($dimensions as $expr) {
            $size = $expr->getText();
            $type .= "[" . $size . "]";
        }

        return $type . $baseType;
    }

}