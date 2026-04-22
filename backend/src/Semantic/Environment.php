<?php

namespace Semantic;

/**
 * Environment — tabla de valores en tiempo de ejecución.
 *
 * Separada de SymbolTable (que guarda tipos para análisis semántico).
 * Maneja scopes como una pila: cada llamada a función o bloque
 * empuja un scope nuevo y lo descarta al salir.
 */
class Environment {

    private array $scopes = [];

    public function __construct() {
        $this->enterScope(); // scope global
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function enterScope(): void {
        $this->scopes[] = [];
    }

    public function exitScope(): void {
        if (count($this->scopes) > 1) {
            array_pop($this->scopes);
        }
    }

    // ─── Variables ────────────────────────────────────────────────────────────

    /**
     * Declara una variable nueva en el scope actual con un valor inicial.
     */
    public function declare(string $name, mixed $value): void {
        $idx = count($this->scopes) - 1;
        $this->scopes[$idx][$name] = $value;
    }

    /**
     * Asigna un nuevo valor a una variable ya declarada (busca en toda la pila).
     * Si no existe, la declara en el scope actual (comportamiento de :=).
     */
    public function assign(string $name, mixed $value): void {
        for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
            if (array_key_exists($name, $this->scopes[$i])) {
                $this->scopes[$i][$name] = $value;
                return;
            }
        }
        // No existe aún — declarar en scope actual
        $this->declare($name, $value);
    }

    /**
     * Obtiene el valor de una variable buscando desde el scope más interno.
     */
    public function get(string $name): mixed {
        for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
            if (array_key_exists($name, $this->scopes[$i])) {
                return $this->scopes[$i][$name];
            }
        }
        return null;
    }

    /**
     * Verifica si una variable existe en algún scope.
     */
    public function has(string $name): bool {
        for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
            if (array_key_exists($name, $this->scopes[$i])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Devuelve todos los valores del scope global (para reporte final).
     */
    public function getGlobalValues(): array
    {
        return $this->scopes[0] ?? [];
    }

    /**
     * Asigna por referencia: actualiza el valor apuntado por un puntero.
     * $ptrName es el nombre de la variable que contiene la referencia.
     */
    public function assignByRef(string $ptrName, mixed $value): void {
        $ref = $this->get($ptrName);
        if (is_array($ref) && isset($ref['__ref'])) {
            $this->assign($ref['__ref'], $value);
        }
    }
}