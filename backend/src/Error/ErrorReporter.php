<?php

namespace Error;

class ErrorReporter {
    
    private array $errors = [];

    public function add(CompilerError $error): void { // Agrega un error al reporte
        $this->errors[] = $error;
    }

    public function hasErrors(): bool { // Verifica si hay errores registrados
        return !empty($this->errors);
    }

    public function getErrors(): array { // Obtiene la lista de errores registrados
        return $this->errors;
    }
}