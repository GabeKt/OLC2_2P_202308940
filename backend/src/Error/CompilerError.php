<?php

namespace Error;

class CompilerError {
    public function __construct(
        public string $type,
        public string $message,
        public int $line,
        public int $column
    ){}
}