<?php
namespace Visitor;

use GrammarBaseVisitor;
use Semantic\SymbolTable;

class CodeGenerator extends GrammarBaseVisitor {

    private SymbolTable $symbolTable; // instancia de la tabla de símbolos para acceder a información de variables y funciones
    private array $lines = []; // para almacenar las líneas de código ensamblador generadas
    private array $dataSection = []; // para almacenar variables y strings (sección .data)


    public function __construct(SymbolTable $symbolTable) {
        $this->symbolTable = $symbolTable;
    }

    // Método auxiliar para agregar una línea de código ensamblador a la salida
    private function emit(string $line): void {
        $this->lines[] = $line;
    }

    // Para escribir instrucciones con espacios al inicio de instrucciones
    private function instr(string $op, string ...$args): void {
        $operands = implode(', ', $args);
        $this->lines[] = "    {$op} {$operands}";
    }


    // Resultado final, ensamblador completo como string -----------------------------------------------------------
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
}

