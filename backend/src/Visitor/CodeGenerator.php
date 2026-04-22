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
        if ($funcName === 'main') {
            $this->emitLabel('main');
            $this->instr('ret'); 
        }
    }

}

