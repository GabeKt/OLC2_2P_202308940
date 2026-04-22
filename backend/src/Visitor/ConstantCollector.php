<?php

namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;
use Error\ErrorReporter;
use Error\CompilerError;

class ConstantCollector extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private ErrorReporter $errorReporter;

    public function __construct(SymbolTable $symbolTable, ErrorReporter $errorReporter) {
        $this->symbolTable = $symbolTable;
        $this->errorReporter = $errorReporter;
    }

    public function visitConstDecl($ctx) {

        $idToken = $ctx->ID();
        $name = $idToken->getText();

        $type = $ctx->typeLiteral()->getText();

        $added = $this->symbolTable->addVariable($name, $type, "const");

        if (!$added) {

            $token = $idToken->getSymbol();

            $this->errorReporter->add(
                new CompilerError(
                    "Error Semántico",
                    "La constante '$name' ya fue declarada en este ámbito.",
                    $token->getLine(),
                    $token->getCharPositionInLine()
                )
            );
        }

        return $this->visitChildren($ctx);
    }

    public function visitBlock($ctx) {

        $this->symbolTable->enterScope();

        return $this->visitChildren($ctx);

    }
}