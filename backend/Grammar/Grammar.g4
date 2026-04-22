grammar Grammar;

program
    : globalDeclaration+ EOF
    ;

globalDeclaration
    : funcDecl
    | varDecl
    | constDecl
    ;

funcDecl
    : FUNC ID LPAREN paramList? RPAREN returnType? block
    ;

paramList
    : paramGroup (COMMA paramGroup)*
    ;

paramGroup
    : ID (COMMA ID)* typeLiteral        // var normal: a int, b int
    | STAR ID (COMMA STAR ID)* typeLiteral  // puntero: *a int
    | ID (COMMA ID)* LBRACKET INT_LIT RBRACKET typeLiteral  // arreglo: a [5]int
    | STAR ID typeLiteral               // puntero simple: *arr [5]int
    ;

returnType
    : typeLiteral
    | LPAREN typeLiteral (COMMA typeLiteral)* RPAREN
    ;

block
    : LBRACE statement* RBRACE
    ;

//Sentencias
statement
    : block
    | varDecl
    | constDecl
    | shortVarDecl
    | assignStmt
    | incDecStmt
    | ifStmt
    | forStmt
    | switchStatement
    | returnStatement
    | breakStmt
    | continueStmt
    | expression
    ;

varDecl
    : VAR idList arrayTypeLiteral (ASSIGN expressionList)?   // arreglo con o sin init
    | VAR idList typeLiteral (ASSIGN expressionList)?        // tipo simple con o sin init
    | VAR idList ASSIGN expressionList                        // inferencia de tipo con init, sin tipo declarado
    ;

shortVarDecl
    : idList DEFINE expressionList
    ;

// Declaración de constantes

constDecl
    : CONST ID typeLiteral ASSIGN expression
    ;

assignStmt
    : lValueList assignOp expressionList
    ;

lValueList
    : lValue (COMMA lValue)*
    ;


// lvalue: variable simple, acceso a arreglo o desreferencia
lValue
    : ID (LBRACKET expression RBRACKET)*   // a  |  a[0]  |  a[i][j]
    | STAR ID                              // *p  (desreferenciación)
    ;

// Operadores de asignacion 
assignOp
    : ASSIGN       // =
    | ADD_ASSIGN   // +=
    | SUB_ASSIGN   // -=
    | MUL_ASSIGN   // *=
    | DIV_ASSIGN   // /=
    ;

// incremento y decremento
incDecStmt
    : lValue (INC | DEC)
    ;


ifStmt //if statement con optional else
    : IF expression block (ELSE (ifStmt | block))?
    ;


forStmt  // for i := 0; i < 5; i++ { ... }
    : FOR forHeader? block
    ;

forHeader  //  i := 0; i < 5; i++ block
    : forInitialization PUNTOYCOM expression? PUNTOYCOM forPost  
    | expression
    ;


forInitialization // i := 0
    : shortVarDecl
    | varDecl
    | assignStmt
    | incDecStmt
    ;

forPost
    : assignStmt
    | incDecStmt
    | expression
    ;

switchStatement
    : SWITCH expression LBRACE switchCase* defaultCase? RBRACE
    ;

switchCase
    : CASE expressionList DOSPUNTOS statement*
    ;

defaultCase
    : DEFAULT DOSPUNTOS statement*
    ;

// Sentencias de transferencia

returnStatement
    : RETURN expressionList?
    ;

breakStmt
    : BREAK
    ;

continueStmt
    : CONTINUE
    ;



// expresiones

expressionList  // lista de expresiones
    : expression (COMMA expression)*
    ;

idList
    : ID (COMMA ID)*
    ;



// Jerarquia de precedencia de operaciones

expression
    : logicalOr
    ;

logicalOr
    : logicalAnd (OR logicalAnd)*
    ;

logicalAnd
    : equality (AND equality)*
    ;

equality
    : comparison ((EQ | NEQ) comparison)*
    ;

comparison
    : addition ((MENORQ | MENORIGUAL | MAYORQ | MAYORIGUAL) addition)*
    ;

addition
    : multiplication ((PLUS | MINUS) multiplication)*
    ;

multiplication
    : unary ((STAR | DIV | MOD) unary)*
    ;

unary
    : (NOT | MINUS | STAR | AMP) unary
    | primaryExpr
    ;

// Expresión primaria

primaryExpr
    : functionCall
    | builtinCall
    | primaryExpr LBRACKET expression RBRACKET
    | operand
    ;

operand
    : literal
    | ID                                                // variable
    | AMP ID                                            // &var (referencia)
    | STAR ID                                           // *ptr (desreferencia como valor)
    | LPAREN expression RPAREN                          // agrupación
    | NIL
    ;


literal
    : INT_LIT
    | FLOAT_LIT
    | RUNE_LIT
    | STRING_LIT
    | TRUE
    | FALSE
    | arrayLiteral
    ;

// [3]int{1, 2, 3}  |  [2][2]int{{1,2},{3,4}}
arrayLiteral
    : arrayTypeLiteral LBRACE arrayElements RBRACE
    ;

arrayElements
    : arrayElement (COMMA arrayElement)* COMMA?
    ;

