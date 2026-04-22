<?php

namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;
use Error\ErrorReporter;
use Error\CompilerError;

class AssignmentChecker extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private ErrorReporter $errorReporter;

    public function __construct(SymbolTable $symbolTable, ErrorReporter $errorReporter) {
        $this->symbolTable = $symbolTable;
        $this->errorReporter = $errorReporter;
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function visitBlock($ctx) {

        $this->symbolTable->enterScope();

        $result = $this->visitChildren($ctx);

        $this->symbolTable->exitScope();

        return $result;
    }

    public function visitFuncDecl($ctx) {

        $this->symbolTable->enterScope();

        if ($ctx->paramList() !== null) {
            foreach ($ctx->paramList()->paramGroup() as $group) {
                $typeNode = $group->typeLiteral();
                $type = $typeNode ? $typeNode->getText() : "unknown";
                foreach ($group->ID() as $idToken) {
                    $this->symbolTable->addVariable($idToken->getText(), $type, "var");
                }
            }
        }

        $result = $this->visitChildren($ctx);

        $this->symbolTable->exitScope();

        return $result;
    }

    // ─── Validación 3 — constantes no reasignables ───────────────────────────

    public function visitAssignStmt($ctx) {


        foreach ($ctx->lValueList()->lValue() as $lValue) {

            $idNode = $lValue->ID();

            if ($idNode === null) {
                continue;
            }

            $name   = $idNode->getText();
            $symbol = $this->symbolTable->lookup($name);

            if ($symbol === null) {
                $token = $idNode->getSymbol();
                $this->errorReporter->add(new CompilerError(
                    "Error Semántico",
                    "La variable '$name' no está declarada.",
                    $token->getLine(),
                    $token->getCharPositionInLine()
                ));
                continue;
            }

            if ($symbol["kind"] === "const") {
                $token = $idNode->getSymbol();
                $this->errorReporter->add(new CompilerError(
                    "Error Semántico",
                    "No se puede modificar la constante '$name'.",
                    $token->getLine(),
                    $token->getCharPositionInLine()
                ));
            }
        }

        return $this->visitChildren($ctx);
    }

    public function visitIncDecStmt($ctx) {

        $idNode = $ctx->lValue()->ID();

        if ($idNode === null) {
            return $this->visitChildren($ctx);
        }

        $name   = $idNode->getText();
        $symbol = $this->symbolTable->lookup($name);

        if ($symbol !== null && $symbol["kind"] === "const") {
            $token = $idNode->getSymbol();
            $this->errorReporter->add(new CompilerError(
                "Error Semántico",
                "No se puede modificar la constante '$name' con ++ / --.",
                $token->getLine(),
                $token->getCharPositionInLine()
            ));
        }

        return $this->visitChildren($ctx);
    }

    // ─── Validación de lvalues ────────────────────────────────────────────────

    public function visitLValue($ctx) {

        if ($ctx->ID() !== null) {
            $name = $ctx->ID()->getText();
            if (!$this->symbolTable->hasVariable($name)) {
                $token = $ctx->ID()->getSymbol();
                $this->errorReporter->add(new CompilerError(
                    "Error Semántico",
                    "La variable '$name' no está declarada.",
                    $token->getLine(),
                    $token->getCharPositionInLine()
                ));
            }
        }

        return $this->visitChildren($ctx);
    }

    // ─── Validación de uso de identificadores en expresiones ─────────────────

    public function visitOperand($ctx) {

        $idNode = $ctx->ID();

        if ($idNode !== null) {

            $name = $idNode->getText();

            $isVariable = $this->symbolTable->hasVariable($name);
            $isFunction = $this->symbolTable->getFunction($name) !== null;

            if (!$isVariable && !$isFunction) {
                $token = $idNode->getSymbol();
                $this->errorReporter->add(new CompilerError(
                    "Error Semántico",
                    "El identificador '$name' no está declarado.",
                    $token->getLine(),
                    $token->getCharPositionInLine()
                ));
            }
        }

        return $this->visitChildren($ctx);
    }
}