<?php

namespace Error;

use Antlr\Antlr4\Runtime\Error\Listeners\BaseErrorListener;
use Antlr\Antlr4\Runtime\Recognizer;
use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;


class SyntaxErrorListener extends BaseErrorListener {

    private ErrorReporter $errorReporter;

    public function __construct(ErrorReporter $errorReporter){
        $this->errorReporter = $errorReporter;
    }

    public function syntaxError(
        Recognizer $recognizer,
        $offendingSymbol,
        int $line,
        int $charPositionInLine,
        string $msg,
        RecognitionException $exeption = null
    ): void {

        $customMessage = "Error Sintáctico en la línea $line, columna $charPositionInLine.";

        if(preg_match("/expecting (.+)$/", $msg, $matches)){
            $expectedTokens = $matches[1];
            $customMessage .= " Se esperaba: $expectedTokens.";
        }
        else{
            $customMessage .= "Se encontro un simbolo inesperado";
        }

        $this->errorReporter->add(
        new CompilerError(
            "Error Sintáctico",
            $customMessage,
            $line,
            $charPositionInLine
        )
    );
    }

    

}