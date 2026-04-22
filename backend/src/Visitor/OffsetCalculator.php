<?php
namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;

class OffsetCalculator extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private string $currentFunction = '';
    private int $currentOffset = 0;

    public function __construct(SymbolTable $symbolTable) {
        $this->symbolTable = $symbolTable;
    }

    public function visitFunctionDecl($ctx): void {
        $funcName = $ctx->ID()->getText();
        $this->currentFunction = $funcName;
        $this->nextOffset = 0; // Reiniciar offset para cada función

        if($ctx->paramList() !== null){
            foreach($ctx->paramList()->paramGroup() as $group) {
                $typeText = $group->typeLiteral() ? $group->typeLiteral()->getText() : 'unknown';
                $size = $this->symbolTable->getTypeSize($typeText);
                $size = max($size, 8); // Asegurar un mínimo de 8 bytes para alineación

                $ids = $group->ID();
                foreach($ids as $idToken) {
                    $name = $idToken->getText();
                    $this->symbolTable->setOffset($name, $this->nextOffset);
                    $this->nextOffset += SymbolTable::alignUp($size, 8); // Alinear al siguiente múltiplo de 8
                }
            }
        }

        //visitar el bloque de la función para calcular offsets de variables locales
        $this->visit($ctx->block());

        $frameSize = SymbolTable::alignUp($this->nextOffset, 16); // Alinear el tamaño total del frame a 16 bytes
        $this->symbolTable->setFunctionFrameSize($funcName, $frameSize);

        $this->currentFunction = '';
    }

    public function visitVarDecl($ctx): void{
        if($this->currentFunction === '') return; // Solo calcular offsets dentro de funciones

        $ids = $ctx->idList() !== null ? $ctx->idList()->ID() : [];

        $typeText = 'unknown';
        if($ctx->typeLiteral() !== null) {
            $typeText = $ctx->typeLiteral()->getText();
        }
        if($ctx->arrayTypeLiteral() !== null) {
            $typeText = $ctx->arrayTypeLiteral()->getText();
        }

        foreach ($ids as $idToken) {
            $name = $idToken->getText();
            $size = $this->symbolTable->typeSize($typeText);
            $size = max($size, 8);
            $this->symbolTable->setOffset($name, $this->nextOffset);
            $this->nextOffset += SymbolTable::alignUp($size, 8);
        }
    }

    public function visitShortVarDecl($ctx): void {
        if ($this->currentFunc === '') return;

        $ids = $ctx->idList()->ID();
        foreach ($ids as $idToken) {
            $name = $idToken->getText();
            if ($this->symbolTable->lookup($name) !== null
                && $this->symbolTable->getOffset($name) === -1) {
                // Variable nueva — asignar offset
                $info = $this->symbolTable->lookup($name);
                $size = $this->symbolTable->typeSize($info['type'] ?? 'unknown');
                $size = max($size, 8);
                $this->symbolTable->setOffset($name, $this->nextOffset);
                $this->nextOffset += SymbolTable::alignUp($size, 8);
            }
        }
    }

    public function visitConstDecl($ctx): void {
        if ($this->currentFunc === '') return;
        $name = $ctx->ID()->getText();
        $info = $this->symbolTable->lookup($name);
        $size = $this->symbolTable->typeSize($info['type'] ?? 'int32');
        $size = max($size, 8);
        $this->symbolTable->setOffset($name, $this->nextOffset);
        $this->nextOffset += SymbolTable::alignUp($size, 8);
    }

    public function visitBlock($ctx): void { // Visitar el bloque para calcular offsets de variables locales
        $this->visitChildren($ctx);
    }
}