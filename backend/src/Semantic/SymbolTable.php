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
            'params'     => $params,
            'frameSize'  => 0,   // calculado por OffsetCalculator
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

    /** Actualiza el frameSize de una función (usado por OffsetCalculator). */
    public function setFunctionFrameSize(string $name, int $size): void {
        if (isset($this->scopes[0]['functions'][$name])) {
            $this->scopes[0]['functions'][$name]['frameSize'] = $size;
        }
    }

    /** Guarda el offset de stack de un parámetro de función. */
    public function setParamOffset(string $funcName, string $paramName, int $offset): void {
        if (isset($this->scopes[0]['functions'][$funcName])) {
            $this->scopes[0]['functions'][$funcName]['paramOffsets'][$paramName] = $offset;
        }
    }

    /** Retorna el offset de stack de un parámetro de función, o -1 si no existe. */
    public function getParamOffset(string $funcName, string $paramName): int {
        return $this->scopes[0]['functions'][$funcName]['paramOffsets'][$paramName] ?? -1;
    }

    public function getFunctionFrameSize(string $name): int {
        return $this->scopes[0]['functions'][$name]['frameSize'] ?? 0;
    }

    //  Variables ------------------------------------------------------------
    public function addVariable(string $name, string $type, string $kind = 'var', int $line = 0, ?string $scopeLabel = null): bool {
        $scope = &$this->currentScopeRef();
        if (isset($scope['variables'][$name])) return false;
        $scope['variables'][$name] = [
            'type'   => $type,
            'kind'   => $kind,
            'line'   => $line,
            'scope'  => $scopeLabel ?? $this->currentScopeLabel(),
            'value'  => null,
            'offset' => -1,   // asignado por OffsetCalculator, -1 = global/no asignado
            'size'   => $this->typeSize($type),
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

    /** Asigna offset de stack a una variable en el scope actual. */
    public function setOffset(string $name, int $offset): void {
        for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
            if (isset($this->scopes[$i]['variables'][$name])) {
                $this->scopes[$i]['variables'][$name]['offset'] = $offset;
                return;
            }
        }
    }

    /** Devuelve el offset de stack de una variable. */
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

    // Tamaños de tipos ----------------------------------------------
    public function typeSize(string $type): int {
        // Arreglos: [N]tipo -> N * typeSize(tipo)
        if (preg_match('/^\[(\d+)\](.+)$/', $type, $m)) {
            return (int)$m[1] * $this->typeSize($m[2]);
        }
        return match($type) {
            'int32', 'rune', 'bool' => 4,
            'float32'               => 4,
            'float64'               => 8,
            'string'                => 16, // puntero(8) + longitud(8)
            default                 => 8,  // puntero / desconocido
        };
    }

    /** Alinea un valor al múltiplo de $align más cercano hacia arriba. */
    public static function alignUp(int $value, int $align): int {
        return (int)(ceil($value / $align) * $align);
    }
}