arrayElement
    : expression
    | LBRACE arrayElements RBRACE           // fila de array multidimensional
    ;


typeLiteral
    : baseType
    | STAR typeLiteral
    | arrayTypeLiteral
;

baseType
    : INT32
    | FLOAT32
    | BOOL
    | RUNE
    | STRING
;


// Arreglo
arrayTypeLiteral
    : (LBRACKET expression RBRACKET)+ typeLiteral
    ;

// Llamadas a funciones 
functionCall
    : ID LPAREN argumentList? RPAREN
    ;

argumentList
    : argument (COMMA argument)*
    ;

argument
    : expression
    | AMP ID            // &var como argumento (paso por referencia)
    ;

// funciones embebidas (built-in)

builtinCall
    : fmtPrintln
    | lenCall
    | nowCall
    | substrCall
    | typeOfCall
    | castCall
    ;

// fmt.Println(expr, expr, ...)
fmtPrintln
    : FMT PUNTO PRINTLN LPAREN argumentList? RPAREN
    ;

// len(expr)
lenCall
    : LEN LPAREN expression RPAREN
    ;

// now()
nowCall
    : NOW LPAREN RPAREN
    ;

// substr(str, start, length)
substrCall
    : SUBSTR LPAREN expression COMMA expression COMMA expression RPAREN
    ;

// typeOf(expr)
typeOfCall
    : TYPEOF LPAREN expression RPAREN
    ;

// casteos: int32(expr), float32(expr), bool(expr), string(expr)
castCall
    : (INT32 | FLOAT32 | BOOL | STRING | RUNE) LPAREN expression RPAREN
    ;











//  PALABRAS RESERVADAS ---------------------------------------------------------------------


FUNC     : 'func' ;
VAR      : 'var' ;
CONST    : 'const' ;
IF       : 'if' ;
ELSE     : 'else' ;
FOR      : 'for' ;
SWITCH   : 'switch' ;
CASE     : 'case' ;
DEFAULT  : 'default' ;
RETURN   : 'return' ;
BREAK    : 'break' ;
CONTINUE : 'continue' ;
NIL      : 'nil' ;
TRUE     : 'true' ;
FALSE    : 'false' ;

// Tipos primitivos
INT32   : 'int32' | 'int' ;   // int es alias aceptado según ejemplos del doc
FLOAT32 : 'float32' ;
BOOL    : 'bool' ;
RUNE    : 'rune' ;
STRING  : 'string' ;

// Funciones embebidas como palabras reservadas
FMT     : 'fmt' ;
PRINTLN : 'Println' ;
LEN     : 'len' ;
NOW     : 'now' ;
SUBSTR  : 'substr' ;
TYPEOF  : 'typeOf' ;


//  OPERADORES Y SÍMBOLOS


ASSIGN      : '=' ;
DEFINE      : ':=' ;
ADD_ASSIGN  : '+=' ;
SUB_ASSIGN  : '-=' ;
MUL_ASSIGN  : '*=' ;
DIV_ASSIGN  : '/=' ;

INC : '++' ;
DEC : '--' ;

PLUS  : '+' ;
MINUS : '-' ;
STAR  : '*' ;
DIV   : '/' ;
MOD   : '%' ;
AMP   : '&' ;

EQ          : '==' ;
NEQ         : '!=' ;
MENORQ      : '<' ;
MENORIGUAL  : '<=' ;
MAYORQ      : '>' ;
MAYORIGUAL  : '>=' ;

AND : '&&' ;
OR  : '||' ;
NOT : '!' ;

LPAREN   : '(' ;
RPAREN   : ')' ;
LBRACE   : '{' ;
RBRACE   : '}' ;
LBRACKET : '[' ;
RBRACKET : ']' ;
PUNTOYCOM: ';' ;
DOSPUNTOS: ':' ;
COMMA    : ',' ;
PUNTO    : '.' ;

//  LITERALES DE DATOS


INT_LIT
    : [0-9]+
    ;

FLOAT_LIT
    : [0-9]+ '.' [0-9]*
    | '.' [0-9]+
    ;

// Carácter rune entre comillas simples: 'a'  '\n'  '\u0041'
RUNE_LIT
    : '\'' ( ~['\\\r\n]
           | '\\' [abfnrtvz\\'"]
           | '\\u' HEX_DIGIT HEX_DIGIT HEX_DIGIT HEX_DIGIT
           )
      '\''
    ;

// Cadena entre comillas dobles con secuencias de escape
STRING_LIT
    : '"' ( ~["\\\r\n]
          | '\\' [abfnrtvz\\'"]
          | '\\u' HEX_DIGIT HEX_DIGIT HEX_DIGIT HEX_DIGIT
          )*
      '"'
    ;

//  IDENTIFICADORES


ID
    : [a-zA-Z_] [a-zA-Z0-9_]*
    ;


//  COMENTARIOS Y ESPACIOS EN BLANCO ----------------------------------------------------------


LINE_COMMENT
    : '//' ~[\r\n]* -> skip
    ;

BLOCK_COMMENT
    : '/*' .*? '*/' -> skip
    ;

WS
    : [ \t\r\n]+ -> skip
    ;


fragment HEX_DIGIT : [0-9a-fA-F] ;











