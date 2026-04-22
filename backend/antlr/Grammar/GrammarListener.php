<?php

/*
 * Generated from Grammar/Grammar.g4 by ANTLR 4.13.1
 */

use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;

/**
 * This interface defines a complete listener for a parse tree produced by
 * {@see GrammarParser}.
 */
interface GrammarListener extends ParseTreeListener {
	/**
	 * Enter a parse tree produced by {@see GrammarParser::program()}.
	 * @param $context The parse tree.
	 */
	public function enterProgram(Context\ProgramContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::program()}.
	 * @param $context The parse tree.
	 */
	public function exitProgram(Context\ProgramContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::globalDeclaration()}.
	 * @param $context The parse tree.
	 */
	public function enterGlobalDeclaration(Context\GlobalDeclarationContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::globalDeclaration()}.
	 * @param $context The parse tree.
	 */
	public function exitGlobalDeclaration(Context\GlobalDeclarationContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::funcDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterFuncDecl(Context\FuncDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::funcDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitFuncDecl(Context\FuncDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::paramList()}.
	 * @param $context The parse tree.
	 */
	public function enterParamList(Context\ParamListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::paramList()}.
	 * @param $context The parse tree.
	 */
	public function exitParamList(Context\ParamListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::paramGroup()}.
	 * @param $context The parse tree.
	 */
	public function enterParamGroup(Context\ParamGroupContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::paramGroup()}.
	 * @param $context The parse tree.
	 */
	public function exitParamGroup(Context\ParamGroupContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::returnType()}.
	 * @param $context The parse tree.
	 */
	public function enterReturnType(Context\ReturnTypeContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::returnType()}.
	 * @param $context The parse tree.
	 */
	public function exitReturnType(Context\ReturnTypeContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::block()}.
	 * @param $context The parse tree.
	 */
	public function enterBlock(Context\BlockContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::block()}.
	 * @param $context The parse tree.
	 */
	public function exitBlock(Context\BlockContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::statement()}.
	 * @param $context The parse tree.
	 */
	public function enterStatement(Context\StatementContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::statement()}.
	 * @param $context The parse tree.
	 */
	public function exitStatement(Context\StatementContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::varDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterVarDecl(Context\VarDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::varDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitVarDecl(Context\VarDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::shortVarDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterShortVarDecl(Context\ShortVarDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::shortVarDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitShortVarDecl(Context\ShortVarDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::constDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterConstDecl(Context\ConstDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::constDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitConstDecl(Context\ConstDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::assignStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterAssignStmt(Context\AssignStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::assignStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitAssignStmt(Context\AssignStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::lValueList()}.
	 * @param $context The parse tree.
	 */
	public function enterLValueList(Context\LValueListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::lValueList()}.
	 * @param $context The parse tree.
	 */
	public function exitLValueList(Context\LValueListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::lValue()}.
	 * @param $context The parse tree.
	 */
	public function enterLValue(Context\LValueContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::lValue()}.
	 * @param $context The parse tree.
	 */
	public function exitLValue(Context\LValueContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::assignOp()}.
	 * @param $context The parse tree.
	 */
	public function enterAssignOp(Context\AssignOpContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::assignOp()}.
	 * @param $context The parse tree.
	 */
	public function exitAssignOp(Context\AssignOpContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::incDecStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterIncDecStmt(Context\IncDecStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::incDecStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitIncDecStmt(Context\IncDecStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::ifStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterIfStmt(Context\IfStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::ifStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitIfStmt(Context\IfStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::forStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterForStmt(Context\ForStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::forStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitForStmt(Context\ForStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::forHeader()}.
	 * @param $context The parse tree.
	 */
	public function enterForHeader(Context\ForHeaderContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::forHeader()}.
	 * @param $context The parse tree.
	 */
	public function exitForHeader(Context\ForHeaderContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::forInitialization()}.
	 * @param $context The parse tree.
	 */
	public function enterForInitialization(Context\ForInitializationContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::forInitialization()}.
	 * @param $context The parse tree.
	 */
	public function exitForInitialization(Context\ForInitializationContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::forPost()}.
	 * @param $context The parse tree.
	 */
	public function enterForPost(Context\ForPostContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::forPost()}.
	 * @param $context The parse tree.
	 */
	public function exitForPost(Context\ForPostContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::switchStatement()}.
	 * @param $context The parse tree.
	 */
	public function enterSwitchStatement(Context\SwitchStatementContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::switchStatement()}.
	 * @param $context The parse tree.
	 */
	public function exitSwitchStatement(Context\SwitchStatementContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::switchCase()}.
	 * @param $context The parse tree.
	 */
	public function enterSwitchCase(Context\SwitchCaseContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::switchCase()}.
	 * @param $context The parse tree.
	 */
	public function exitSwitchCase(Context\SwitchCaseContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::defaultCase()}.
	 * @param $context The parse tree.
	 */
	public function enterDefaultCase(Context\DefaultCaseContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::defaultCase()}.
	 * @param $context The parse tree.
	 */
	public function exitDefaultCase(Context\DefaultCaseContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::returnStatement()}.
	 * @param $context The parse tree.
	 */
	public function enterReturnStatement(Context\ReturnStatementContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::returnStatement()}.
	 * @param $context The parse tree.
	 */
	public function exitReturnStatement(Context\ReturnStatementContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::breakStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterBreakStmt(Context\BreakStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::breakStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitBreakStmt(Context\BreakStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::continueStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterContinueStmt(Context\ContinueStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::continueStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitContinueStmt(Context\ContinueStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::expressionList()}.
	 * @param $context The parse tree.
	 */
	public function enterExpressionList(Context\ExpressionListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::expressionList()}.
	 * @param $context The parse tree.
	 */
	public function exitExpressionList(Context\ExpressionListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::idList()}.
	 * @param $context The parse tree.
	 */
	public function enterIdList(Context\IdListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::idList()}.
	 * @param $context The parse tree.
	 */
	public function exitIdList(Context\IdListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function enterExpression(Context\ExpressionContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::expression()}.
	 * @param $context The parse tree.
	 */
	public function exitExpression(Context\ExpressionContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::logicalOr()}.
	 * @param $context The parse tree.
	 */
	public function enterLogicalOr(Context\LogicalOrContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::logicalOr()}.
	 * @param $context The parse tree.
	 */
	public function exitLogicalOr(Context\LogicalOrContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::logicalAnd()}.
	 * @param $context The parse tree.
	 */
	public function enterLogicalAnd(Context\LogicalAndContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::logicalAnd()}.
	 * @param $context The parse tree.
	 */
	public function exitLogicalAnd(Context\LogicalAndContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::equality()}.
	 * @param $context The parse tree.
	 */
	public function enterEquality(Context\EqualityContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::equality()}.
	 * @param $context The parse tree.
	 */
	public function exitEquality(Context\EqualityContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::comparison()}.
	 * @param $context The parse tree.
	 */
	public function enterComparison(Context\ComparisonContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::comparison()}.
	 * @param $context The parse tree.
	 */
	public function exitComparison(Context\ComparisonContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::addition()}.
	 * @param $context The parse tree.
	 */
	public function enterAddition(Context\AdditionContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::addition()}.
	 * @param $context The parse tree.
	 */
	public function exitAddition(Context\AdditionContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::multiplication()}.
	 * @param $context The parse tree.
	 */
	public function enterMultiplication(Context\MultiplicationContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::multiplication()}.
	 * @param $context The parse tree.
	 */
	public function exitMultiplication(Context\MultiplicationContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::unary()}.
	 * @param $context The parse tree.
	 */
	public function enterUnary(Context\UnaryContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::unary()}.
	 * @param $context The parse tree.
	 */
	public function exitUnary(Context\UnaryContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::primaryExpr()}.
	 * @param $context The parse tree.
	 */
	public function enterPrimaryExpr(Context\PrimaryExprContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::primaryExpr()}.
	 * @param $context The parse tree.
	 */
	public function exitPrimaryExpr(Context\PrimaryExprContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::operand()}.
	 * @param $context The parse tree.
	 */
	public function enterOperand(Context\OperandContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::operand()}.
	 * @param $context The parse tree.
	 */
	public function exitOperand(Context\OperandContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::literal()}.
	 * @param $context The parse tree.
	 */
	public function enterLiteral(Context\LiteralContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::literal()}.
	 * @param $context The parse tree.
	 */
	public function exitLiteral(Context\LiteralContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::arrayLiteral()}.
	 * @param $context The parse tree.
	 */
	public function enterArrayLiteral(Context\ArrayLiteralContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::arrayLiteral()}.
	 * @param $context The parse tree.
	 */
	public function exitArrayLiteral(Context\ArrayLiteralContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::arrayElements()}.
	 * @param $context The parse tree.
	 */
	public function enterArrayElements(Context\ArrayElementsContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::arrayElements()}.
	 * @param $context The parse tree.
	 */
	public function exitArrayElements(Context\ArrayElementsContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::arrayElement()}.
	 * @param $context The parse tree.
	 */
	public function enterArrayElement(Context\ArrayElementContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::arrayElement()}.
	 * @param $context The parse tree.
	 */
	public function exitArrayElement(Context\ArrayElementContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::typeLiteral()}.
	 * @param $context The parse tree.
	 */
	public function enterTypeLiteral(Context\TypeLiteralContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::typeLiteral()}.
	 * @param $context The parse tree.
	 */
	public function exitTypeLiteral(Context\TypeLiteralContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::baseType()}.
	 * @param $context The parse tree.
	 */
	public function enterBaseType(Context\BaseTypeContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::baseType()}.
	 * @param $context The parse tree.
	 */
	public function exitBaseType(Context\BaseTypeContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::arrayTypeLiteral()}.
	 * @param $context The parse tree.
	 */
	public function enterArrayTypeLiteral(Context\ArrayTypeLiteralContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::arrayTypeLiteral()}.
	 * @param $context The parse tree.
	 */
	public function exitArrayTypeLiteral(Context\ArrayTypeLiteralContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::functionCall()}.
	 * @param $context The parse tree.
	 */
	public function enterFunctionCall(Context\FunctionCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::functionCall()}.
	 * @param $context The parse tree.
	 */
	public function exitFunctionCall(Context\FunctionCallContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::argumentList()}.
	 * @param $context The parse tree.
	 */
	public function enterArgumentList(Context\ArgumentListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::argumentList()}.
	 * @param $context The parse tree.
	 */
	public function exitArgumentList(Context\ArgumentListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::argument()}.
	 * @param $context The parse tree.
	 */
	public function enterArgument(Context\ArgumentContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::argument()}.
	 * @param $context The parse tree.
	 */
	public function exitArgument(Context\ArgumentContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::builtinCall()}.
	 * @param $context The parse tree.
	 */
	public function enterBuiltinCall(Context\BuiltinCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::builtinCall()}.
	 * @param $context The parse tree.
	 */
	public function exitBuiltinCall(Context\BuiltinCallContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::fmtPrintln()}.
	 * @param $context The parse tree.
	 */
	public function enterFmtPrintln(Context\FmtPrintlnContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::fmtPrintln()}.
	 * @param $context The parse tree.
	 */
	public function exitFmtPrintln(Context\FmtPrintlnContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::lenCall()}.
	 * @param $context The parse tree.
	 */
	public function enterLenCall(Context\LenCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::lenCall()}.
	 * @param $context The parse tree.
	 */
	public function exitLenCall(Context\LenCallContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::nowCall()}.
	 * @param $context The parse tree.
	 */
	public function enterNowCall(Context\NowCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::nowCall()}.
	 * @param $context The parse tree.
	 */
	public function exitNowCall(Context\NowCallContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::substrCall()}.
	 * @param $context The parse tree.
	 */
	public function enterSubstrCall(Context\SubstrCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::substrCall()}.
	 * @param $context The parse tree.
	 */
	public function exitSubstrCall(Context\SubstrCallContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::typeOfCall()}.
	 * @param $context The parse tree.
	 */
	public function enterTypeOfCall(Context\TypeOfCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::typeOfCall()}.
	 * @param $context The parse tree.
	 */
	public function exitTypeOfCall(Context\TypeOfCallContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GrammarParser::castCall()}.
	 * @param $context The parse tree.
	 */
	public function enterCastCall(Context\CastCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GrammarParser::castCall()}.
	 * @param $context The parse tree.
	 */
	public function exitCastCall(Context\CastCallContext $context): void;
}