<?php
namespace Semantic;
class SymbolTable {
    private array $scopes      = [];
    private array $scopeLabels = [];

    private array $funcitionFrames = []; // Para almacenar información de funciones (nombre, tipo de retorno, parámetros)
    private string $currentFunction = '';
    private int $currentOffset = 0; // Para calcular offsets de variables locales

    public function __construct() {
        $this->enterScope('global');
    }

    // enterScope acepta label opcional — compatible con llamadas sin argumento
    public function enterScope(string $label = 'block'): void {
        $this->scopes[]      = ['variables' => [], 'functions' => []];
        $this->scopeLabels[] = $label;
    }

    public function exitScope(): void {
        if (count($this->scopes) > 1) {
            array_pop($this->scopes);
            array_pop($this->scopeLabels);
        }
    }

    private function &currentScopeRef(): array {
        return $this->scopes[count($this->scopes) - 1];
    }

    private function currentScopeLabel(): string {
        return $this->scopeLabels[count($this->scopeLabels) - 1] ?? 'global';
    }

    //  Funciones ------------------------------------------------------------
    public function addFunction(string $name, string $returnType, array $params = []): bool {
        $globalScope = &$this->scopes[0];
        if (isset($globalScope['functions'][$name])) return false;
        $globalScope['functions'][$name] = [
            'returnType' => $returnType, 
            'params' => $params,
            'frameSize' => 0, // 
            ];
        return true;
    }

    public function getFunction(string $name): ?array {
        return $this->scopes[0]['functions'][$name] ?? null;
    }

    public function hasFunction(string $name): bool {
        return isset($this->scopes[0]['functions'][$name]);
    }

    public function getFunctions(): array {
        return $this->scopes[0]['functions'] ?? [];
    }

    // Funcion auxiliar para actualizar el tamaño del frame de una función (usada al agregar variables locales)
    public function setFunctionFrameSize(string $name, int $size): void {
        if (isset($this->scopes[0]['functions'][$name])) {
            $this->scopes[0]['functions'][$name]['frameSize'] = $size;
        }
    }

    public function getFunctionFrameSize(string $name): int {
        return $this->scopes[0]['functions'][$name]['frameSize'] ?? 0;
    }

    //  Variables ------------------------------------------------------------
    // Firma extendida pero compatible: line y scopeLabel son opcionales
    public function addVariable(string $name, string $type, string $kind = 'var', int $line = 0, ?string $scopeLabel = null): bool {
        $scope = &$this->currentScopeRef();
        if (isset($scope['variables'][$name])) return false;
        $scope['variables'][$name] = [
            'type'  => $type,
            'kind'  => $kind,
            'line'  => $line,
            'scope' => $scopeLabel ?? $this->currentScopeLabel(),
            'value' => null,
            'offset'=> -1, // Offset se asignará durante la generación de código
            'size'  => $this->typeSize($type),
        ];
        return true;
    }

    public function lookup(string $name): ?array {
        for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
            if (isset($this->scopes[$i]['variables'][$name])) {
                return $this->scopes[$i]['variables'][$name];
            }
        }
        return null;
    }

    public function hasVariable(string $name): bool {
        return $this->lookup($name) !== null;
    }

    public function hasVariableInCurrentScope(string $name): bool {
        $scope = $this->currentScopeRef();
        return isset($scope['variables'][$name]);
    }

    public function setValue(string $name, mixed $value): void {
        for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
            if (isset($this->scopes[$i]['variables'][$name])) {
                $this->scopes[$i]['variables'][$name]['value'] = $value;
                return;
            }
        }
    }

    public function setOffset(string $name, int $offset): void{
        for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
            if (isset($this->scopes[$i]['variables'][$name])) {
                $this->scopes[$i]['variables'][$name]['offset'] = $offset;
                return;
            }
        }
    }

    public function getOffset(string $name): int {
        $info = $this->lookup($name);
        return $info['offset'] ?? -1;
    }

    // Devuelve variables de TODOS los scopes para el reporte final
    public function getVariables(): array {
        $all = [];
        foreach ($this->scopes as $scope) {
            foreach ($scope['variables'] as $name => $info) {
                $all[$name] = $info;
            }
        }
        return $all;
    }

    // Tamaño en bytes de tipos primitivos 

    public function typeSize(string $type): int{
        if (preg_match('/^\[(\d+)\](.+)$/', $type, $match)){
            return (int)$match[1] * $this->typeSize($match[2]);
        }

        return match($type){
            'int32', 'rune', 'bool' => 4,
            'float32' => 4,
            'float64' => 8,
            'string' => 24, // Referencia + longitud (en un sistema de 64 bits)
            default => 8,
        };
    }

    public static function alignUp(int $value, int $align): int{
        return (int)(ceil($value / $align) * $align);
    }
}