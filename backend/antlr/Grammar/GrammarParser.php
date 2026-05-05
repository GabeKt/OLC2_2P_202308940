<?php

/*
 * Generated from Grammar/Grammar.g4 by ANTLR 4.13.1
 */

namespace {
	use Antlr\Antlr4\Runtime\Atn\ATN;
	use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
	use Antlr\Antlr4\Runtime\Atn\ParserATNSimulator;
	use Antlr\Antlr4\Runtime\Dfa\DFA;
	use Antlr\Antlr4\Runtime\Error\Exceptions\FailedPredicateException;
	use Antlr\Antlr4\Runtime\Error\Exceptions\NoViableAltException;
	use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
	use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;
	use Antlr\Antlr4\Runtime\RuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\TokenStream;
	use Antlr\Antlr4\Runtime\Vocabulary;
	use Antlr\Antlr4\Runtime\VocabularyImpl;
	use Antlr\Antlr4\Runtime\RuntimeMetaData;
	use Antlr\Antlr4\Runtime\Parser;

	final class GrammarParser extends Parser
	{
		public const FUNC = 1, VAR = 2, CONST = 3, IF = 4, ELSE = 5, FOR = 6, 
               SWITCH = 7, CASE = 8, DEFAULT = 9, RETURN = 10, BREAK = 11, 
               CONTINUE = 12, IN = 13, NOT_KW = 14, NIL = 15, TRUE = 16, 
               FALSE = 17, INT32 = 18, FLOAT32 = 19, BOOL = 20, RUNE = 21, 
               STRING = 22, FMT = 23, PRINTLN = 24, LEN = 25, NOW = 26, 
               SUBSTR = 27, TYPEOF = 28, ASSIGN = 29, DEFINE = 30, ADD_ASSIGN = 31, 
               SUB_ASSIGN = 32, MUL_ASSIGN = 33, DIV_ASSIGN = 34, INC = 35, 
               DEC = 36, PLUS = 37, MINUS = 38, STAR = 39, DIV = 40, MOD = 41, 
               AMP = 42, EQ = 43, NEQ = 44, MENORQ = 45, MENORIGUAL = 46, 
               MAYORQ = 47, MAYORIGUAL = 48, AND = 49, OR = 50, NOT = 51, 
               LPAREN = 52, RPAREN = 53, LBRACE = 54, RBRACE = 55, LBRACKET = 56, 
               RBRACKET = 57, PUNTOYCOM = 58, DOSPUNTOS = 59, COMMA = 60, 
               DOTDOT = 61, PUNTO = 62, INT_LIT = 63, FLOAT_LIT = 64, RUNE_LIT = 65, 
               STRING_LIT = 66, ID = 67, LINE_COMMENT = 68, BLOCK_COMMENT = 69, 
               WS = 70;

		public const RULE_program = 0, RULE_globalDeclaration = 1, RULE_funcDecl = 2, 
               RULE_paramList = 3, RULE_paramGroup = 4, RULE_returnType = 5, 
               RULE_block = 6, RULE_statement = 7, RULE_varDecl = 8, RULE_shortVarDecl = 9, 
               RULE_constDecl = 10, RULE_assignStmt = 11, RULE_lValueList = 12, 
               RULE_lValue = 13, RULE_assignOp = 14, RULE_incDecStmt = 15, 
               RULE_ifStmt = 16, RULE_forStmt = 17, RULE_forHeader = 18, 
               RULE_forInitialization = 19, RULE_forPost = 20, RULE_switchStatement = 21, 
               RULE_switchCase = 22, RULE_defaultCase = 23, RULE_returnStatement = 24, 
               RULE_breakStmt = 25, RULE_continueStmt = 26, RULE_expressionList = 27, 
               RULE_idList = 28, RULE_expression = 29, RULE_logicalOr = 30, 
               RULE_logicalAnd = 31, RULE_equality = 32, RULE_comparison = 33, 
               RULE_addition = 34, RULE_multiplication = 35, RULE_unary = 36, 
               RULE_primaryExpr = 37, RULE_operand = 38, RULE_literal = 39, 
               RULE_arrayLiteral = 40, RULE_arrayElements = 41, RULE_arrayElement = 42, 
               RULE_typeLiteral = 43, RULE_baseType = 44, RULE_arrayTypeLiteral = 45, 
               RULE_functionCall = 46, RULE_argumentList = 47, RULE_argument = 48, 
               RULE_builtinCall = 49, RULE_fmtPrintln = 50, RULE_lenCall = 51, 
               RULE_nowCall = 52, RULE_substrCall = 53, RULE_typeOfCall = 54, 
               RULE_castCall = 55;

		/**
		 * @var array<string>
		 */
		public const RULE_NAMES = [
			'program', 'globalDeclaration', 'funcDecl', 'paramList', 'paramGroup', 
			'returnType', 'block', 'statement', 'varDecl', 'shortVarDecl', 'constDecl', 
			'assignStmt', 'lValueList', 'lValue', 'assignOp', 'incDecStmt', 'ifStmt', 
			'forStmt', 'forHeader', 'forInitialization', 'forPost', 'switchStatement', 
			'switchCase', 'defaultCase', 'returnStatement', 'breakStmt', 'continueStmt', 
			'expressionList', 'idList', 'expression', 'logicalOr', 'logicalAnd', 
			'equality', 'comparison', 'addition', 'multiplication', 'unary', 'primaryExpr', 
			'operand', 'literal', 'arrayLiteral', 'arrayElements', 'arrayElement', 
			'typeLiteral', 'baseType', 'arrayTypeLiteral', 'functionCall', 'argumentList', 
			'argument', 'builtinCall', 'fmtPrintln', 'lenCall', 'nowCall', 'substrCall', 
			'typeOfCall', 'castCall'
		];

		/**
		 * @var array<string|null>
		 */
		private const LITERAL_NAMES = [
		    null, "'func'", "'var'", "'const'", "'if'", "'else'", "'for'", "'switch'", 
		    "'case'", "'default'", "'return'", "'break'", "'continue'", "'in'", 
		    "'not'", "'nil'", "'true'", "'false'", null, "'float32'", "'bool'", 
		    "'rune'", "'string'", "'fmt'", "'Println'", "'len'", "'now'", "'substr'", 
		    "'typeOf'", "'='", "':='", "'+='", "'-='", "'*='", "'/='", "'++'", 
		    "'--'", "'+'", "'-'", "'*'", "'/'", "'%'", "'&'", "'=='", "'!='", 
		    "'<'", "'<='", "'>'", "'>='", "'&&'", "'||'", "'!'", "'('", "')'", 
		    "'{'", "'}'", "'['", "']'", "';'", "':'", "','", "'..'", "'.'"
		];

		/**
		 * @var array<string>
		 */
		private const SYMBOLIC_NAMES = [
		    null, "FUNC", "VAR", "CONST", "IF", "ELSE", "FOR", "SWITCH", "CASE", 
		    "DEFAULT", "RETURN", "BREAK", "CONTINUE", "IN", "NOT_KW", "NIL", "TRUE", 
		    "FALSE", "INT32", "FLOAT32", "BOOL", "RUNE", "STRING", "FMT", "PRINTLN", 
		    "LEN", "NOW", "SUBSTR", "TYPEOF", "ASSIGN", "DEFINE", "ADD_ASSIGN", 
		    "SUB_ASSIGN", "MUL_ASSIGN", "DIV_ASSIGN", "INC", "DEC", "PLUS", "MINUS", 
		    "STAR", "DIV", "MOD", "AMP", "EQ", "NEQ", "MENORQ", "MENORIGUAL", 
		    "MAYORQ", "MAYORIGUAL", "AND", "OR", "NOT", "LPAREN", "RPAREN", "LBRACE", 
		    "RBRACE", "LBRACKET", "RBRACKET", "PUNTOYCOM", "DOSPUNTOS", "COMMA", 
		    "DOTDOT", "PUNTO", "INT_LIT", "FLOAT_LIT", "RUNE_LIT", "STRING_LIT", 
		    "ID", "LINE_COMMENT", "BLOCK_COMMENT", "WS"
		];

