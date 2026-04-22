<?php

namespace Visitor;

use Semantic\SymbolTable;
use Error\CompilerError;
use Error\ErrorReporter;
use Context\FuncDeclContext;

class FunctionCollector extends \GrammarBaseVisitor {

    private ErrorReporter $errorReporter;
    private SymbolTable $symbolTable;

    public function __construct(SymbolTable $symbolTable, ErrorReporter $errorReporter) {
        $this->symbolTable = $symbolTable;
        $this->errorReporter = $errorReporter;
    }

    public function visitFuncDecl($ctx) {

        $functionName = $ctx->ID()->getText();

        $returnType = "void";
        if ($ctx->returnType() !== null) {
            $returnType = $ctx->returnType()->getText();
        }

        // Validaciones de main — se ejecutan SIEMPRE, incluso si main es duplicada.
        if ($functionName === "main") {

            $hasParams = $ctx->paramList() !== null
                && count($ctx->paramList()->paramGroup()) > 0;

            if ($hasParams) {
                $this->errorReporter->add(new CompilerError(
                    "Error Semántico",
                    "La función 'main' no puede recibir parámetros.",
                    $ctx->start->getLine(),
                    $ctx->start->getCharPositionInLine()
                ));
            }

            if ($returnType !== "void") {
                $this->errorReporter->add(new CompilerError(
                    "Error Semántico",
                    "La función 'main' no puede retornar valores (tipo declarado: '$returnType').",
                    $ctx->start->getLine(),
                    $ctx->start->getCharPositionInLine()
                ));
            }
        }

        if ($this->symbolTable->getFunction($functionName) !== null) {
            $this->errorReporter->add(new CompilerError(
                "Error Semántico",
                "La función '$functionName' ya fue declarada.",
                $ctx->start->getLine(),
                $ctx->start->getCharPositionInLine()
            ));
        } else {
 
            $params = [];

            if ($ctx->paramList() !== null) {
                foreach ($ctx->paramList()->paramGroup() as $group) {
                    $typeNode = $group->typeLiteral();
                    $type = $typeNode ? $typeNode->getText() : "unknown";
                    foreach ($group->ID() as $idToken) {
                        $params[] = ['name' => $idToken->getText(), 'type' => $type];
                    }
                }
            }

            $this->symbolTable->addFunction($functionName, $returnType, $params);
        }

        return $this->visitChildren($ctx);
    }

    // VALIDACIÓN 2 — main no puede llamarse explícitamente.
    public function visitFunctionCall($ctx) {

        $name = $ctx->ID()->getText();

        if ($name === "main") {
            $this->errorReporter->add(new CompilerError(
                "Error Semántico",
                "La función 'main' no puede ser invocada explícitamente desde el código.",
                $ctx->start->getLine(),
                $ctx->start->getCharPositionInLine()
            ));
        }

        return $this->visitChildren($ctx);
    }

    public function hasMainFunction(): bool {

        if ($this->symbolTable->hasFunction('main')) {
            //echo "Funcion main encontrada\n";
            return true;
        }

        return false;
    }
}