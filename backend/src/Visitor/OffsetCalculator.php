<?php
namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;


class OffsetCalculator extends GrammarBaseVisitor {

    private SymbolTable $symbolTable;
    private string $currentFunc = '';
    private int $nextOffset = 16;
    private array $paramOffsets = []; // [funcName][paramName] => offset   // próximo offset disponible (16 = reserva x29/x30)

    public function __construct(SymbolTable $symbolTable) {
        $this->symbolTable = $symbolTable;
    }

    public function visitFuncDecl($ctx): void {
        $funcName = $ctx->ID()->getText();
        $this->currentFunc = $funcName;
        $this->nextOffset  = 16; // reservar 16 bytes para x29/x30

        // Asignar offsets a parámetros desde el nodo de la gramática
        // (no desde SymbolTable.lookup porque los params viven en scopes temporales)
        if ($ctx->paramList() !== null) {
            foreach ($ctx->paramList()->paramGroup() as $group) {
                $typeText = $group->typeLiteral() ? $group->typeLiteral()->getText() : 'int32';
                $size     = $this->symbolTable->typeSize($typeText);
                $size     = max((int)$size, 8);

                $ids = $group->ID();
                foreach ($ids as $idToken) {
                    $name = $idToken->getText();
                    // Guardar offset en SymbolTable y en mapa local
                    $this->symbolTable->setParamOffset($funcName, $name, $this->nextOffset);
                    $this->paramOffsets[$funcName][$name] = $this->nextOffset;
                    $this->nextOffset += SymbolTable::alignUp($size, 8);
                }
            }
        }

        // Visitar el bloque para asignar offsets a variables locales
        $this->visit($ctx->block());

        // frameSize total alineado a 16 bytes
        $frameSize = SymbolTable::alignUp($this->nextOffset, 16);
        $this->symbolTable->setFunctionFrameSize($funcName, $frameSize);

        $this->currentFunc = '';
    }

    public function visitVarDecl($ctx): void {
        if ($this->currentFunc === '') return; // global — sin offset

        $ids = $ctx->idList() !== null ? $ctx->idList()->ID() : [];

        $typeText = 'unknown';
        if ($ctx->typeLiteral()      !== null) $typeText = $ctx->typeLiteral()->getText();
        if ($ctx->arrayTypeLiteral() !== null) $typeText = $ctx->arrayTypeLiteral()->getText();

        foreach ($ids as $idToken) {
            $name = $idToken->getText();
            $size = $this->symbolTable->typeSize($typeText);
            $size = max($size, 8);
            $this->symbolTable->setOffset($name, (int)$this->nextOffset);
            $this->nextOffset += SymbolTable::alignUp((int)$size, 8);
        }
    }

    public function visitShortVarDecl($ctx): void {
        if ($this->currentFunc === '') return;

        $ids = $ctx->idList()->ID();
        foreach ($ids as $idToken) {
            $name    = $idToken->getText();
            $info    = $this->symbolTable->lookup($name);
            $current = $info !== null ? $this->symbolTable->getOffset($name) : -1;

            if ($info !== null && $current === -1) {
                // Variable nueva — asignar offset
                $type = $info['type'] ?? 'int32';
                $size = $this->symbolTable->typeSize($type);
                $size = max((int)$size, 8);
                $this->symbolTable->setOffset($name, (int)$this->nextOffset);
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

    /** Retorna el offset de un parámetro de función (para CodeGenerator). */
    public function getParamOffset(string $funcName, string $paramName): int {
        return $this->paramOffsets[$funcName][$paramName] ?? -1;
    }

    /** Retorna todos los offsets de parámetros de una función. */
    public function getParamOffsets(string $funcName): array {
        return $this->paramOffsets[$funcName] ?? [];
    }

    // No abrir scopes — solo necesitamos recorrer para encontrar declaraciones
    public function visitBlock($ctx): void {
        $this->visitChildren($ctx);
    }
}