		private const SERIALIZED_ATN =
			[4, 1, 70, 593, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 2, 3, 7, 3, 2, 4, 
		    7, 4, 2, 5, 7, 5, 2, 6, 7, 6, 2, 7, 7, 7, 2, 8, 7, 8, 2, 9, 7, 9, 
		    2, 10, 7, 10, 2, 11, 7, 11, 2, 12, 7, 12, 2, 13, 7, 13, 2, 14, 7, 
		    14, 2, 15, 7, 15, 2, 16, 7, 16, 2, 17, 7, 17, 2, 18, 7, 18, 2, 19, 
		    7, 19, 2, 20, 7, 20, 2, 21, 7, 21, 2, 22, 7, 22, 2, 23, 7, 23, 2, 
		    24, 7, 24, 2, 25, 7, 25, 2, 26, 7, 26, 2, 27, 7, 27, 2, 28, 7, 28, 
		    2, 29, 7, 29, 2, 30, 7, 30, 2, 31, 7, 31, 2, 32, 7, 32, 2, 33, 7, 
		    33, 2, 34, 7, 34, 2, 35, 7, 35, 2, 36, 7, 36, 2, 37, 7, 37, 2, 38, 
		    7, 38, 2, 39, 7, 39, 2, 40, 7, 40, 2, 41, 7, 41, 2, 42, 7, 42, 2, 
		    43, 7, 43, 2, 44, 7, 44, 2, 45, 7, 45, 2, 46, 7, 46, 2, 47, 7, 47, 
		    2, 48, 7, 48, 2, 49, 7, 49, 2, 50, 7, 50, 2, 51, 7, 51, 2, 52, 7, 
		    52, 2, 53, 7, 53, 2, 54, 7, 54, 2, 55, 7, 55, 1, 0, 4, 0, 114, 8, 
		    0, 11, 0, 12, 0, 115, 1, 0, 1, 0, 1, 1, 1, 1, 1, 1, 3, 1, 123, 8, 
		    1, 1, 2, 1, 2, 1, 2, 1, 2, 3, 2, 129, 8, 2, 1, 2, 1, 2, 3, 2, 133, 
		    8, 2, 1, 2, 1, 2, 1, 3, 1, 3, 1, 3, 5, 3, 140, 8, 3, 10, 3, 12, 3, 
		    143, 9, 3, 1, 4, 1, 4, 1, 4, 5, 4, 148, 8, 4, 10, 4, 12, 4, 151, 9, 
		    4, 1, 4, 1, 4, 1, 4, 1, 4, 1, 4, 1, 4, 5, 4, 159, 8, 4, 10, 4, 12, 
		    4, 162, 9, 4, 1, 4, 1, 4, 1, 4, 1, 4, 5, 4, 168, 8, 4, 10, 4, 12, 
		    4, 171, 9, 4, 1, 4, 1, 4, 1, 4, 1, 4, 1, 4, 1, 4, 1, 4, 3, 4, 180, 
		    8, 4, 1, 5, 1, 5, 1, 5, 1, 5, 1, 5, 5, 5, 187, 8, 5, 10, 5, 12, 5, 
		    190, 9, 5, 1, 5, 1, 5, 3, 5, 194, 8, 5, 1, 6, 1, 6, 5, 6, 198, 8, 
		    6, 10, 6, 12, 6, 201, 9, 6, 1, 6, 1, 6, 1, 7, 1, 7, 1, 7, 1, 7, 1, 
		    7, 1, 7, 1, 7, 1, 7, 1, 7, 1, 7, 1, 7, 1, 7, 1, 7, 3, 7, 218, 8, 7, 
		    1, 8, 1, 8, 1, 8, 1, 8, 1, 8, 3, 8, 225, 8, 8, 1, 8, 1, 8, 1, 8, 1, 
		    8, 1, 8, 3, 8, 232, 8, 8, 1, 8, 1, 8, 1, 8, 1, 8, 1, 8, 3, 8, 239, 
		    8, 8, 1, 9, 1, 9, 1, 9, 1, 9, 1, 10, 1, 10, 1, 10, 1, 10, 1, 10, 1, 
		    10, 1, 11, 1, 11, 1, 11, 1, 11, 1, 12, 1, 12, 1, 12, 5, 12, 258, 8, 
		    12, 10, 12, 12, 12, 261, 9, 12, 1, 13, 1, 13, 1, 13, 1, 13, 1, 13, 
		    5, 13, 268, 8, 13, 10, 13, 12, 13, 271, 9, 13, 1, 13, 1, 13, 3, 13, 
		    275, 8, 13, 1, 14, 1, 14, 1, 15, 1, 15, 1, 15, 1, 16, 1, 16, 1, 16, 
		    1, 16, 1, 16, 1, 16, 3, 16, 288, 8, 16, 3, 16, 290, 8, 16, 1, 17, 
		    1, 17, 3, 17, 294, 8, 17, 1, 17, 1, 17, 1, 18, 1, 18, 1, 18, 3, 18, 
		    301, 8, 18, 1, 18, 1, 18, 1, 18, 1, 18, 3, 18, 307, 8, 18, 1, 19, 
		    1, 19, 1, 19, 1, 19, 3, 19, 313, 8, 19, 1, 20, 1, 20, 1, 20, 3, 20, 
		    318, 8, 20, 1, 21, 1, 21, 1, 21, 1, 21, 5, 21, 324, 8, 21, 10, 21, 
		    12, 21, 327, 9, 21, 1, 21, 3, 21, 330, 8, 21, 1, 21, 1, 21, 1, 22, 
		    1, 22, 1, 22, 1, 22, 5, 22, 338, 8, 22, 10, 22, 12, 22, 341, 9, 22, 
		    1, 23, 1, 23, 1, 23, 5, 23, 346, 8, 23, 10, 23, 12, 23, 349, 9, 23, 
		    1, 24, 1, 24, 3, 24, 353, 8, 24, 1, 25, 1, 25, 1, 26, 1, 26, 1, 27, 
		    1, 27, 1, 27, 5, 27, 362, 8, 27, 10, 27, 12, 27, 365, 9, 27, 1, 28, 
		    1, 28, 1, 28, 5, 28, 370, 8, 28, 10, 28, 12, 28, 373, 9, 28, 1, 29, 
		    1, 29, 1, 30, 1, 30, 1, 30, 5, 30, 380, 8, 30, 10, 30, 12, 30, 383, 
		    9, 30, 1, 31, 1, 31, 1, 31, 5, 31, 388, 8, 31, 10, 31, 12, 31, 391, 
		    9, 31, 1, 32, 1, 32, 1, 32, 5, 32, 396, 8, 32, 10, 32, 12, 32, 399, 
		    9, 32, 1, 33, 1, 33, 1, 33, 5, 33, 404, 8, 33, 10, 33, 12, 33, 407, 
		    9, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 
		    33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 1, 33, 3, 33, 
		    426, 8, 33, 1, 34, 1, 34, 1, 34, 5, 34, 431, 8, 34, 10, 34, 12, 34, 
		    434, 9, 34, 1, 35, 1, 35, 1, 35, 5, 35, 439, 8, 35, 10, 35, 12, 35, 
		    442, 9, 35, 1, 36, 1, 36, 1, 36, 3, 36, 447, 8, 36, 1, 37, 1, 37, 
		    1, 37, 1, 37, 3, 37, 453, 8, 37, 1, 37, 1, 37, 1, 37, 1, 37, 1, 37, 
		    5, 37, 460, 8, 37, 10, 37, 12, 37, 463, 9, 37, 1, 38, 1, 38, 1, 38, 
		    1, 38, 1, 38, 1, 38, 1, 38, 1, 38, 1, 38, 1, 38, 1, 38, 3, 38, 476, 
		    8, 38, 1, 39, 1, 39, 1, 39, 1, 39, 1, 39, 1, 39, 1, 39, 3, 39, 485, 
		    8, 39, 1, 40, 1, 40, 1, 40, 1, 40, 1, 40, 1, 41, 1, 41, 1, 41, 5, 
		    41, 495, 8, 41, 10, 41, 12, 41, 498, 9, 41, 1, 41, 3, 41, 501, 8, 
		    41, 1, 42, 1, 42, 1, 42, 1, 42, 1, 42, 3, 42, 508, 8, 42, 1, 43, 1, 
		    43, 1, 43, 1, 43, 3, 43, 514, 8, 43, 1, 44, 1, 44, 1, 45, 1, 45, 1, 
		    45, 1, 45, 4, 45, 522, 8, 45, 11, 45, 12, 45, 523, 1, 45, 1, 45, 1, 
		    46, 1, 46, 1, 46, 3, 46, 531, 8, 46, 1, 46, 1, 46, 1, 47, 1, 47, 1, 
		    47, 5, 47, 538, 8, 47, 10, 47, 12, 47, 541, 9, 47, 1, 48, 1, 48, 1, 
		    48, 3, 48, 546, 8, 48, 1, 49, 1, 49, 1, 49, 1, 49, 1, 49, 1, 49, 3, 
		    49, 554, 8, 49, 1, 50, 1, 50, 1, 50, 1, 50, 1, 50, 3, 50, 561, 8, 
		    50, 1, 50, 1, 50, 1, 51, 1, 51, 1, 51, 1, 51, 1, 51, 1, 52, 1, 52, 
		    1, 52, 1, 52, 1, 53, 1, 53, 1, 53, 1, 53, 1, 53, 1, 53, 1, 53, 1, 
		    53, 1, 53, 1, 54, 1, 54, 1, 54, 1, 54, 1, 54, 1, 55, 1, 55, 1, 55, 
		    1, 55, 1, 55, 1, 55, 0, 1, 74, 56, 0, 2, 4, 6, 8, 10, 12, 14, 16, 
		    18, 20, 22, 24, 26, 28, 30, 32, 34, 36, 38, 40, 42, 44, 46, 48, 50, 
		    52, 54, 56, 58, 60, 62, 64, 66, 68, 70, 72, 74, 76, 78, 80, 82, 84, 
		    86, 88, 90, 92, 94, 96, 98, 100, 102, 104, 106, 108, 110, 0, 8, 2, 
		    0, 29, 29, 31, 34, 1, 0, 35, 36, 1, 0, 43, 44, 1, 0, 45, 48, 1, 0, 
		    37, 38, 1, 0, 39, 41, 3, 0, 38, 39, 42, 42, 51, 51, 1, 0, 18, 22, 
		    625, 0, 113, 1, 0, 0, 0, 2, 122, 1, 0, 0, 0, 4, 124, 1, 0, 0, 0, 6, 
		    136, 1, 0, 0, 0, 8, 179, 1, 0, 0, 0, 10, 193, 1, 0, 0, 0, 12, 195, 
		    1, 0, 0, 0, 14, 217, 1, 0, 0, 0, 16, 238, 1, 0, 0, 0, 18, 240, 1, 
		    0, 0, 0, 20, 244, 1, 0, 0, 0, 22, 250, 1, 0, 0, 0, 24, 254, 1, 0, 
		    0, 0, 26, 274, 1, 0, 0, 0, 28, 276, 1, 0, 0, 0, 30, 278, 1, 0, 0, 
		    0, 32, 281, 1, 0, 0, 0, 34, 291, 1, 0, 0, 0, 36, 306, 1, 0, 0, 0, 
		    38, 312, 1, 0, 0, 0, 40, 317, 1, 0, 0, 0, 42, 319, 1, 0, 0, 0, 44, 
		    333, 1, 0, 0, 0, 46, 342, 1, 0, 0, 0, 48, 350, 1, 0, 0, 0, 50, 354, 
		    1, 0, 0, 0, 52, 356, 1, 0, 0, 0, 54, 358, 1, 0, 0, 0, 56, 366, 1, 
		    0, 0, 0, 58, 374, 1, 0, 0, 0, 60, 376, 1, 0, 0, 0, 62, 384, 1, 0, 
		    0, 0, 64, 392, 1, 0, 0, 0, 66, 425, 1, 0, 0, 0, 68, 427, 1, 0, 0, 
		    0, 70, 435, 1, 0, 0, 0, 72, 446, 1, 0, 0, 0, 74, 452, 1, 0, 0, 0, 
		    76, 475, 1, 0, 0, 0, 78, 484, 1, 0, 0, 0, 80, 486, 1, 0, 0, 0, 82, 
		    491, 1, 0, 0, 0, 84, 507, 1, 0, 0, 0, 86, 513, 1, 0, 0, 0, 88, 515, 
		    1, 0, 0, 0, 90, 521, 1, 0, 0, 0, 92, 527, 1, 0, 0, 0, 94, 534, 1, 
		    0, 0, 0, 96, 545, 1, 0, 0, 0, 98, 553, 1, 0, 0, 0, 100, 555, 1, 0, 
		    0, 0, 102, 564, 1, 0, 0, 0, 104, 569, 1, 0, 0, 0, 106, 573, 1, 0, 
		    0, 0, 108, 582, 1, 0, 0, 0, 110, 587, 1, 0, 0, 0, 112, 114, 3, 2, 
		    1, 0, 113, 112, 1, 0, 0, 0, 114, 115, 1, 0, 0, 0, 115, 113, 1, 0, 
		    0, 0, 115, 116, 1, 0, 0, 0, 116, 117, 1, 0, 0, 0, 117, 118, 5, 0, 
		    0, 1, 118, 1, 1, 0, 0, 0, 119, 123, 3, 4, 2, 0, 120, 123, 3, 16, 8, 
		    0, 121, 123, 3, 20, 10, 0, 122, 119, 1, 0, 0, 0, 122, 120, 1, 0, 0, 
		    0, 122, 121, 1, 0, 0, 0, 123, 3, 1, 0, 0, 0, 124, 125, 5, 1, 0, 0, 
		    125, 126, 5, 67, 0, 0, 126, 128, 5, 52, 0, 0, 127, 129, 3, 6, 3, 0, 
		    128, 127, 1, 0, 0, 0, 128, 129, 1, 0, 0, 0, 129, 130, 1, 0, 0, 0, 
		    130, 132, 5, 53, 0, 0, 131, 133, 3, 10, 5, 0, 132, 131, 1, 0, 0, 0, 
		    132, 133, 1, 0, 0, 0, 133, 134, 1, 0, 0, 0, 134, 135, 3, 12, 6, 0, 
		    135, 5, 1, 0, 0, 0, 136, 141, 3, 8, 4, 0, 137, 138, 5, 60, 0, 0, 138, 
		    140, 3, 8, 4, 0, 139, 137, 1, 0, 0, 0, 140, 143, 1, 0, 0, 0, 141, 
		    139, 1, 0, 0, 0, 141, 142, 1, 0, 0, 0, 142, 7, 1, 0, 0, 0, 143, 141, 
		    1, 0, 0, 0, 144, 149, 5, 67, 0, 0, 145, 146, 5, 60, 0, 0, 146, 148, 
		    5, 67, 0, 0, 147, 145, 1, 0, 0, 0, 148, 151, 1, 0, 0, 0, 149, 147, 
		    1, 0, 0, 0, 149, 150, 1, 0, 0, 0, 150, 152, 1, 0, 0, 0, 151, 149, 
		    1, 0, 0, 0, 152, 180, 3, 86, 43, 0, 153, 154, 5, 39, 0, 0, 154, 160, 
		    5, 67, 0, 0, 155, 156, 5, 60, 0, 0, 156, 157, 5, 39, 0, 0, 157, 159, 
		    5, 67, 0, 0, 158, 155, 1, 0, 0, 0, 159, 162, 1, 0, 0, 0, 160, 158, 
		    1, 0, 0, 0, 160, 161, 1, 0, 0, 0, 161, 163, 1, 0, 0, 0, 162, 160, 
		    1, 0, 0, 0, 163, 180, 3, 86, 43, 0, 164, 169, 5, 67, 0, 0, 165, 166, 
		    5, 60, 0, 0, 166, 168, 5, 67, 0, 0, 167, 165, 1, 0, 0, 0, 168, 171, 
		    1, 0, 0, 0, 169, 167, 1, 0, 0, 0, 169, 170, 1, 0, 0, 0, 170, 172, 
		    1, 0, 0, 0, 171, 169, 1, 0, 0, 0, 172, 173, 5, 56, 0, 0, 173, 174, 
		    5, 63, 0, 0, 174, 175, 5, 57, 0, 0, 175, 180, 3, 86, 43, 0, 176, 177, 
		    5, 39, 0, 0, 177, 178, 5, 67, 0, 0, 178, 180, 3, 86, 43, 0, 179, 144, 
		    1, 0, 0, 0, 179, 153, 1, 0, 0, 0, 179, 164, 1, 0, 0, 0, 179, 176, 
		    1, 0, 0, 0, 180, 9, 1, 0, 0, 0, 181, 194, 3, 86, 43, 0, 182, 183, 
		    5, 52, 0, 0, 183, 188, 3, 86, 43, 0, 184, 185, 5, 60, 0, 0, 185, 187, 
		    3, 86, 43, 0, 186, 184, 1, 0, 0, 0, 187, 190, 1, 0, 0, 0, 188, 186, 
		    1, 0, 0, 0, 188, 189, 1, 0, 0, 0, 189, 191, 1, 0, 0, 0, 190, 188, 
		    1, 0, 0, 0, 191, 192, 5, 53, 0, 0, 192, 194, 1, 0, 0, 0, 193, 181, 
		    1, 0, 0, 0, 193, 182, 1, 0, 0, 0, 194, 11, 1, 0, 0, 0, 195, 199, 5, 
		    54, 0, 0, 196, 198, 3, 14, 7, 0, 197, 196, 1, 0, 0, 0, 198, 201, 1, 
		    0, 0, 0, 199, 197, 1, 0, 0, 0, 199, 200, 1, 0, 0, 0, 200, 202, 1, 
		    0, 0, 0, 201, 199, 1, 0, 0, 0, 202, 203, 5, 55, 0, 0, 203, 13, 1, 
		    0, 0, 0, 204, 218, 3, 12, 6, 0, 205, 218, 3, 16, 8, 0, 206, 218, 3, 
		    20, 10, 0, 207, 218, 3, 18, 9, 0, 208, 218, 3, 22, 11, 0, 209, 218, 
		    3, 30, 15, 0, 210, 218, 3, 32, 16, 0, 211, 218, 3, 34, 17, 0, 212, 
		    218, 3, 42, 21, 0, 213, 218, 3, 48, 24, 0, 214, 218, 3, 50, 25, 0, 
		    215, 218, 3, 52, 26, 0, 216, 218, 3, 58, 29, 0, 217, 204, 1, 0, 0, 
		    0, 217, 205, 1, 0, 0, 0, 217, 206, 1, 0, 0, 0, 217, 207, 1, 0, 0, 
		    0, 217, 208, 1, 0, 0, 0, 217, 209, 1, 0, 0, 0, 217, 210, 1, 0, 0, 
		    0, 217, 211, 1, 0, 0, 0, 217, 212, 1, 0, 0, 0, 217, 213, 1, 0, 0, 
		    0, 217, 214, 1, 0, 0, 0, 217, 215, 1, 0, 0, 0, 217, 216, 1, 0, 0, 
		    0, 218, 15, 1, 0, 0, 0, 219, 220, 5, 2, 0, 0, 220, 221, 3, 56, 28, 
		    0, 221, 224, 3, 90, 45, 0, 222, 223, 5, 29, 0, 0, 223, 225, 3, 54, 
		    27, 0, 224, 222, 1, 0, 0, 0, 224, 225, 1, 0, 0, 0, 225, 239, 1, 0, 
		    0, 0, 226, 227, 5, 2, 0, 0, 227, 228, 3, 56, 28, 0, 228, 231, 3, 86, 
		    43, 0, 229, 230, 5, 29, 0, 0, 230, 232, 3, 54, 27, 0, 231, 229, 1, 
		    0, 0, 0, 231, 232, 1, 0, 0, 0, 232, 239, 1, 0, 0, 0, 233, 234, 5, 
		    2, 0, 0, 234, 235, 3, 56, 28, 0, 235, 236, 5, 29, 0, 0, 236, 237, 
		    3, 54, 27, 0, 237, 239, 1, 0, 0, 0, 238, 219, 1, 0, 0, 0, 238, 226, 
		    1, 0, 0, 0, 238, 233, 1, 0, 0, 0, 239, 17, 1, 0, 0, 0, 240, 241, 3, 
		    56, 28, 0, 241, 242, 5, 30, 0, 0, 242, 243, 3, 54, 27, 0, 243, 19, 
		    1, 0, 0, 0, 244, 245, 5, 3, 0, 0, 245, 246, 5, 67, 0, 0, 246, 247, 
		    3, 86, 43, 0, 247, 248, 5, 29, 0, 0, 248, 249, 3, 58, 29, 0, 249, 
		    21, 1, 0, 0, 0, 250, 251, 3, 24, 12, 0, 251, 252, 3, 28, 14, 0, 252, 
		    253, 3, 54, 27, 0, 253, 23, 1, 0, 0, 0, 254, 259, 3, 26, 13, 0, 255, 
		    256, 5, 60, 0, 0, 256, 258, 3, 26, 13, 0, 257, 255, 1, 0, 0, 0, 258, 
		    261, 1, 0, 0, 0, 259, 257, 1, 0, 0, 0, 259, 260, 1, 0, 0, 0, 260, 
		    25, 1, 0, 0, 0, 261, 259, 1, 0, 0, 0, 262, 269, 5, 67, 0, 0, 263, 
		    264, 5, 56, 0, 0, 264, 265, 3, 58, 29, 0, 265, 266, 5, 57, 0, 0, 266, 
		    268, 1, 0, 0, 0, 267, 263, 1, 0, 0, 0, 268, 271, 1, 0, 0, 0, 269, 
		    267, 1, 0, 0, 0, 269, 270, 1, 0, 0, 0, 270, 275, 1, 0, 0, 0, 271, 
		    269, 1, 0, 0, 0, 272, 273, 5, 39, 0, 0, 273, 275, 5, 67, 0, 0, 274, 
		    262, 1, 0, 0, 0, 274, 272, 1, 0, 0, 0, 275, 27, 1, 0, 0, 0, 276, 277, 
		    7, 0, 0, 0, 277, 29, 1, 0, 0, 0, 278, 279, 3, 26, 13, 0, 279, 280, 
		    7, 1, 0, 0, 280, 31, 1, 0, 0, 0, 281, 282, 5, 4, 0, 0, 282, 283, 3, 
		    58, 29, 0, 283, 289, 3, 12, 6, 0, 284, 287, 5, 5, 0, 0, 285, 288, 
		    3, 32, 16, 0, 286, 288, 3, 12, 6, 0, 287, 285, 1, 0, 0, 0, 287, 286, 
		    1, 0, 0, 0, 288, 290, 1, 0, 0, 0, 289, 284, 1, 0, 0, 0, 289, 290, 
		    1, 0, 0, 0, 290, 33, 1, 0, 0, 0, 291, 293, 5, 6, 0, 0, 292, 294, 3, 
		    36, 18, 0, 293, 292, 1, 0, 0, 0, 293, 294, 1, 0, 0, 0, 294, 295, 1, 
		    0, 0, 0, 295, 296, 3, 12, 6, 0, 296, 35, 1, 0, 0, 0, 297, 298, 3, 
		    38, 19, 0, 298, 300, 5, 58, 0, 0, 299, 301, 3, 58, 29, 0, 300, 299, 
		    1, 0, 0, 0, 300, 301, 1, 0, 0, 0, 301, 302, 1, 0, 0, 0, 302, 303, 
		    5, 58, 0, 0, 303, 304, 3, 40, 20, 0, 304, 307, 1, 0, 0, 0, 305, 307, 
		    3, 58, 29, 0, 306, 297, 1, 0, 0, 0, 306, 305, 1, 0, 0, 0, 307, 37, 
		    1, 0, 0, 0, 308, 313, 3, 18, 9, 0, 309, 313, 3, 16, 8, 0, 310, 313, 
		    3, 22, 11, 0, 311, 313, 3, 30, 15, 0, 312, 308, 1, 0, 0, 0, 312, 309, 
		    1, 0, 0, 0, 312, 310, 1, 0, 0, 0, 312, 311, 1, 0, 0, 0, 313, 39, 1, 
		    0, 0, 0, 314, 318, 3, 22, 11, 0, 315, 318, 3, 30, 15, 0, 316, 318, 
		    3, 58, 29, 0, 317, 314, 1, 0, 0, 0, 317, 315, 1, 0, 0, 0, 317, 316, 
		    1, 0, 0, 0, 318, 41, 1, 0, 0, 0, 319, 320, 5, 7, 0, 0, 320, 321, 3, 
		    58, 29, 0, 321, 325, 5, 54, 0, 0, 322, 324, 3, 44, 22, 0, 323, 322, 
		    1, 0, 0, 0, 324, 327, 1, 0, 0, 0, 325, 323, 1, 0, 0, 0, 325, 326, 
		    1, 0, 0, 0, 326, 329, 1, 0, 0, 0, 327, 325, 1, 0, 0, 0, 328, 330, 
		    3, 46, 23, 0, 329, 328, 1, 0, 0, 0, 329, 330, 1, 0, 0, 0, 330, 331, 
		    1, 0, 0, 0, 331, 332, 5, 55, 0, 0, 332, 43, 1, 0, 0, 0, 333, 334, 
		    5, 8, 0, 0, 334, 335, 3, 54, 27, 0, 335, 339, 5, 59, 0, 0, 336, 338, 
		    3, 14, 7, 0, 337, 336, 1, 0, 0, 0, 338, 341, 1, 0, 0, 0, 339, 337, 
		    1, 0, 0, 0, 339, 340, 1, 0, 0, 0, 340, 45, 1, 0, 0, 0, 341, 339, 1, 
		    0, 0, 0, 342, 343, 5, 9, 0, 0, 343, 347, 5, 59, 0, 0, 344, 346, 3, 
		    14, 7, 0, 345, 344, 1, 0, 0, 0, 346, 349, 1, 0, 0, 0, 347, 345, 1, 
		    0, 0, 0, 347, 348, 1, 0, 0, 0, 348, 47, 1, 0, 0, 0, 349, 347, 1, 0, 
		    0, 0, 350, 352, 5, 10, 0, 0, 351, 353, 3, 54, 27, 0, 352, 351, 1, 
		    0, 0, 0, 352, 353, 1, 0, 0, 0, 353, 49, 1, 0, 0, 0, 354, 355, 5, 11, 
		    0, 0, 355, 51, 1, 0, 0, 0, 356, 357, 5, 12, 0, 0, 357, 53, 1, 0, 0, 
		    0, 358, 363, 3, 58, 29, 0, 359, 360, 5, 60, 0, 0, 360, 362, 3, 58, 
		    29, 0, 361, 359, 1, 0, 0, 0, 362, 365, 1, 0, 0, 0, 363, 361, 1, 0, 
		    0, 0, 363, 364, 1, 0, 0, 0, 364, 55, 1, 0, 0, 0, 365, 363, 1, 0, 0, 
		    0, 366, 371, 5, 67, 0, 0, 367, 368, 5, 60, 0, 0, 368, 370, 5, 67, 
		    0, 0, 369, 367, 1, 0, 0, 0, 370, 373, 1, 0, 0, 0, 371, 369, 1, 0, 
		    0, 0, 371, 372, 1, 0, 0, 0, 372, 57, 1, 0, 0, 0, 373, 371, 1, 0, 0, 
		    0, 374, 375, 3, 60, 30, 0, 375, 59, 1, 0, 0, 0, 376, 381, 3, 62, 31, 
		    0, 377, 378, 5, 50, 0, 0, 378, 380, 3, 62, 31, 0, 379, 377, 1, 0, 
		    0, 0, 380, 383, 1, 0, 0, 0, 381, 379, 1, 0, 0, 0, 381, 382, 1, 0, 
		    0, 0, 382, 61, 1, 0, 0, 0, 383, 381, 1, 0, 0, 0, 384, 389, 3, 64, 
		    32, 0, 385, 386, 5, 49, 0, 0, 386, 388, 3, 64, 32, 0, 387, 385, 1, 
		    0, 0, 0, 388, 391, 1, 0, 0, 0, 389, 387, 1, 0, 0, 0, 389, 390, 1, 
		    0, 0, 0, 390, 63, 1, 0, 0, 0, 391, 389, 1, 0, 0, 0, 392, 397, 3, 66, 
		    33, 0, 393, 394, 7, 2, 0, 0, 394, 396, 3, 66, 33, 0, 395, 393, 1, 
		    0, 0, 0, 396, 399, 1, 0, 0, 0, 397, 395, 1, 0, 0, 0, 397, 398, 1, 
		    0, 0, 0, 398, 65, 1, 0, 0, 0, 399, 397, 1, 0, 0, 0, 400, 405, 3, 68, 
		    34, 0, 401, 402, 7, 3, 0, 0, 402, 404, 3, 68, 34, 0, 403, 401, 1, 
		    0, 0, 0, 404, 407, 1, 0, 0, 0, 405, 403, 1, 0, 0, 0, 405, 406, 1, 
		    0, 0, 0, 406, 426, 1, 0, 0, 0, 407, 405, 1, 0, 0, 0, 408, 409, 3, 
		    68, 34, 0, 409, 410, 5, 13, 0, 0, 410, 411, 5, 56, 0, 0, 411, 412, 
		    3, 58, 29, 0, 412, 413, 5, 61, 0, 0, 413, 414, 3, 58, 29, 0, 414, 
		    415, 5, 57, 0, 0, 415, 426, 1, 0, 0, 0, 416, 417, 3, 68, 34, 0, 417, 
		    418, 5, 14, 0, 0, 418, 419, 5, 13, 0, 0, 419, 420, 5, 56, 0, 0, 420, 
		    421, 3, 58, 29, 0, 421, 422, 5, 61, 0, 0, 422, 423, 3, 58, 29, 0, 
		    423, 424, 5, 57, 0, 0, 424, 426, 1, 0, 0, 0, 425, 400, 1, 0, 0, 0, 
		    425, 408, 1, 0, 0, 0, 425, 416, 1, 0, 0, 0, 426, 67, 1, 0, 0, 0, 427, 
		    432, 3, 70, 35, 0, 428, 429, 7, 4, 0, 0, 429, 431, 3, 70, 35, 0, 430, 
		    428, 1, 0, 0, 0, 431, 434, 1, 0, 0, 0, 432, 430, 1, 0, 0, 0, 432, 
		    433, 1, 0, 0, 0, 433, 69, 1, 0, 0, 0, 434, 432, 1, 0, 0, 0, 435, 440, 
		    3, 72, 36, 0, 436, 437, 7, 5, 0, 0, 437, 439, 3, 72, 36, 0, 438, 436, 
		    1, 0, 0, 0, 439, 442, 1, 0, 0, 0, 440, 438, 1, 0, 0, 0, 440, 441, 
		    1, 0, 0, 0, 441, 71, 1, 0, 0, 0, 442, 440, 1, 0, 0, 0, 443, 444, 7, 
		    6, 0, 0, 444, 447, 3, 72, 36, 0, 445, 447, 3, 74, 37, 0, 446, 443, 
		    1, 0, 0, 0, 446, 445, 1, 0, 0, 0, 447, 73, 1, 0, 0, 0, 448, 449, 6, 
		    37, -1, 0, 449, 453, 3, 92, 46, 0, 450, 453, 3, 98, 49, 0, 451, 453, 
		    3, 76, 38, 0, 452, 448, 1, 0, 0, 0, 452, 450, 1, 0, 0, 0, 452, 451, 
		    1, 0, 0, 0, 453, 461, 1, 0, 0, 0, 454, 455, 10, 2, 0, 0, 455, 456, 
		    5, 56, 0, 0, 456, 457, 3, 58, 29, 0, 457, 458, 5, 57, 0, 0, 458, 460, 
		    1, 0, 0, 0, 459, 454, 1, 0, 0, 0, 460, 463, 1, 0, 0, 0, 461, 459, 
		    1, 0, 0, 0, 461, 462, 1, 0, 0, 0, 462, 75, 1, 0, 0, 0, 463, 461, 1, 
		    0, 0, 0, 464, 476, 3, 78, 39, 0, 465, 476, 5, 67, 0, 0, 466, 467, 
		    5, 42, 0, 0, 467, 476, 5, 67, 0, 0, 468, 469, 5, 39, 0, 0, 469, 476, 
		    5, 67, 0, 0, 470, 471, 5, 52, 0, 0, 471, 472, 3, 58, 29, 0, 472, 473, 
		    5, 53, 0, 0, 473, 476, 1, 0, 0, 0, 474, 476, 5, 15, 0, 0, 475, 464, 
		    1, 0, 0, 0, 475, 465, 1, 0, 0, 0, 475, 466, 1, 0, 0, 0, 475, 468, 
		    1, 0, 0, 0, 475, 470, 1, 0, 0, 0, 475, 474, 1, 0, 0, 0, 476, 77, 1, 
		    0, 0, 0, 477, 485, 5, 63, 0, 0, 478, 485, 5, 64, 0, 0, 479, 485, 5, 
		    65, 0, 0, 480, 485, 5, 66, 0, 0, 481, 485, 5, 16, 0, 0, 482, 485, 
		    5, 17, 0, 0, 483, 485, 3, 80, 40, 0, 484, 477, 1, 0, 0, 0, 484, 478, 
		    1, 0, 0, 0, 484, 479, 1, 0, 0, 0, 484, 480, 1, 0, 0, 0, 484, 481, 
		    1, 0, 0, 0, 484, 482, 1, 0, 0, 0, 484, 483, 1, 0, 0, 0, 485, 79, 1, 
		    0, 0, 0, 486, 487, 3, 90, 45, 0, 487, 488, 5, 54, 0, 0, 488, 489, 
		    3, 82, 41, 0, 489, 490, 5, 55, 0, 0, 490, 81, 1, 0, 0, 0, 491, 496, 
		    3, 84, 42, 0, 492, 493, 5, 60, 0, 0, 493, 495, 3, 84, 42, 0, 494, 
		    492, 1, 0, 0, 0, 495, 498, 1, 0, 0, 0, 496, 494, 1, 0, 0, 0, 496, 
		    497, 1, 0, 0, 0, 497, 500, 1, 0, 0, 0, 498, 496, 1, 0, 0, 0, 499, 
		    501, 5, 60, 0, 0, 500, 499, 1, 0, 0, 0, 500, 501, 1, 0, 0, 0, 501, 
		    83, 1, 0, 0, 0, 502, 508, 3, 58, 29, 0, 503, 504, 5, 54, 0, 0, 504, 
		    505, 3, 82, 41, 0, 505, 506, 5, 55, 0, 0, 506, 508, 1, 0, 0, 0, 507, 
		    502, 1, 0, 0, 0, 507, 503, 1, 0, 0, 0, 508, 85, 1, 0, 0, 0, 509, 514, 
		    3, 88, 44, 0, 510, 511, 5, 39, 0, 0, 511, 514, 3, 86, 43, 0, 512, 
		    514, 3, 90, 45, 0, 513, 509, 1, 0, 0, 0, 513, 510, 1, 0, 0, 0, 513, 
		    512, 1, 0, 0, 0, 514, 87, 1, 0, 0, 0, 515, 516, 7, 7, 0, 0, 516, 89, 
		    1, 0, 0, 0, 517, 518, 5, 56, 0, 0, 518, 519, 3, 58, 29, 0, 519, 520, 
		    5, 57, 0, 0, 520, 522, 1, 0, 0, 0, 521, 517, 1, 0, 0, 0, 522, 523, 
		    1, 0, 0, 0, 523, 521, 1, 0, 0, 0, 523, 524, 1, 0, 0, 0, 524, 525, 
		    1, 0, 0, 0, 525, 526, 3, 86, 43, 0, 526, 91, 1, 0, 0, 0, 527, 528, 
		    5, 67, 0, 0, 528, 530, 5, 52, 0, 0, 529, 531, 3, 94, 47, 0, 530, 529, 
		    1, 0, 0, 0, 530, 531, 1, 0, 0, 0, 531, 532, 1, 0, 0, 0, 532, 533, 
		    5, 53, 0, 0, 533, 93, 1, 0, 0, 0, 534, 539, 3, 96, 48, 0, 535, 536, 
		    5, 60, 0, 0, 536, 538, 3, 96, 48, 0, 537, 535, 1, 0, 0, 0, 538, 541, 
		    1, 0, 0, 0, 539, 537, 1, 0, 0, 0, 539, 540, 1, 0, 0, 0, 540, 95, 1, 
		    0, 0, 0, 541, 539, 1, 0, 0, 0, 542, 546, 3, 58, 29, 0, 543, 544, 5, 
		    42, 0, 0, 544, 546, 5, 67, 0, 0, 545, 542, 1, 0, 0, 0, 545, 543, 1, 
		    0, 0, 0, 546, 97, 1, 0, 0, 0, 547, 554, 3, 100, 50, 0, 548, 554, 3, 
		    102, 51, 0, 549, 554, 3, 104, 52, 0, 550, 554, 3, 106, 53, 0, 551, 
		    554, 3, 108, 54, 0, 552, 554, 3, 110, 55, 0, 553, 547, 1, 0, 0, 0, 
		    553, 548, 1, 0, 0, 0, 553, 549, 1, 0, 0, 0, 553, 550, 1, 0, 0, 0, 
		    553, 551, 1, 0, 0, 0, 553, 552, 1, 0, 0, 0, 554, 99, 1, 0, 0, 0, 555, 
		    556, 5, 23, 0, 0, 556, 557, 5, 62, 0, 0, 557, 558, 5, 24, 0, 0, 558, 
		    560, 5, 52, 0, 0, 559, 561, 3, 94, 47, 0, 560, 559, 1, 0, 0, 0, 560, 
		    561, 1, 0, 0, 0, 561, 562, 1, 0, 0, 0, 562, 563, 5, 53, 0, 0, 563, 
		    101, 1, 0, 0, 0, 564, 565, 5, 25, 0, 0, 565, 566, 5, 52, 0, 0, 566, 
		    567, 3, 58, 29, 0, 567, 568, 5, 53, 0, 0, 568, 103, 1, 0, 0, 0, 569, 
		    570, 5, 26, 0, 0, 570, 571, 5, 52, 0, 0, 571, 572, 5, 53, 0, 0, 572, 
		    105, 1, 0, 0, 0, 573, 574, 5, 27, 0, 0, 574, 575, 5, 52, 0, 0, 575, 
		    576, 3, 58, 29, 0, 576, 577, 5, 60, 0, 0, 577, 578, 3, 58, 29, 0, 
		    578, 579, 5, 60, 0, 0, 579, 580, 3, 58, 29, 0, 580, 581, 5, 53, 0, 
		    0, 581, 107, 1, 0, 0, 0, 582, 583, 5, 28, 0, 0, 583, 584, 5, 52, 0, 
		    0, 584, 585, 3, 58, 29, 0, 585, 586, 5, 53, 0, 0, 586, 109, 1, 0, 
		    0, 0, 587, 588, 7, 7, 0, 0, 588, 589, 5, 52, 0, 0, 589, 590, 3, 58, 
		    29, 0, 590, 591, 5, 53, 0, 0, 591, 111, 1, 0, 0, 0, 55, 115, 122, 
		    128, 132, 141, 149, 160, 169, 179, 188, 193, 199, 217, 224, 231, 238, 
		    259, 269, 274, 287, 289, 293, 300, 306, 312, 317, 325, 329, 339, 347, 
		    352, 363, 371, 381, 389, 397, 405, 425, 432, 440, 446, 452, 461, 475, 
		    484, 496, 500, 507, 513, 523, 530, 539, 545, 553, 560];
		protected static $atn;
		protected static $decisionToDFA;
		protected static $sharedContextCache;

