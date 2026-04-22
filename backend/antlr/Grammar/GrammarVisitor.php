<?php

/*
 * Generated from Grammar/Grammar.g4 by ANTLR 4.13.1
 */

use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;

/**
 * This interface defines a complete generic visitor for a parse tree produced by {@see GrammarParser}.
 */
interface GrammarVisitor extends ParseTreeVisitor
{
	/**
	 * Visit a parse tree produced by {@see GrammarParser::program()}.
	 *
	 * @param Context\ProgramContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitProgram(Context\ProgramContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::globalDeclaration()}.
	 *
	 * @param Context\GlobalDeclarationContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitGlobalDeclaration(Context\GlobalDeclarationContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::funcDecl()}.
	 *
	 * @param Context\FuncDeclContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFuncDecl(Context\FuncDeclContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::paramList()}.
	 *
	 * @param Context\ParamListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitParamList(Context\ParamListContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::paramGroup()}.
	 *
	 * @param Context\ParamGroupContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitParamGroup(Context\ParamGroupContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::returnType()}.
	 *
	 * @param Context\ReturnTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitReturnType(Context\ReturnTypeContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::block()}.
	 *
	 * @param Context\BlockContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBlock(Context\BlockContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::statement()}.
	 *
	 * @param Context\StatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitStatement(Context\StatementContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::varDecl()}.
	 *
	 * @param Context\VarDeclContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitVarDecl(Context\VarDeclContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::shortVarDecl()}.
	 *
	 * @param Context\ShortVarDeclContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitShortVarDecl(Context\ShortVarDeclContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::constDecl()}.
	 *
	 * @param Context\ConstDeclContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitConstDecl(Context\ConstDeclContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::assignStmt()}.
	 *
	 * @param Context\AssignStmtContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAssignStmt(Context\AssignStmtContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::lValueList()}.
	 *
	 * @param Context\LValueListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLValueList(Context\LValueListContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::lValue()}.
	 *
	 * @param Context\LValueContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLValue(Context\LValueContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::assignOp()}.
	 *
	 * @param Context\AssignOpContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAssignOp(Context\AssignOpContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::incDecStmt()}.
	 *
	 * @param Context\IncDecStmtContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIncDecStmt(Context\IncDecStmtContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::ifStmt()}.
	 *
	 * @param Context\IfStmtContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIfStmt(Context\IfStmtContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::forStmt()}.
	 *
	 * @param Context\ForStmtContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForStmt(Context\ForStmtContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::forHeader()}.
	 *
	 * @param Context\ForHeaderContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForHeader(Context\ForHeaderContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::forInitialization()}.
	 *
	 * @param Context\ForInitializationContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForInitialization(Context\ForInitializationContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::forPost()}.
	 *
	 * @param Context\ForPostContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitForPost(Context\ForPostContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::switchStatement()}.
	 *
	 * @param Context\SwitchStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitSwitchStatement(Context\SwitchStatementContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::switchCase()}.
	 *
	 * @param Context\SwitchCaseContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitSwitchCase(Context\SwitchCaseContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::defaultCase()}.
	 *
	 * @param Context\DefaultCaseContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitDefaultCase(Context\DefaultCaseContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::returnStatement()}.
	 *
	 * @param Context\ReturnStatementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitReturnStatement(Context\ReturnStatementContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::breakStmt()}.
	 *
	 * @param Context\BreakStmtContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBreakStmt(Context\BreakStmtContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::continueStmt()}.
	 *
	 * @param Context\ContinueStmtContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitContinueStmt(Context\ContinueStmtContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::expressionList()}.
	 *
	 * @param Context\ExpressionListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitExpressionList(Context\ExpressionListContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::idList()}.
	 *
	 * @param Context\IdListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitIdList(Context\IdListContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::expression()}.
	 *
	 * @param Context\ExpressionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitExpression(Context\ExpressionContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::logicalOr()}.
	 *
	 * @param Context\LogicalOrContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLogicalOr(Context\LogicalOrContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::logicalAnd()}.
	 *
	 * @param Context\LogicalAndContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLogicalAnd(Context\LogicalAndContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::equality()}.
	 *
	 * @param Context\EqualityContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitEquality(Context\EqualityContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::comparison()}.
	 *
	 * @param Context\ComparisonContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitComparison(Context\ComparisonContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::addition()}.
	 *
	 * @param Context\AdditionContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitAddition(Context\AdditionContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::multiplication()}.
	 *
	 * @param Context\MultiplicationContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitMultiplication(Context\MultiplicationContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::unary()}.
	 *
	 * @param Context\UnaryContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitUnary(Context\UnaryContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::primaryExpr()}.
	 *
	 * @param Context\PrimaryExprContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitPrimaryExpr(Context\PrimaryExprContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::operand()}.
	 *
	 * @param Context\OperandContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitOperand(Context\OperandContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::literal()}.
	 *
	 * @param Context\LiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLiteral(Context\LiteralContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::arrayLiteral()}.
	 *
	 * @param Context\ArrayLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayLiteral(Context\ArrayLiteralContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::arrayElements()}.
	 *
	 * @param Context\ArrayElementsContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayElements(Context\ArrayElementsContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::arrayElement()}.
	 *
	 * @param Context\ArrayElementContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayElement(Context\ArrayElementContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::typeLiteral()}.
	 *
	 * @param Context\TypeLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTypeLiteral(Context\TypeLiteralContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::baseType()}.
	 *
	 * @param Context\BaseTypeContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBaseType(Context\BaseTypeContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::arrayTypeLiteral()}.
	 *
	 * @param Context\ArrayTypeLiteralContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArrayTypeLiteral(Context\ArrayTypeLiteralContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::functionCall()}.
	 *
	 * @param Context\FunctionCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFunctionCall(Context\FunctionCallContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::argumentList()}.
	 *
	 * @param Context\ArgumentListContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArgumentList(Context\ArgumentListContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::argument()}.
	 *
	 * @param Context\ArgumentContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitArgument(Context\ArgumentContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::builtinCall()}.
	 *
	 * @param Context\BuiltinCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitBuiltinCall(Context\BuiltinCallContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::fmtPrintln()}.
	 *
	 * @param Context\FmtPrintlnContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitFmtPrintln(Context\FmtPrintlnContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::lenCall()}.
	 *
	 * @param Context\LenCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitLenCall(Context\LenCallContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::nowCall()}.
	 *
	 * @param Context\NowCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitNowCall(Context\NowCallContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::substrCall()}.
	 *
	 * @param Context\SubstrCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitSubstrCall(Context\SubstrCallContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::typeOfCall()}.
	 *
	 * @param Context\TypeOfCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitTypeOfCall(Context\TypeOfCallContext $context);

	/**
	 * Visit a parse tree produced by {@see GrammarParser::castCall()}.
	 *
	 * @param Context\CastCallContext $context The parse tree.
	 *
	 * @return mixed The visitor result.
	 */
	public function visitCastCall(Context\CastCallContext $context);
}