		public function __construct(TokenStream $input)
		{
			parent::__construct($input);

			self::initialize();

			$this->interp = new ParserATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
		}

		private static function initialize(): void
		{
			if (self::$atn !== null) {
				return;
			}

			RuntimeMetaData::checkVersion('4.13.1', RuntimeMetaData::VERSION);

			$atn = (new ATNDeserializer())->deserialize(self::SERIALIZED_ATN);

			$decisionToDFA = [];
			for ($i = 0, $count = $atn->getNumberOfDecisions(); $i < $count; $i++) {
				$decisionToDFA[] = new DFA($atn->getDecisionState($i), $i);
			}

			self::$atn = $atn;
			self::$decisionToDFA = $decisionToDFA;
			self::$sharedContextCache = new PredictionContextCache();
		}

		public function getGrammarFileName(): string
		{
			return "Grammar.g4";
		}

		public function getRuleNames(): array
		{
			return self::RULE_NAMES;
		}

		public function getSerializedATN(): array
		{
			return self::SERIALIZED_ATN;
		}

		public function getATN(): ATN
		{
			return self::$atn;
		}

		public function getVocabulary(): Vocabulary
        {
            static $vocabulary;

			return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
        }

		/**
		 * @throws RecognitionException
		 */
		public function program(): Context\ProgramContext
		{
		    $localContext = new Context\ProgramContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 0, self::RULE_program);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(113); 
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        do {
		        	$this->setState(112);
		        	$this->globalDeclaration();
		        	$this->setState(115); 
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        } while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 14) !== 0));
		        $this->setState(117);
		        $this->match(self::EOF);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function globalDeclaration(): Context\GlobalDeclarationContext
		{
		    $localContext = new Context\GlobalDeclarationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 2, self::RULE_globalDeclaration);

		    try {
		        $this->setState(122);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::FUNC:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(119);
		            	$this->funcDecl();
		            	break;

		            case self::VAR:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(120);
		            	$this->varDecl();
		            	break;

		            case self::CONST:
		            	$this->enterOuterAlt($localContext, 3);
		            	$this->setState(121);
		            	$this->constDecl();
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function funcDecl(): Context\FuncDeclContext
		{
		    $localContext = new Context\FuncDeclContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 4, self::RULE_funcDecl);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(124);
		        $this->match(self::FUNC);
		        $this->setState(125);
		        $this->match(self::ID);
		        $this->setState(126);
		        $this->match(self::LPAREN);
		        $this->setState(128);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::STAR || $_la === self::ID) {
		        	$this->setState(127);
		        	$this->paramList();
		        }
		        $this->setState(130);
		        $this->match(self::RPAREN);
		        $this->setState(132);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 76561743429238784) !== 0)) {
		        	$this->setState(131);
		        	$this->returnType();
		        }
		        $this->setState(134);
		        $this->block();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function paramList(): Context\ParamListContext
		{
		    $localContext = new Context\ParamListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 6, self::RULE_paramList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(136);
		        $this->paramGroup();
		        $this->setState(141);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::COMMA) {
		        	$this->setState(137);
		        	$this->match(self::COMMA);
		        	$this->setState(138);
		        	$this->paramGroup();
		        	$this->setState(143);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function paramGroup(): Context\ParamGroupContext
		{
		    $localContext = new Context\ParamGroupContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 8, self::RULE_paramGroup);

		    try {
		        $this->setState(179);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 8, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(144);
		        	    $this->match(self::ID);
		        	    $this->setState(149);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::COMMA) {
		        	    	$this->setState(145);
		        	    	$this->match(self::COMMA);
		        	    	$this->setState(146);
		        	    	$this->match(self::ID);
		        	    	$this->setState(151);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(152);
		        	    $this->typeLiteral();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(153);
		        	    $this->match(self::STAR);
		        	    $this->setState(154);
		        	    $this->match(self::ID);
		        	    $this->setState(160);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::COMMA) {
		        	    	$this->setState(155);
		        	    	$this->match(self::COMMA);
		        	    	$this->setState(156);
		        	    	$this->match(self::STAR);
		        	    	$this->setState(157);
		        	    	$this->match(self::ID);
		        	    	$this->setState(162);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(163);
		        	    $this->typeLiteral();
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(164);
		        	    $this->match(self::ID);
		        	    $this->setState(169);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while ($_la === self::COMMA) {
		        	    	$this->setState(165);
		        	    	$this->match(self::COMMA);
		        	    	$this->setState(166);
		        	    	$this->match(self::ID);
		        	    	$this->setState(171);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	    $this->setState(172);
		        	    $this->match(self::LBRACKET);
		        	    $this->setState(173);
		        	    $this->match(self::INT_LIT);
		        	    $this->setState(174);
		        	    $this->match(self::RBRACKET);
		        	    $this->setState(175);
		        	    $this->typeLiteral();
		        	break;

		        	case 4:
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(176);
		        	    $this->match(self::STAR);
		        	    $this->setState(177);
		        	    $this->match(self::ID);
		        	    $this->setState(178);
		        	    $this->typeLiteral();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function returnType(): Context\ReturnTypeContext
		{
		    $localContext = new Context\ReturnTypeContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 10, self::RULE_returnType);

		    try {
		        $this->setState(193);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::INT32:
		            case self::FLOAT32:
		            case self::BOOL:
		            case self::RUNE:
		            case self::STRING:
		            case self::STAR:
		            case self::LBRACKET:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(181);
		            	$this->typeLiteral();
		            	break;

		            case self::LPAREN:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(182);
		            	$this->match(self::LPAREN);
		            	$this->setState(183);
		            	$this->typeLiteral();
		            	$this->setState(188);
		            	$this->errorHandler->sync($this);

		            	$_la = $this->input->LA(1);
		            	while ($_la === self::COMMA) {
		            		$this->setState(184);
		            		$this->match(self::COMMA);
		            		$this->setState(185);
		            		$this->typeLiteral();
		            		$this->setState(190);
		            		$this->errorHandler->sync($this);
		            		$_la = $this->input->LA(1);
		            	}
		            	$this->setState(191);
		            	$this->match(self::RPAREN);
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function block(): Context\BlockContext
		{
		    $localContext = new Context\BlockContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 12, self::RULE_block);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(195);
		        $this->match(self::LBRACE);
		        $this->setState(199);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & -9126539421666009892) !== 0) || (((($_la - 64)) & ~0x3f) === 0 && ((1 << ($_la - 64)) & 15) !== 0)) {
		        	$this->setState(196);
		        	$this->statement();
		        	$this->setState(201);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		        $this->setState(202);
		        $this->match(self::RBRACE);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function statement(): Context\StatementContext
		{
		    $localContext = new Context\StatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 14, self::RULE_statement);

		    try {
		        $this->setState(217);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 12, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(204);
		        	    $this->block();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(205);
		        	    $this->varDecl();
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(206);
		        	    $this->constDecl();
		        	break;

		        	case 4:
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(207);
		        	    $this->shortVarDecl();
		        	break;

		        	case 5:
		        	    $this->enterOuterAlt($localContext, 5);
		        	    $this->setState(208);
		        	    $this->assignStmt();
		        	break;

		        	case 6:
		        	    $this->enterOuterAlt($localContext, 6);
		        	    $this->setState(209);
		        	    $this->incDecStmt();
		        	break;

		        	case 7:
		        	    $this->enterOuterAlt($localContext, 7);
		        	    $this->setState(210);
		        	    $this->ifStmt();
		        	break;

		        	case 8:
		        	    $this->enterOuterAlt($localContext, 8);
		        	    $this->setState(211);
		        	    $this->forStmt();
		        	break;

		        	case 9:
		        	    $this->enterOuterAlt($localContext, 9);
		        	    $this->setState(212);
		        	    $this->switchStatement();
		        	break;

		        	case 10:
		        	    $this->enterOuterAlt($localContext, 10);
		        	    $this->setState(213);
		        	    $this->returnStatement();
		        	break;

		        	case 11:
		        	    $this->enterOuterAlt($localContext, 11);
		        	    $this->setState(214);
		        	    $this->breakStmt();
		        	break;

		        	case 12:
		        	    $this->enterOuterAlt($localContext, 12);
		        	    $this->setState(215);
		        	    $this->continueStmt();
		        	break;

		        	case 13:
		        	    $this->enterOuterAlt($localContext, 13);
		        	    $this->setState(216);
		        	    $this->expression();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function varDecl(): Context\VarDeclContext
		{
		    $localContext = new Context\VarDeclContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 16, self::RULE_varDecl);

		    try {
		        $this->setState(238);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 15, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(219);
		        	    $this->match(self::VAR);
		        	    $this->setState(220);
		        	    $this->idList();
		        	    $this->setState(221);
		        	    $this->arrayTypeLiteral();
		        	    $this->setState(224);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if ($_la === self::ASSIGN) {
		        	    	$this->setState(222);
		        	    	$this->match(self::ASSIGN);
		        	    	$this->setState(223);
		        	    	$this->expressionList();
		        	    }
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(226);
		        	    $this->match(self::VAR);
		        	    $this->setState(227);
		        	    $this->idList();
		        	    $this->setState(228);
		        	    $this->typeLiteral();
		        	    $this->setState(231);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if ($_la === self::ASSIGN) {
		        	    	$this->setState(229);
		        	    	$this->match(self::ASSIGN);
		        	    	$this->setState(230);
		        	    	$this->expressionList();
		        	    }
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(233);
		        	    $this->match(self::VAR);
		        	    $this->setState(234);
		        	    $this->idList();
		        	    $this->setState(235);
		        	    $this->match(self::ASSIGN);
		        	    $this->setState(236);
		        	    $this->expressionList();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function shortVarDecl(): Context\ShortVarDeclContext
		{
		    $localContext = new Context\ShortVarDeclContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 18, self::RULE_shortVarDecl);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(240);
		        $this->idList();
		        $this->setState(241);
		        $this->match(self::DEFINE);
		        $this->setState(242);
		        $this->expressionList();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function constDecl(): Context\ConstDeclContext
		{
		    $localContext = new Context\ConstDeclContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 20, self::RULE_constDecl);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(244);
		        $this->match(self::CONST);
		        $this->setState(245);
		        $this->match(self::ID);
		        $this->setState(246);
		        $this->typeLiteral();
		        $this->setState(247);
		        $this->match(self::ASSIGN);
		        $this->setState(248);
		        $this->expression();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function assignStmt(): Context\AssignStmtContext
		{
		    $localContext = new Context\AssignStmtContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 22, self::RULE_assignStmt);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(250);
		        $this->lValueList();
		        $this->setState(251);
		        $this->assignOp();
		        $this->setState(252);
		        $this->expressionList();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function lValueList(): Context\LValueListContext
		{
		    $localContext = new Context\LValueListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 24, self::RULE_lValueList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(254);
		        $this->lValue();
		        $this->setState(259);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::COMMA) {
		        	$this->setState(255);
		        	$this->match(self::COMMA);
		        	$this->setState(256);
		        	$this->lValue();
		        	$this->setState(261);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function lValue(): Context\LValueContext
		{
		    $localContext = new Context\LValueContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 26, self::RULE_lValue);

		    try {
		        $this->setState(274);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::ID:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(262);
		            	$this->match(self::ID);
		            	$this->setState(269);
		            	$this->errorHandler->sync($this);

		            	$_la = $this->input->LA(1);
		            	while ($_la === self::LBRACKET) {
		            		$this->setState(263);
		            		$this->match(self::LBRACKET);
		            		$this->setState(264);
		            		$this->expression();
		            		$this->setState(265);
		            		$this->match(self::RBRACKET);
		            		$this->setState(271);
		            		$this->errorHandler->sync($this);
		            		$_la = $this->input->LA(1);
		            	}
		            	break;

		            case self::STAR:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(272);
		            	$this->match(self::STAR);
		            	$this->setState(273);
		            	$this->match(self::ID);
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function assignOp(): Context\AssignOpContext
		{
		    $localContext = new Context\AssignOpContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 28, self::RULE_assignOp);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(276);

		        $_la = $this->input->LA(1);

		        if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 32749125632) !== 0))) {
		        $this->errorHandler->recoverInline($this);
		        } else {
		        	if ($this->input->LA(1) === Token::EOF) {
		        	    $this->matchedEOF = true;
		            }

		        	$this->errorHandler->reportMatch($this);
		        	$this->consume();
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function incDecStmt(): Context\IncDecStmtContext
		{
		    $localContext = new Context\IncDecStmtContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 30, self::RULE_incDecStmt);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(278);
		        $this->lValue();
		        $this->setState(279);

		        $_la = $this->input->LA(1);

		        if (!($_la === self::INC || $_la === self::DEC)) {
		        $this->errorHandler->recoverInline($this);
		        } else {
		        	if ($this->input->LA(1) === Token::EOF) {
		        	    $this->matchedEOF = true;
		            }

		        	$this->errorHandler->reportMatch($this);
		        	$this->consume();
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function ifStmt(): Context\IfStmtContext
		{
		    $localContext = new Context\IfStmtContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 32, self::RULE_ifStmt);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(281);
		        $this->match(self::IF);
		        $this->setState(282);
		        $this->expression();
		        $this->setState(283);
		        $this->block();
		        $this->setState(289);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::ELSE) {
		        	$this->setState(284);
		        	$this->match(self::ELSE);
		        	$this->setState(287);
		        	$this->errorHandler->sync($this);

		        	switch ($this->input->LA(1)) {
		        	    case self::IF:
		        	    	$this->setState(285);
		        	    	$this->ifStmt();
		        	    	break;

		        	    case self::LBRACE:
		        	    	$this->setState(286);
		        	    	$this->block();
		        	    	break;

		        	default:
		        		throw new NoViableAltException($this);
		        	}
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forStmt(): Context\ForStmtContext
		{
		    $localContext = new Context\ForStmtContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 34, self::RULE_forStmt);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(291);
		        $this->match(self::FOR);
		        $this->setState(293);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if (((($_la) & ~0x3f) === 0 && ((1 << $_la) & -9144553820175499260) !== 0) || (((($_la - 64)) & ~0x3f) === 0 && ((1 << ($_la - 64)) & 15) !== 0)) {
		        	$this->setState(292);
		        	$this->forHeader();
		        }
		        $this->setState(295);
		        $this->block();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forHeader(): Context\ForHeaderContext
		{
		    $localContext = new Context\ForHeaderContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 36, self::RULE_forHeader);

		    try {
		        $this->setState(306);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 23, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(297);
		        	    $this->forInitialization();
		        	    $this->setState(298);
		        	    $this->match(self::PUNTOYCOM);
		        	    $this->setState(300);
		        	    $this->errorHandler->sync($this);
		        	    $_la = $this->input->LA(1);

		        	    if ((((($_la - 15)) & ~0x3f) === 0 && ((1 << ($_la - 15)) & 8728129619115519) !== 0)) {
		        	    	$this->setState(299);
		        	    	$this->expression();
		        	    }
		        	    $this->setState(302);
		        	    $this->match(self::PUNTOYCOM);
		        	    $this->setState(303);
		        	    $this->forPost();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(305);
		        	    $this->expression();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forInitialization(): Context\ForInitializationContext
		{
		    $localContext = new Context\ForInitializationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 38, self::RULE_forInitialization);

		    try {
		        $this->setState(312);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 24, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(308);
		        	    $this->shortVarDecl();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(309);
		        	    $this->varDecl();
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(310);
		        	    $this->assignStmt();
		        	break;

		        	case 4:
		        	    $this->enterOuterAlt($localContext, 4);
		        	    $this->setState(311);
		        	    $this->incDecStmt();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function forPost(): Context\ForPostContext
		{
		    $localContext = new Context\ForPostContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 40, self::RULE_forPost);

		    try {
		        $this->setState(317);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 25, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(314);
		        	    $this->assignStmt();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(315);
		        	    $this->incDecStmt();
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(316);
		        	    $this->expression();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function switchStatement(): Context\SwitchStatementContext
		{
		    $localContext = new Context\SwitchStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 42, self::RULE_switchStatement);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(319);
		        $this->match(self::SWITCH);
		        $this->setState(320);
		        $this->expression();
		        $this->setState(321);
		        $this->match(self::LBRACE);
		        $this->setState(325);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::CASE) {
		        	$this->setState(322);
		        	$this->switchCase();
		        	$this->setState(327);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		        $this->setState(329);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::DEFAULT) {
		        	$this->setState(328);
		        	$this->defaultCase();
		        }
		        $this->setState(331);
		        $this->match(self::RBRACE);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function switchCase(): Context\SwitchCaseContext
		{
		    $localContext = new Context\SwitchCaseContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 44, self::RULE_switchCase);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(333);
		        $this->match(self::CASE);
		        $this->setState(334);
		        $this->expressionList();
		        $this->setState(335);
		        $this->match(self::DOSPUNTOS);
		        $this->setState(339);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & -9126539421666009892) !== 0) || (((($_la - 64)) & ~0x3f) === 0 && ((1 << ($_la - 64)) & 15) !== 0)) {
		        	$this->setState(336);
		        	$this->statement();
		        	$this->setState(341);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function defaultCase(): Context\DefaultCaseContext
		{
		    $localContext = new Context\DefaultCaseContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 46, self::RULE_defaultCase);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(342);
		        $this->match(self::DEFAULT);
		        $this->setState(343);
		        $this->match(self::DOSPUNTOS);
		        $this->setState(347);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & -9126539421666009892) !== 0) || (((($_la - 64)) & ~0x3f) === 0 && ((1 << ($_la - 64)) & 15) !== 0)) {
		        	$this->setState(344);
		        	$this->statement();
		        	$this->setState(349);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function returnStatement(): Context\ReturnStatementContext
		{
		    $localContext = new Context\ReturnStatementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 48, self::RULE_returnStatement);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(350);
		        $this->match(self::RETURN);
		        $this->setState(352);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 30, $this->ctx)) {
		            case 1:
		        	    $this->setState(351);
		        	    $this->expressionList();
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function breakStmt(): Context\BreakStmtContext
		{
		    $localContext = new Context\BreakStmtContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 50, self::RULE_breakStmt);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(354);
		        $this->match(self::BREAK);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function continueStmt(): Context\ContinueStmtContext
		{
		    $localContext = new Context\ContinueStmtContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 52, self::RULE_continueStmt);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(356);
		        $this->match(self::CONTINUE);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function expressionList(): Context\ExpressionListContext
		{
		    $localContext = new Context\ExpressionListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 54, self::RULE_expressionList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(358);
		        $this->expression();
		        $this->setState(363);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::COMMA) {
		        	$this->setState(359);
		        	$this->match(self::COMMA);
		        	$this->setState(360);
		        	$this->expression();
		        	$this->setState(365);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function idList(): Context\IdListContext
		{
		    $localContext = new Context\IdListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 56, self::RULE_idList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(366);
		        $this->match(self::ID);
		        $this->setState(371);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::COMMA) {
		        	$this->setState(367);
		        	$this->match(self::COMMA);
		        	$this->setState(368);
		        	$this->match(self::ID);
		        	$this->setState(373);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function expression(): Context\ExpressionContext
		{
		    $localContext = new Context\ExpressionContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 58, self::RULE_expression);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(374);
		        $this->logicalOr();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function logicalOr(): Context\LogicalOrContext
		{
		    $localContext = new Context\LogicalOrContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 60, self::RULE_logicalOr);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(376);
		        $this->logicalAnd();
		        $this->setState(381);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::OR) {
		        	$this->setState(377);
		        	$this->match(self::OR);
		        	$this->setState(378);
		        	$this->logicalAnd();
		        	$this->setState(383);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function logicalAnd(): Context\LogicalAndContext
		{
		    $localContext = new Context\LogicalAndContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 62, self::RULE_logicalAnd);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(384);
		        $this->equality();
		        $this->setState(389);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::AND) {
		        	$this->setState(385);
		        	$this->match(self::AND);
		        	$this->setState(386);
		        	$this->equality();
		        	$this->setState(391);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function equality(): Context\EqualityContext
		{
		    $localContext = new Context\EqualityContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 64, self::RULE_equality);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(392);
		        $this->comparison();
		        $this->setState(397);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::EQ || $_la === self::NEQ) {
		        	$this->setState(393);

		        	$_la = $this->input->LA(1);

		        	if (!($_la === self::EQ || $_la === self::NEQ)) {
		        	$this->errorHandler->recoverInline($this);
		        	} else {
		        		if ($this->input->LA(1) === Token::EOF) {
		        		    $this->matchedEOF = true;
		        	    }

		        		$this->errorHandler->reportMatch($this);
		        		$this->consume();
		        	}
		        	$this->setState(394);
		        	$this->comparison();
		        	$this->setState(399);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function comparison(): Context\ComparisonContext
		{
		    $localContext = new Context\ComparisonContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 66, self::RULE_comparison);

		    try {
		        $this->setState(425);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 37, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(400);
		        	    $this->addition();
		        	    $this->setState(405);
		        	    $this->errorHandler->sync($this);

		        	    $_la = $this->input->LA(1);
		        	    while (((($_la) & ~0x3f) === 0 && ((1 << $_la) & 527765581332480) !== 0)) {
		        	    	$this->setState(401);

		        	    	$_la = $this->input->LA(1);

		        	    	if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 527765581332480) !== 0))) {
		        	    	$this->errorHandler->recoverInline($this);
		        	    	} else {
		        	    		if ($this->input->LA(1) === Token::EOF) {
		        	    		    $this->matchedEOF = true;
		        	    	    }

		        	    		$this->errorHandler->reportMatch($this);
		        	    		$this->consume();
		        	    	}
		        	    	$this->setState(402);
		        	    	$this->addition();
		        	    	$this->setState(407);
		        	    	$this->errorHandler->sync($this);
		        	    	$_la = $this->input->LA(1);
		        	    }
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(408);
		        	    $this->addition();
		        	    $this->setState(409);
		        	    $this->match(self::IN);
		        	    $this->setState(410);
		        	    $this->match(self::LBRACKET);
		        	    $this->setState(411);
		        	    $this->expression();
		        	    $this->setState(412);
		        	    $this->match(self::DOTDOT);
		        	    $this->setState(413);
		        	    $this->expression();
		        	    $this->setState(414);
		        	    $this->match(self::RBRACKET);
		        	break;

		        	case 3:
		        	    $this->enterOuterAlt($localContext, 3);
		        	    $this->setState(416);
		        	    $this->addition();
		        	    $this->setState(417);
		        	    $this->match(self::NOT_KW);
		        	    $this->setState(418);
		        	    $this->match(self::IN);
		        	    $this->setState(419);
		        	    $this->match(self::LBRACKET);
		        	    $this->setState(420);
		        	    $this->expression();
		        	    $this->setState(421);
		        	    $this->match(self::DOTDOT);
		        	    $this->setState(422);
		        	    $this->expression();
		        	    $this->setState(423);
		        	    $this->match(self::RBRACKET);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function addition(): Context\AdditionContext
		{
		    $localContext = new Context\AdditionContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 68, self::RULE_addition);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(427);
		        $this->multiplication();
		        $this->setState(432);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 38, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(428);

		        		$_la = $this->input->LA(1);

		        		if (!($_la === self::PLUS || $_la === self::MINUS)) {
		        		$this->errorHandler->recoverInline($this);
		        		} else {
		        			if ($this->input->LA(1) === Token::EOF) {
		        			    $this->matchedEOF = true;
		        		    }

		        			$this->errorHandler->reportMatch($this);
		        			$this->consume();
		        		}
		        		$this->setState(429);
		        		$this->multiplication(); 
		        	}

		        	$this->setState(434);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 38, $this->ctx);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function multiplication(): Context\MultiplicationContext
		{
		    $localContext = new Context\MultiplicationContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 70, self::RULE_multiplication);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(435);
		        $this->unary();
		        $this->setState(440);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 39, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(436);

		        		$_la = $this->input->LA(1);

		        		if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 3848290697216) !== 0))) {
		        		$this->errorHandler->recoverInline($this);
		        		} else {
		        			if ($this->input->LA(1) === Token::EOF) {
		        			    $this->matchedEOF = true;
		        		    }

		        			$this->errorHandler->reportMatch($this);
		        			$this->consume();
		        		}
		        		$this->setState(437);
		        		$this->unary(); 
		        	}

		        	$this->setState(442);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 39, $this->ctx);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function unary(): Context\UnaryContext
		{
		    $localContext = new Context\UnaryContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 72, self::RULE_unary);

		    try {
		        $this->setState(446);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 40, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(443);

		        	    $_la = $this->input->LA(1);

		        	    if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 2257022493917184) !== 0))) {
		        	    $this->errorHandler->recoverInline($this);
		        	    } else {
		        	    	if ($this->input->LA(1) === Token::EOF) {
		        	    	    $this->matchedEOF = true;
		        	        }

		        	    	$this->errorHandler->reportMatch($this);
		        	    	$this->consume();
		        	    }
		        	    $this->setState(444);
		        	    $this->unary();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(445);
		        	    $this->recursivePrimaryExpr(0);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function primaryExpr(): Context\PrimaryExprContext
		{
			return $this->recursivePrimaryExpr(0);
		}

		/**
		 * @throws RecognitionException
		 */
		private function recursivePrimaryExpr(int $precedence): Context\PrimaryExprContext
		{
			$parentContext = $this->ctx;
			$parentState = $this->getState();
			$localContext = new Context\PrimaryExprContext($this->ctx, $parentState);
			$previousContext = $localContext;
			$startState = 74;
			$this->enterRecursionRule($localContext, 74, self::RULE_primaryExpr, $precedence);

			try {
				$this->enterOuterAlt($localContext, 1);
				$this->setState(452);
				$this->errorHandler->sync($this);

				switch ($this->getInterpreter()->adaptivePredict($this->input, 41, $this->ctx)) {
					case 1:
					    $this->setState(449);
					    $this->functionCall();
					break;

					case 2:
					    $this->setState(450);
					    $this->builtinCall();
					break;

					case 3:
					    $this->setState(451);
					    $this->operand();
					break;
				}
				$this->ctx->stop = $this->input->LT(-1);
				$this->setState(461);
				$this->errorHandler->sync($this);

				$alt = $this->getInterpreter()->adaptivePredict($this->input, 42, $this->ctx);

				while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
					if ($alt === 1) {
						if ($this->getParseListeners() !== null) {
						    $this->triggerExitRuleEvent();
						}

						$previousContext = $localContext;
						$localContext = new Context\PrimaryExprContext($parentContext, $parentState);
						$this->pushNewRecursionContext($localContext, $startState, self::RULE_primaryExpr);
						$this->setState(454);

						if (!($this->precpred($this->ctx, 2))) {
						    throw new FailedPredicateException($this, "\\\$this->precpred(\\\$this->ctx, 2)");
						}
						$this->setState(455);
						$this->match(self::LBRACKET);
						$this->setState(456);
						$this->expression();
						$this->setState(457);
						$this->match(self::RBRACKET); 
					}

					$this->setState(463);
					$this->errorHandler->sync($this);

					$alt = $this->getInterpreter()->adaptivePredict($this->input, 42, $this->ctx);
				}
			} catch (RecognitionException $exception) {
				$localContext->exception = $exception;
				$this->errorHandler->reportError($this, $exception);
				$this->errorHandler->recover($this, $exception);
			} finally {
				$this->unrollRecursionContexts($parentContext);
			}

			return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function operand(): Context\OperandContext
		{
		    $localContext = new Context\OperandContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 76, self::RULE_operand);

		    try {
		        $this->setState(475);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::TRUE:
		            case self::FALSE:
		            case self::LBRACKET:
		            case self::INT_LIT:
		            case self::FLOAT_LIT:
		            case self::RUNE_LIT:
		            case self::STRING_LIT:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(464);
		            	$this->literal();
		            	break;

		            case self::ID:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(465);
		            	$this->match(self::ID);
		            	break;

		            case self::AMP:
		            	$this->enterOuterAlt($localContext, 3);
		            	$this->setState(466);
		            	$this->match(self::AMP);
		            	$this->setState(467);
		            	$this->match(self::ID);
		            	break;

		            case self::STAR:
		            	$this->enterOuterAlt($localContext, 4);
		            	$this->setState(468);
		            	$this->match(self::STAR);
		            	$this->setState(469);
		            	$this->match(self::ID);
		            	break;

		            case self::LPAREN:
		            	$this->enterOuterAlt($localContext, 5);
		            	$this->setState(470);
		            	$this->match(self::LPAREN);
		            	$this->setState(471);
		            	$this->expression();
		            	$this->setState(472);
		            	$this->match(self::RPAREN);
		            	break;

		            case self::NIL:
		            	$this->enterOuterAlt($localContext, 6);
		            	$this->setState(474);
		            	$this->match(self::NIL);
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function literal(): Context\LiteralContext
		{
		    $localContext = new Context\LiteralContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 78, self::RULE_literal);

		    try {
		        $this->setState(484);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::INT_LIT:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(477);
		            	$this->match(self::INT_LIT);
		            	break;

		            case self::FLOAT_LIT:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(478);
		            	$this->match(self::FLOAT_LIT);
		            	break;

		            case self::RUNE_LIT:
		            	$this->enterOuterAlt($localContext, 3);
		            	$this->setState(479);
		            	$this->match(self::RUNE_LIT);
		            	break;

		            case self::STRING_LIT:
		            	$this->enterOuterAlt($localContext, 4);
		            	$this->setState(480);
		            	$this->match(self::STRING_LIT);
		            	break;

		            case self::TRUE:
		            	$this->enterOuterAlt($localContext, 5);
		            	$this->setState(481);
		            	$this->match(self::TRUE);
		            	break;

		            case self::FALSE:
		            	$this->enterOuterAlt($localContext, 6);
		            	$this->setState(482);
		            	$this->match(self::FALSE);
		            	break;

		            case self::LBRACKET:
		            	$this->enterOuterAlt($localContext, 7);
		            	$this->setState(483);
		            	$this->arrayLiteral();
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function arrayLiteral(): Context\ArrayLiteralContext
		{
		    $localContext = new Context\ArrayLiteralContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 80, self::RULE_arrayLiteral);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(486);
		        $this->arrayTypeLiteral();
		        $this->setState(487);
		        $this->match(self::LBRACE);
		        $this->setState(488);
		        $this->arrayElements();
		        $this->setState(489);
		        $this->match(self::RBRACE);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function arrayElements(): Context\ArrayElementsContext
		{
		    $localContext = new Context\ArrayElementsContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 82, self::RULE_arrayElements);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(491);
		        $this->arrayElement();
		        $this->setState(496);
		        $this->errorHandler->sync($this);

		        $alt = $this->getInterpreter()->adaptivePredict($this->input, 45, $this->ctx);

		        while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER) {
		        	if ($alt === 1) {
		        		$this->setState(492);
		        		$this->match(self::COMMA);
		        		$this->setState(493);
		        		$this->arrayElement(); 
		        	}

		        	$this->setState(498);
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 45, $this->ctx);
		        }
		        $this->setState(500);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ($_la === self::COMMA) {
		        	$this->setState(499);
		        	$this->match(self::COMMA);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function arrayElement(): Context\ArrayElementContext
		{
		    $localContext = new Context\ArrayElementContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 84, self::RULE_arrayElement);

		    try {
		        $this->setState(507);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::NIL:
		            case self::TRUE:
		            case self::FALSE:
		            case self::INT32:
		            case self::FLOAT32:
		            case self::BOOL:
		            case self::RUNE:
		            case self::STRING:
		            case self::FMT:
		            case self::LEN:
		            case self::NOW:
		            case self::SUBSTR:
		            case self::TYPEOF:
		            case self::MINUS:
		            case self::STAR:
		            case self::AMP:
		            case self::NOT:
		            case self::LPAREN:
		            case self::LBRACKET:
		            case self::INT_LIT:
		            case self::FLOAT_LIT:
		            case self::RUNE_LIT:
		            case self::STRING_LIT:
		            case self::ID:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(502);
		            	$this->expression();
		            	break;

		            case self::LBRACE:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(503);
		            	$this->match(self::LBRACE);
		            	$this->setState(504);
		            	$this->arrayElements();
		            	$this->setState(505);
		            	$this->match(self::RBRACE);
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function typeLiteral(): Context\TypeLiteralContext
		{
		    $localContext = new Context\TypeLiteralContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 86, self::RULE_typeLiteral);

		    try {
		        $this->setState(513);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::INT32:
		            case self::FLOAT32:
		            case self::BOOL:
		            case self::RUNE:
		            case self::STRING:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(509);
		            	$this->baseType();
		            	break;

		            case self::STAR:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(510);
		            	$this->match(self::STAR);
		            	$this->setState(511);
		            	$this->typeLiteral();
		            	break;

		            case self::LBRACKET:
		            	$this->enterOuterAlt($localContext, 3);
		            	$this->setState(512);
		            	$this->arrayTypeLiteral();
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function baseType(): Context\BaseTypeContext
		{
		    $localContext = new Context\BaseTypeContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 88, self::RULE_baseType);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(515);

		        $_la = $this->input->LA(1);

		        if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 8126464) !== 0))) {
		        $this->errorHandler->recoverInline($this);
		        } else {
		        	if ($this->input->LA(1) === Token::EOF) {
		        	    $this->matchedEOF = true;
		            }

		        	$this->errorHandler->reportMatch($this);
		        	$this->consume();
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function arrayTypeLiteral(): Context\ArrayTypeLiteralContext
		{
		    $localContext = new Context\ArrayTypeLiteralContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 90, self::RULE_arrayTypeLiteral);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(521); 
		        $this->errorHandler->sync($this);

		        $alt = 1;

		        do {
		        	switch ($alt) {
		        	case 1:
		        		$this->setState(517);
		        		$this->match(self::LBRACKET);
		        		$this->setState(518);
		        		$this->expression();
		        		$this->setState(519);
		        		$this->match(self::RBRACKET);
		        		break;
		        	default:
		        		throw new NoViableAltException($this);
		        	}

		        	$this->setState(523); 
		        	$this->errorHandler->sync($this);

		        	$alt = $this->getInterpreter()->adaptivePredict($this->input, 49, $this->ctx);
		        } while ($alt !== 2 && $alt !== ATN::INVALID_ALT_NUMBER);
		        $this->setState(525);
		        $this->typeLiteral();
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function functionCall(): Context\FunctionCallContext
		{
		    $localContext = new Context\FunctionCallContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 92, self::RULE_functionCall);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(527);
		        $this->match(self::ID);
		        $this->setState(528);
		        $this->match(self::LPAREN);
		        $this->setState(530);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ((((($_la - 15)) & ~0x3f) === 0 && ((1 << ($_la - 15)) & 8728129619115519) !== 0)) {
		        	$this->setState(529);
		        	$this->argumentList();
		        }
		        $this->setState(532);
		        $this->match(self::RPAREN);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function argumentList(): Context\ArgumentListContext
		{
		    $localContext = new Context\ArgumentListContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 94, self::RULE_argumentList);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(534);
		        $this->argument();
		        $this->setState(539);
		        $this->errorHandler->sync($this);

		        $_la = $this->input->LA(1);
		        while ($_la === self::COMMA) {
		        	$this->setState(535);
		        	$this->match(self::COMMA);
		        	$this->setState(536);
		        	$this->argument();
		        	$this->setState(541);
		        	$this->errorHandler->sync($this);
		        	$_la = $this->input->LA(1);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function argument(): Context\ArgumentContext
		{
		    $localContext = new Context\ArgumentContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 96, self::RULE_argument);

		    try {
		        $this->setState(545);
		        $this->errorHandler->sync($this);

		        switch ($this->getInterpreter()->adaptivePredict($this->input, 52, $this->ctx)) {
		        	case 1:
		        	    $this->enterOuterAlt($localContext, 1);
		        	    $this->setState(542);
		        	    $this->expression();
		        	break;

		        	case 2:
		        	    $this->enterOuterAlt($localContext, 2);
		        	    $this->setState(543);
		        	    $this->match(self::AMP);
		        	    $this->setState(544);
		        	    $this->match(self::ID);
		        	break;
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function builtinCall(): Context\BuiltinCallContext
		{
		    $localContext = new Context\BuiltinCallContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 98, self::RULE_builtinCall);

		    try {
		        $this->setState(553);
		        $this->errorHandler->sync($this);

		        switch ($this->input->LA(1)) {
		            case self::FMT:
		            	$this->enterOuterAlt($localContext, 1);
		            	$this->setState(547);
		            	$this->fmtPrintln();
		            	break;

		            case self::LEN:
		            	$this->enterOuterAlt($localContext, 2);
		            	$this->setState(548);
		            	$this->lenCall();
		            	break;

		            case self::NOW:
		            	$this->enterOuterAlt($localContext, 3);
		            	$this->setState(549);
		            	$this->nowCall();
		            	break;

		            case self::SUBSTR:
		            	$this->enterOuterAlt($localContext, 4);
		            	$this->setState(550);
		            	$this->substrCall();
		            	break;

		            case self::TYPEOF:
		            	$this->enterOuterAlt($localContext, 5);
		            	$this->setState(551);
		            	$this->typeOfCall();
		            	break;

		            case self::INT32:
		            case self::FLOAT32:
		            case self::BOOL:
		            case self::RUNE:
		            case self::STRING:
		            	$this->enterOuterAlt($localContext, 6);
		            	$this->setState(552);
		            	$this->castCall();
		            	break;

		        default:
		        	throw new NoViableAltException($this);
		        }
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function fmtPrintln(): Context\FmtPrintlnContext
		{
		    $localContext = new Context\FmtPrintlnContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 100, self::RULE_fmtPrintln);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(555);
		        $this->match(self::FMT);
		        $this->setState(556);
		        $this->match(self::PUNTO);
		        $this->setState(557);
		        $this->match(self::PRINTLN);
		        $this->setState(558);
		        $this->match(self::LPAREN);
		        $this->setState(560);
		        $this->errorHandler->sync($this);
		        $_la = $this->input->LA(1);

		        if ((((($_la - 15)) & ~0x3f) === 0 && ((1 << ($_la - 15)) & 8728129619115519) !== 0)) {
		        	$this->setState(559);
		        	$this->argumentList();
		        }
		        $this->setState(562);
		        $this->match(self::RPAREN);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function lenCall(): Context\LenCallContext
		{
		    $localContext = new Context\LenCallContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 102, self::RULE_lenCall);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(564);
		        $this->match(self::LEN);
		        $this->setState(565);
		        $this->match(self::LPAREN);
		        $this->setState(566);
		        $this->expression();
		        $this->setState(567);
		        $this->match(self::RPAREN);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function nowCall(): Context\NowCallContext
		{
		    $localContext = new Context\NowCallContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 104, self::RULE_nowCall);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(569);
		        $this->match(self::NOW);
		        $this->setState(570);
		        $this->match(self::LPAREN);
		        $this->setState(571);
		        $this->match(self::RPAREN);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function substrCall(): Context\SubstrCallContext
		{
		    $localContext = new Context\SubstrCallContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 106, self::RULE_substrCall);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(573);
		        $this->match(self::SUBSTR);
		        $this->setState(574);
		        $this->match(self::LPAREN);
		        $this->setState(575);
		        $this->expression();
		        $this->setState(576);
		        $this->match(self::COMMA);
		        $this->setState(577);
		        $this->expression();
		        $this->setState(578);
		        $this->match(self::COMMA);
		        $this->setState(579);
		        $this->expression();
		        $this->setState(580);
		        $this->match(self::RPAREN);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function typeOfCall(): Context\TypeOfCallContext
		{
		    $localContext = new Context\TypeOfCallContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 108, self::RULE_typeOfCall);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(582);
		        $this->match(self::TYPEOF);
		        $this->setState(583);
		        $this->match(self::LPAREN);
		        $this->setState(584);
		        $this->expression();
		        $this->setState(585);
		        $this->match(self::RPAREN);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		/**
		 * @throws RecognitionException
		 */
		public function castCall(): Context\CastCallContext
		{
		    $localContext = new Context\CastCallContext($this->ctx, $this->getState());

		    $this->enterRule($localContext, 110, self::RULE_castCall);

		    try {
		        $this->enterOuterAlt($localContext, 1);
		        $this->setState(587);

		        $_la = $this->input->LA(1);

		        if (!(((($_la) & ~0x3f) === 0 && ((1 << $_la) & 8126464) !== 0))) {
		        $this->errorHandler->recoverInline($this);
		        } else {
		        	if ($this->input->LA(1) === Token::EOF) {
		        	    $this->matchedEOF = true;
		            }

		        	$this->errorHandler->reportMatch($this);
		        	$this->consume();
		        }
		        $this->setState(588);
		        $this->match(self::LPAREN);
		        $this->setState(589);
		        $this->expression();
		        $this->setState(590);
		        $this->match(self::RPAREN);
		    } catch (RecognitionException $exception) {
		        $localContext->exception = $exception;
		        $this->errorHandler->reportError($this, $exception);
		        $this->errorHandler->recover($this, $exception);
		    } finally {
		        $this->exitRule();
		    }

		    return $localContext;
		}

		public function sempred(?RuleContext $localContext, int $ruleIndex, int $predicateIndex): bool
		{
			switch ($ruleIndex) {
					case 37:
						return $this->sempredPrimaryExpr($localContext, $predicateIndex);

				default:
					return true;
				}
		}

		private function sempredPrimaryExpr(?Context\PrimaryExprContext $localContext, int $predicateIndex): bool
		{
			switch ($predicateIndex) {
			    case 0:
			        return $this->precpred($this->ctx, 2);
			}

			return true;
		}
	}
}

namespace Context {
	use Antlr\Antlr4\Runtime\ParserRuleContext;
	use Antlr\Antlr4\Runtime\Token;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
	use Antlr\Antlr4\Runtime\Tree\TerminalNode;
	use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
	use GrammarParser;
	use GrammarVisitor;
	use GrammarListener;

	class ProgramContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_program;
	    }

	    public function EOF(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::EOF, 0);
	    }

	    /**
	     * @return array<GlobalDeclarationContext>|GlobalDeclarationContext|null
	     */
	    public function globalDeclaration(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(GlobalDeclarationContext::class);
	    	}

	        return $this->getTypedRuleContext(GlobalDeclarationContext::class, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterProgram($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitProgram($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitProgram($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class GlobalDeclarationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_globalDeclaration;
	    }

	    public function funcDecl(): ?FuncDeclContext
	    {
	    	return $this->getTypedRuleContext(FuncDeclContext::class, 0);
	    }

	    public function varDecl(): ?VarDeclContext
	    {
	    	return $this->getTypedRuleContext(VarDeclContext::class, 0);
	    }

	    public function constDecl(): ?ConstDeclContext
	    {
	    	return $this->getTypedRuleContext(ConstDeclContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterGlobalDeclaration($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitGlobalDeclaration($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitGlobalDeclaration($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class FuncDeclContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_funcDecl;
	    }

	    public function FUNC(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::FUNC, 0);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ID, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

	    public function paramList(): ?ParamListContext
	    {
	    	return $this->getTypedRuleContext(ParamListContext::class, 0);
	    }

	    public function returnType(): ?ReturnTypeContext
	    {
	    	return $this->getTypedRuleContext(ReturnTypeContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterFuncDecl($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitFuncDecl($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitFuncDecl($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ParamListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_paramList;
	    }

	    /**
	     * @return array<ParamGroupContext>|ParamGroupContext|null
	     */
	    public function paramGroup(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ParamGroupContext::class);
	    	}

	        return $this->getTypedRuleContext(ParamGroupContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterParamList($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitParamList($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitParamList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ParamGroupContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_paramGroup;
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function ID(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::ID);
	    	}

	        return $this->getToken(GrammarParser::ID, $index);
	    }

	    public function typeLiteral(): ?TypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(TypeLiteralContext::class, 0);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function STAR(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::STAR);
	    	}

	        return $this->getToken(GrammarParser::STAR, $index);
	    }

	    public function LBRACKET(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LBRACKET, 0);
	    }

	    public function INT_LIT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::INT_LIT, 0);
	    }

	    public function RBRACKET(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RBRACKET, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterParamGroup($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitParamGroup($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitParamGroup($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ReturnTypeContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_returnType;
	    }

	    /**
	     * @return array<TypeLiteralContext>|TypeLiteralContext|null
	     */
	    public function typeLiteral(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(TypeLiteralContext::class);
	    	}

	        return $this->getTypedRuleContext(TypeLiteralContext::class, $index);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterReturnType($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitReturnType($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitReturnType($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class BlockContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_block;
	    }

	    public function LBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LBRACE, 0);
	    }

	    public function RBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RBRACE, 0);
	    }

	    /**
	     * @return array<StatementContext>|StatementContext|null
	     */
	    public function statement(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(StatementContext::class);
	    	}

	        return $this->getTypedRuleContext(StatementContext::class, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterBlock($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitBlock($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitBlock($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class StatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_statement;
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

	    public function varDecl(): ?VarDeclContext
	    {
	    	return $this->getTypedRuleContext(VarDeclContext::class, 0);
	    }

	    public function constDecl(): ?ConstDeclContext
	    {
	    	return $this->getTypedRuleContext(ConstDeclContext::class, 0);
	    }

	    public function shortVarDecl(): ?ShortVarDeclContext
	    {
	    	return $this->getTypedRuleContext(ShortVarDeclContext::class, 0);
	    }

	    public function assignStmt(): ?AssignStmtContext
	    {
	    	return $this->getTypedRuleContext(AssignStmtContext::class, 0);
	    }

	    public function incDecStmt(): ?IncDecStmtContext
	    {
	    	return $this->getTypedRuleContext(IncDecStmtContext::class, 0);
	    }

	    public function ifStmt(): ?IfStmtContext
	    {
	    	return $this->getTypedRuleContext(IfStmtContext::class, 0);
	    }

	    public function forStmt(): ?ForStmtContext
	    {
	    	return $this->getTypedRuleContext(ForStmtContext::class, 0);
	    }

	    public function switchStatement(): ?SwitchStatementContext
	    {
	    	return $this->getTypedRuleContext(SwitchStatementContext::class, 0);
	    }

	    public function returnStatement(): ?ReturnStatementContext
	    {
	    	return $this->getTypedRuleContext(ReturnStatementContext::class, 0);
	    }

	    public function breakStmt(): ?BreakStmtContext
	    {
	    	return $this->getTypedRuleContext(BreakStmtContext::class, 0);
	    }

	    public function continueStmt(): ?ContinueStmtContext
	    {
	    	return $this->getTypedRuleContext(ContinueStmtContext::class, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterStatement($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitStatement($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class VarDeclContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_varDecl;
	    }

	    public function VAR(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::VAR, 0);
	    }

	    public function idList(): ?IdListContext
	    {
	    	return $this->getTypedRuleContext(IdListContext::class, 0);
	    }

	    public function arrayTypeLiteral(): ?ArrayTypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(ArrayTypeLiteralContext::class, 0);
	    }

	    public function ASSIGN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ASSIGN, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

	    public function typeLiteral(): ?TypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(TypeLiteralContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterVarDecl($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitVarDecl($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitVarDecl($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ShortVarDeclContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_shortVarDecl;
	    }

	    public function idList(): ?IdListContext
	    {
	    	return $this->getTypedRuleContext(IdListContext::class, 0);
	    }

	    public function DEFINE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::DEFINE, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterShortVarDecl($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitShortVarDecl($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitShortVarDecl($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ConstDeclContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_constDecl;
	    }

	    public function CONST(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::CONST, 0);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ID, 0);
	    }

	    public function typeLiteral(): ?TypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(TypeLiteralContext::class, 0);
	    }

	    public function ASSIGN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ASSIGN, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterConstDecl($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitConstDecl($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitConstDecl($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class AssignStmtContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_assignStmt;
	    }

	    public function lValueList(): ?LValueListContext
	    {
	    	return $this->getTypedRuleContext(LValueListContext::class, 0);
	    }

	    public function assignOp(): ?AssignOpContext
	    {
	    	return $this->getTypedRuleContext(AssignOpContext::class, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterAssignStmt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitAssignStmt($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitAssignStmt($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LValueListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_lValueList;
	    }

	    /**
	     * @return array<LValueContext>|LValueContext|null
	     */
	    public function lValue(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(LValueContext::class);
	    	}

	        return $this->getTypedRuleContext(LValueContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterLValueList($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitLValueList($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitLValueList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LValueContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_lValue;
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ID, 0);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function LBRACKET(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::LBRACKET);
	    	}

	        return $this->getToken(GrammarParser::LBRACKET, $index);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function RBRACKET(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::RBRACKET);
	    	}

	        return $this->getToken(GrammarParser::RBRACKET, $index);
	    }

	    public function STAR(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::STAR, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterLValue($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitLValue($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitLValue($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class AssignOpContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_assignOp;
	    }

	    public function ASSIGN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ASSIGN, 0);
	    }

	    public function ADD_ASSIGN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ADD_ASSIGN, 0);
	    }

	    public function SUB_ASSIGN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::SUB_ASSIGN, 0);
	    }

	    public function MUL_ASSIGN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::MUL_ASSIGN, 0);
	    }

	    public function DIV_ASSIGN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::DIV_ASSIGN, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterAssignOp($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitAssignOp($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitAssignOp($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class IncDecStmtContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_incDecStmt;
	    }

	    public function lValue(): ?LValueContext
	    {
	    	return $this->getTypedRuleContext(LValueContext::class, 0);
	    }

	    public function INC(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::INC, 0);
	    }

	    public function DEC(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::DEC, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterIncDecStmt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitIncDecStmt($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitIncDecStmt($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class IfStmtContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_ifStmt;
	    }

	    public function IF(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::IF, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    /**
	     * @return array<BlockContext>|BlockContext|null
	     */
	    public function block(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(BlockContext::class);
	    	}

	        return $this->getTypedRuleContext(BlockContext::class, $index);
	    }

	    public function ELSE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ELSE, 0);
	    }

	    public function ifStmt(): ?IfStmtContext
	    {
	    	return $this->getTypedRuleContext(IfStmtContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterIfStmt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitIfStmt($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitIfStmt($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForStmtContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_forStmt;
	    }

	    public function FOR(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::FOR, 0);
	    }

	    public function block(): ?BlockContext
	    {
	    	return $this->getTypedRuleContext(BlockContext::class, 0);
	    }

	    public function forHeader(): ?ForHeaderContext
	    {
	    	return $this->getTypedRuleContext(ForHeaderContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterForStmt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitForStmt($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitForStmt($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForHeaderContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_forHeader;
	    }

	    public function forInitialization(): ?ForInitializationContext
	    {
	    	return $this->getTypedRuleContext(ForInitializationContext::class, 0);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function PUNTOYCOM(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::PUNTOYCOM);
	    	}

	        return $this->getToken(GrammarParser::PUNTOYCOM, $index);
	    }

	    public function forPost(): ?ForPostContext
	    {
	    	return $this->getTypedRuleContext(ForPostContext::class, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterForHeader($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitForHeader($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitForHeader($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForInitializationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_forInitialization;
	    }

	    public function shortVarDecl(): ?ShortVarDeclContext
	    {
	    	return $this->getTypedRuleContext(ShortVarDeclContext::class, 0);
	    }

	    public function varDecl(): ?VarDeclContext
	    {
	    	return $this->getTypedRuleContext(VarDeclContext::class, 0);
	    }

	    public function assignStmt(): ?AssignStmtContext
	    {
	    	return $this->getTypedRuleContext(AssignStmtContext::class, 0);
	    }

	    public function incDecStmt(): ?IncDecStmtContext
	    {
	    	return $this->getTypedRuleContext(IncDecStmtContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterForInitialization($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitForInitialization($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitForInitialization($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ForPostContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_forPost;
	    }

	    public function assignStmt(): ?AssignStmtContext
	    {
	    	return $this->getTypedRuleContext(AssignStmtContext::class, 0);
	    }

	    public function incDecStmt(): ?IncDecStmtContext
	    {
	    	return $this->getTypedRuleContext(IncDecStmtContext::class, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterForPost($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitForPost($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitForPost($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class SwitchStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_switchStatement;
	    }

	    public function SWITCH(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::SWITCH, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function LBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LBRACE, 0);
	    }

	    public function RBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RBRACE, 0);
	    }

	    /**
	     * @return array<SwitchCaseContext>|SwitchCaseContext|null
	     */
	    public function switchCase(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(SwitchCaseContext::class);
	    	}

	        return $this->getTypedRuleContext(SwitchCaseContext::class, $index);
	    }

	    public function defaultCase(): ?DefaultCaseContext
	    {
	    	return $this->getTypedRuleContext(DefaultCaseContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterSwitchStatement($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitSwitchStatement($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitSwitchStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class SwitchCaseContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_switchCase;
	    }

	    public function CASE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::CASE, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

	    public function DOSPUNTOS(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::DOSPUNTOS, 0);
	    }

	    /**
	     * @return array<StatementContext>|StatementContext|null
	     */
	    public function statement(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(StatementContext::class);
	    	}

	        return $this->getTypedRuleContext(StatementContext::class, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterSwitchCase($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitSwitchCase($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitSwitchCase($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class DefaultCaseContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_defaultCase;
	    }

	    public function DEFAULT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::DEFAULT, 0);
	    }

	    public function DOSPUNTOS(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::DOSPUNTOS, 0);
	    }

	    /**
	     * @return array<StatementContext>|StatementContext|null
	     */
	    public function statement(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(StatementContext::class);
	    	}

	        return $this->getTypedRuleContext(StatementContext::class, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterDefaultCase($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitDefaultCase($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitDefaultCase($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ReturnStatementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_returnStatement;
	    }

	    public function RETURN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RETURN, 0);
	    }

	    public function expressionList(): ?ExpressionListContext
	    {
	    	return $this->getTypedRuleContext(ExpressionListContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterReturnStatement($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitReturnStatement($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitReturnStatement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class BreakStmtContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_breakStmt;
	    }

	    public function BREAK(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::BREAK, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterBreakStmt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitBreakStmt($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitBreakStmt($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ContinueStmtContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_continueStmt;
	    }

	    public function CONTINUE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::CONTINUE, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterContinueStmt($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitContinueStmt($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitContinueStmt($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ExpressionListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_expressionList;
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterExpressionList($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitExpressionList($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitExpressionList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class IdListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_idList;
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function ID(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::ID);
	    	}

	        return $this->getToken(GrammarParser::ID, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterIdList($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitIdList($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitIdList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ExpressionContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_expression;
	    }

	    public function logicalOr(): ?LogicalOrContext
	    {
	    	return $this->getTypedRuleContext(LogicalOrContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterExpression($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitExpression($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitExpression($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LogicalOrContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_logicalOr;
	    }

	    /**
	     * @return array<LogicalAndContext>|LogicalAndContext|null
	     */
	    public function logicalAnd(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(LogicalAndContext::class);
	    	}

	        return $this->getTypedRuleContext(LogicalAndContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function OR(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::OR);
	    	}

	        return $this->getToken(GrammarParser::OR, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterLogicalOr($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitLogicalOr($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitLogicalOr($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LogicalAndContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_logicalAnd;
	    }

	    /**
	     * @return array<EqualityContext>|EqualityContext|null
	     */
	    public function equality(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(EqualityContext::class);
	    	}

	        return $this->getTypedRuleContext(EqualityContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function AND(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::AND);
	    	}

	        return $this->getToken(GrammarParser::AND, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterLogicalAnd($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitLogicalAnd($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitLogicalAnd($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class EqualityContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_equality;
	    }

	    /**
	     * @return array<ComparisonContext>|ComparisonContext|null
	     */
	    public function comparison(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ComparisonContext::class);
	    	}

	        return $this->getTypedRuleContext(ComparisonContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function EQ(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::EQ);
	    	}

	        return $this->getToken(GrammarParser::EQ, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function NEQ(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::NEQ);
	    	}

	        return $this->getToken(GrammarParser::NEQ, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterEquality($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitEquality($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitEquality($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ComparisonContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_comparison;
	    }

	    /**
	     * @return array<AdditionContext>|AdditionContext|null
	     */
	    public function addition(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(AdditionContext::class);
	    	}

	        return $this->getTypedRuleContext(AdditionContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function MENORQ(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::MENORQ);
	    	}

	        return $this->getToken(GrammarParser::MENORQ, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function MENORIGUAL(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::MENORIGUAL);
	    	}

	        return $this->getToken(GrammarParser::MENORIGUAL, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function MAYORQ(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::MAYORQ);
	    	}

	        return $this->getToken(GrammarParser::MAYORQ, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function MAYORIGUAL(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::MAYORIGUAL);
	    	}

	        return $this->getToken(GrammarParser::MAYORIGUAL, $index);
	    }

	    public function IN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::IN, 0);
	    }

	    public function LBRACKET(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LBRACKET, 0);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

	    public function DOTDOT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::DOTDOT, 0);
	    }

	    public function RBRACKET(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RBRACKET, 0);
	    }

	    public function NOT_KW(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::NOT_KW, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterComparison($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitComparison($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitComparison($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class AdditionContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_addition;
	    }

	    /**
	     * @return array<MultiplicationContext>|MultiplicationContext|null
	     */
	    public function multiplication(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(MultiplicationContext::class);
	    	}

	        return $this->getTypedRuleContext(MultiplicationContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function PLUS(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::PLUS);
	    	}

	        return $this->getToken(GrammarParser::PLUS, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function MINUS(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::MINUS);
	    	}

	        return $this->getToken(GrammarParser::MINUS, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterAddition($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitAddition($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitAddition($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class MultiplicationContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_multiplication;
	    }

	    /**
	     * @return array<UnaryContext>|UnaryContext|null
	     */
	    public function unary(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(UnaryContext::class);
	    	}

	        return $this->getTypedRuleContext(UnaryContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function STAR(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::STAR);
	    	}

	        return $this->getToken(GrammarParser::STAR, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function DIV(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::DIV);
	    	}

	        return $this->getToken(GrammarParser::DIV, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function MOD(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::MOD);
	    	}

	        return $this->getToken(GrammarParser::MOD, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterMultiplication($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitMultiplication($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitMultiplication($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class UnaryContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_unary;
	    }

	    public function unary(): ?UnaryContext
	    {
	    	return $this->getTypedRuleContext(UnaryContext::class, 0);
	    }

	    public function NOT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::NOT, 0);
	    }

	    public function MINUS(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::MINUS, 0);
	    }

	    public function STAR(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::STAR, 0);
	    }

	    public function AMP(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::AMP, 0);
	    }

	    public function primaryExpr(): ?PrimaryExprContext
	    {
	    	return $this->getTypedRuleContext(PrimaryExprContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterUnary($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitUnary($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitUnary($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class PrimaryExprContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_primaryExpr;
	    }

	    public function functionCall(): ?FunctionCallContext
	    {
	    	return $this->getTypedRuleContext(FunctionCallContext::class, 0);
	    }

	    public function builtinCall(): ?BuiltinCallContext
	    {
	    	return $this->getTypedRuleContext(BuiltinCallContext::class, 0);
	    }

	    public function operand(): ?OperandContext
	    {
	    	return $this->getTypedRuleContext(OperandContext::class, 0);
	    }

	    public function primaryExpr(): ?PrimaryExprContext
	    {
	    	return $this->getTypedRuleContext(PrimaryExprContext::class, 0);
	    }

	    public function LBRACKET(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LBRACKET, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function RBRACKET(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RBRACKET, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterPrimaryExpr($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitPrimaryExpr($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitPrimaryExpr($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class OperandContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_operand;
	    }

	    public function literal(): ?LiteralContext
	    {
	    	return $this->getTypedRuleContext(LiteralContext::class, 0);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ID, 0);
	    }

	    public function AMP(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::AMP, 0);
	    }

	    public function STAR(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::STAR, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

	    public function NIL(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::NIL, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterOperand($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitOperand($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitOperand($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LiteralContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_literal;
	    }

	    public function INT_LIT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::INT_LIT, 0);
	    }

	    public function FLOAT_LIT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::FLOAT_LIT, 0);
	    }

	    public function RUNE_LIT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RUNE_LIT, 0);
	    }

	    public function STRING_LIT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::STRING_LIT, 0);
	    }

	    public function TRUE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::TRUE, 0);
	    }

	    public function FALSE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::FALSE, 0);
	    }

	    public function arrayLiteral(): ?ArrayLiteralContext
	    {
	    	return $this->getTypedRuleContext(ArrayLiteralContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterLiteral($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitLiteral($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArrayLiteralContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_arrayLiteral;
	    }

	    public function arrayTypeLiteral(): ?ArrayTypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(ArrayTypeLiteralContext::class, 0);
	    }

	    public function LBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LBRACE, 0);
	    }

	    public function arrayElements(): ?ArrayElementsContext
	    {
	    	return $this->getTypedRuleContext(ArrayElementsContext::class, 0);
	    }

	    public function RBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RBRACE, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterArrayLiteral($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitArrayLiteral($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitArrayLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArrayElementsContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_arrayElements;
	    }

	    /**
	     * @return array<ArrayElementContext>|ArrayElementContext|null
	     */
	    public function arrayElement(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ArrayElementContext::class);
	    	}

	        return $this->getTypedRuleContext(ArrayElementContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterArrayElements($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitArrayElements($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitArrayElements($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArrayElementContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_arrayElement;
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function LBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LBRACE, 0);
	    }

	    public function arrayElements(): ?ArrayElementsContext
	    {
	    	return $this->getTypedRuleContext(ArrayElementsContext::class, 0);
	    }

	    public function RBRACE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RBRACE, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterArrayElement($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitArrayElement($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitArrayElement($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class TypeLiteralContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_typeLiteral;
	    }

	    public function baseType(): ?BaseTypeContext
	    {
	    	return $this->getTypedRuleContext(BaseTypeContext::class, 0);
	    }

	    public function STAR(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::STAR, 0);
	    }

	    public function typeLiteral(): ?TypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(TypeLiteralContext::class, 0);
	    }

	    public function arrayTypeLiteral(): ?ArrayTypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(ArrayTypeLiteralContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterTypeLiteral($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitTypeLiteral($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitTypeLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class BaseTypeContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_baseType;
	    }

	    public function INT32(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::INT32, 0);
	    }

	    public function FLOAT32(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::FLOAT32, 0);
	    }

	    public function BOOL(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::BOOL, 0);
	    }

	    public function RUNE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RUNE, 0);
	    }

	    public function STRING(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::STRING, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterBaseType($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitBaseType($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitBaseType($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArrayTypeLiteralContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_arrayTypeLiteral;
	    }

	    public function typeLiteral(): ?TypeLiteralContext
	    {
	    	return $this->getTypedRuleContext(TypeLiteralContext::class, 0);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function LBRACKET(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::LBRACKET);
	    	}

	        return $this->getToken(GrammarParser::LBRACKET, $index);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function RBRACKET(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::RBRACKET);
	    	}

	        return $this->getToken(GrammarParser::RBRACKET, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterArrayTypeLiteral($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitArrayTypeLiteral($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitArrayTypeLiteral($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class FunctionCallContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_functionCall;
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ID, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

	    public function argumentList(): ?ArgumentListContext
	    {
	    	return $this->getTypedRuleContext(ArgumentListContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterFunctionCall($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitFunctionCall($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitFunctionCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArgumentListContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_argumentList;
	    }

	    /**
	     * @return array<ArgumentContext>|ArgumentContext|null
	     */
	    public function argument(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ArgumentContext::class);
	    	}

	        return $this->getTypedRuleContext(ArgumentContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterArgumentList($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitArgumentList($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitArgumentList($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class ArgumentContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_argument;
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function AMP(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::AMP, 0);
	    }

	    public function ID(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::ID, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterArgument($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitArgument($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitArgument($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class BuiltinCallContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_builtinCall;
	    }

	    public function fmtPrintln(): ?FmtPrintlnContext
	    {
	    	return $this->getTypedRuleContext(FmtPrintlnContext::class, 0);
	    }

	    public function lenCall(): ?LenCallContext
	    {
	    	return $this->getTypedRuleContext(LenCallContext::class, 0);
	    }

	    public function nowCall(): ?NowCallContext
	    {
	    	return $this->getTypedRuleContext(NowCallContext::class, 0);
	    }

	    public function substrCall(): ?SubstrCallContext
	    {
	    	return $this->getTypedRuleContext(SubstrCallContext::class, 0);
	    }

	    public function typeOfCall(): ?TypeOfCallContext
	    {
	    	return $this->getTypedRuleContext(TypeOfCallContext::class, 0);
	    }

	    public function castCall(): ?CastCallContext
	    {
	    	return $this->getTypedRuleContext(CastCallContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterBuiltinCall($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitBuiltinCall($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitBuiltinCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class FmtPrintlnContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_fmtPrintln;
	    }

	    public function FMT(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::FMT, 0);
	    }

	    public function PUNTO(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::PUNTO, 0);
	    }

	    public function PRINTLN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::PRINTLN, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

	    public function argumentList(): ?ArgumentListContext
	    {
	    	return $this->getTypedRuleContext(ArgumentListContext::class, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterFmtPrintln($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitFmtPrintln($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitFmtPrintln($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class LenCallContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_lenCall;
	    }

	    public function LEN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LEN, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterLenCall($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitLenCall($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitLenCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class NowCallContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_nowCall;
	    }

	    public function NOW(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::NOW, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterNowCall($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitNowCall($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitNowCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class SubstrCallContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_substrCall;
	    }

	    public function SUBSTR(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::SUBSTR, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    /**
	     * @return array<ExpressionContext>|ExpressionContext|null
	     */
	    public function expression(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTypedRuleContexts(ExpressionContext::class);
	    	}

	        return $this->getTypedRuleContext(ExpressionContext::class, $index);
	    }

	    /**
	     * @return array<TerminalNode>|TerminalNode|null
	     */
	    public function COMMA(?int $index = null)
	    {
	    	if ($index === null) {
	    		return $this->getTokens(GrammarParser::COMMA);
	    	}

	        return $this->getToken(GrammarParser::COMMA, $index);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterSubstrCall($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitSubstrCall($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitSubstrCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class TypeOfCallContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_typeOfCall;
	    }

	    public function TYPEOF(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::TYPEOF, 0);
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterTypeOfCall($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitTypeOfCall($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitTypeOfCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 

	class CastCallContext extends ParserRuleContext
	{
		public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
		{
			parent::__construct($parent, $invokingState);
		}

		public function getRuleIndex(): int
		{
		    return GrammarParser::RULE_castCall;
	    }

	    public function LPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::LPAREN, 0);
	    }

	    public function expression(): ?ExpressionContext
	    {
	    	return $this->getTypedRuleContext(ExpressionContext::class, 0);
	    }

	    public function RPAREN(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RPAREN, 0);
	    }

	    public function INT32(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::INT32, 0);
	    }

	    public function FLOAT32(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::FLOAT32, 0);
	    }

	    public function BOOL(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::BOOL, 0);
	    }

	    public function STRING(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::STRING, 0);
	    }

	    public function RUNE(): ?TerminalNode
	    {
	        return $this->getToken(GrammarParser::RUNE, 0);
	    }

		public function enterRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->enterCastCall($this);
		    }
		}

		public function exitRule(ParseTreeListener $listener): void
		{
			if ($listener instanceof GrammarListener) {
			    $listener->exitCastCall($this);
		    }
		}

		public function accept(ParseTreeVisitor $visitor): mixed
		{
			if ($visitor instanceof GrammarVisitor) {
			    return $visitor->visitCastCall($this);
		    }

			return $visitor->visitChildren($this);
		}
	} 
}